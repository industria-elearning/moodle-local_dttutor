<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External API for Tutor-IA chat message creation
 *
 * @package    local_dttutor
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dttutor\external;

use external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_dttutor\httpclient\tutoria_api;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
/**
 * Class create_chat_message
 *
 * Creates a chat message and returns streaming URL for Tutor-IA responses.
 *
 * @package    local_dttutor
 * @category   external
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class create_chat_message extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     * @since Moodle 4.5
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
            'message' => new external_value(PARAM_RAW, 'User message', VALUE_REQUIRED),
            'meta' => new external_value(PARAM_RAW, 'Optional metadata (JSON)', VALUE_DEFAULT, '{}'),
        ]);
    }

    /**
     * Create chat message and initialize Tutor-IA session.
     *
     * @param int $courseid Course ID.
     * @param string $message User message text.
     * @param string $meta Optional metadata as JSON string.
     * @return array Session data with streaming URL.
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     * @since Moodle 4.5
     */
    public static function execute($courseid, $message, $meta = '{}'): array {
        global $CFG, $USER;

        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'message' => $message,
            'meta' => $meta,
        ]);

        if (!get_config('local_dttutor', 'enabled')) {
            throw new \moodle_exception('error_api_not_configured', 'local_dttutor');
        }

        // Verify Datacurso AI Provider webservice is configured.
        if (!class_exists('\aiprovider_datacurso\webservice_config')) {
            throw new \moodle_exception('error_api_not_configured', 'local_dttutor');
        }

        if (!\aiprovider_datacurso\webservice_config::is_configured()) {
            // Check if user is admin to show different message.
            $syscontext = \context_system::instance();
            if (has_capability('moodle/site:config', $syscontext)) {
                // Admin user - show message with configuration link.
                $configurl = new \moodle_url('/ai/provider/datacurso/admin/webservice_config.php');
                throw new \moodle_exception('error_webservice_not_configured_admin', 'local_dttutor', '', $configurl->out());
            } else {
                // Regular user - show friendly message.
                throw new \moodle_exception('error_webservice_not_configured', 'local_dttutor');
            }
        }

        $context = \context_course::instance($params['courseid']);
        require_capability('moodle/course:view', $context);

        // Validate message content.
        $trimmedmessage = trim($params['message']);
        if (empty($trimmedmessage)) {
            throw new \moodle_exception('error_empty_message', 'local_dttutor');
        }
        if ($trimmedmessage === '.') {
            throw new \moodle_exception('error_invalid_message', 'local_dttutor');
        }

        $tutoriaapi = new tutoria_api();

        $metaarray = json_decode($params['meta'], true);
        if ($metaarray === null) {
            $metaarray = [];
        }

        $metaarray['userid'] = $USER->id;

        // Add off-topic detection settings to metadata.
        $metaarray['off_topic_detection_enabled'] = (bool)get_config('local_dttutor', 'off_topic_detection_enabled');
        $metaarray['off_topic_strictness'] = get_config('local_dttutor', 'off_topic_strictness') ?: 'permissive';

        // Add custom prompt to metadata if configured.
        $customprompt = get_config('local_dttutor', 'custom_prompt');
        if (!empty($customprompt)) {
            $metaarray['custom_prompt'] = $customprompt;
        }

        $session = $tutoriaapi->start_session($params['courseid']);

        if (!isset($session['ready']) || !$session['ready']) {
            throw new \moodle_exception('sessionnotready', 'local_dttutor');
        }

        $tutoriaapi->send_message($session['session_id'], $params['message'], $metaarray);

        $streamurl = $tutoriaapi->get_stream_url($session['session_id']);

        return [
            'session_id' => $session['session_id'],
            'stream_url' => $streamurl,
            'expires_at' => time() + ($session['session_ttl_seconds'] ?? 604800),
        ];
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     * @since Moodle 4.5
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'session_id' => new external_value(PARAM_TEXT, 'Tutor-IA session ID'),
            'stream_url' => new external_value(PARAM_URL, 'SSE streaming URL with authentication'),
            'expires_at' => new external_value(PARAM_INT, 'Session expiration timestamp'),
        ]);
    }
}
