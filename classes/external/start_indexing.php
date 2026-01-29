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
 * External service to start indexing for a course
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dttutor\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use local_dttutor\httpclient\tutoria_api;
use local_dttutor\course_config;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External service to start indexing for a course
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class start_indexing extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 4.5
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
            'force_reindex' => new external_value(PARAM_BOOL, 'Force re-indexing', VALUE_DEFAULT, false),
        ]);
    }

    /**
     * Start indexing for a course
     *
     * @param int $courseid Course ID
     * @param bool $forcereindex Force re-indexing even if already indexed
     * @return array Response with task_id and status
     * @since Moodle 4.5
     */
    public static function execute(int $courseid, bool $forcereindex = false): array {
        // 1. Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'force_reindex' => $forcereindex,
        ]);

        // 2. Check authentication.
        require_login();

        // 3. Verify plugin is enabled.
        if (!get_config('local_dttutor', 'enabled')) {
            throw new \moodle_exception('error_api_not_configured', 'local_dttutor');
        }

        // 4. Validate course context and check capabilities.
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('moodle/course:update', $context);

        // 5. Call API to start indexing.
        $api = new tutoria_api();
        $result = $api->start_indexing($params['courseid'], $params['force_reindex']);

        // 6. Update local database status.
        if (isset($result['task_id'])) {
            course_config::update_indexing_status(
                $params['courseid'],
                $result['status'] === 'started' ? 'running' : $result['status'],
                $result['task_id']
            );
        } else if (isset($result['status']) && $result['status'] === 'already_indexed') {
            course_config::update_indexing_status(
                $params['courseid'],
                'completed',
                null
            );
        }

        return [
            'success' => true,
            'status' => $result['status'] ?? 'unknown',
            'task_id' => $result['task_id'] ?? null,
            'message' => $result['message'] ?? '',
        ];
    }

    /**
     * Returns description of method result value
     *
     * @return external_single_structure
     * @since Moodle 4.5
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'status' => new external_value(PARAM_TEXT, 'Indexing status'),
            'task_id' => new external_value(PARAM_TEXT, 'Task ID', VALUE_OPTIONAL),
            'message' => new external_value(PARAM_TEXT, 'Status message'),
        ]);
    }
}
