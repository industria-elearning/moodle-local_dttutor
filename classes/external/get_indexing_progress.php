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
 * External service to get indexing progress for a task
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
 * External service to get indexing progress for a task
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_indexing_progress extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 4.5
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'task_id' => new external_value(PARAM_ALPHANUMEXT, 'Task ID', VALUE_REQUIRED),
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Get indexing progress for a task
     *
     * @param string $taskid Task ID
     * @param int $courseid Course ID
     * @return array Progress information
     * @since Moodle 4.5
     */
    public static function execute(string $taskid, int $courseid): array {
        // 1. Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'task_id' => $taskid,
            'courseid' => $courseid,
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

        // 5. Call API to get progress.
        $api = new tutoria_api();
        $result = $api->get_indexing_progress($params['task_id']);

        // 6. Update database if status has changed to a terminal state.
        $status = $result['status'] ?? 'running';
        $terminalstatuses = ['completed', 'failed', 'cancelled', 'interrupted'];

        if (in_array($status, $terminalstatuses)) {
            course_config::update_indexing_status(
                $params['courseid'],
                $status,
                $params['task_id'],
                $result['error'] ?? null
            );
        }

        return [
            'status' => $status,
            'current_phase' => $result['current_phase'] ?? '',
            'phase_details' => $result['phase_details'] ?? '',
            'overall_percent' => $result['overall_percent'] ?? 0,
            'items_processed' => $result['items_processed'] ?? 0,
            'items_total' => $result['items_total'] ?? 0,
            'error' => $result['error'] ?? null,
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
            'status' => new external_value(PARAM_TEXT, 'Status'),
            'current_phase' => new external_value(PARAM_TEXT, 'Current phase name'),
            'phase_details' => new external_value(PARAM_TEXT, 'Phase details'),
            'overall_percent' => new external_value(PARAM_FLOAT, 'Overall progress percentage'),
            'items_processed' => new external_value(PARAM_INT, 'Items processed'),
            'items_total' => new external_value(PARAM_INT, 'Total items'),
            'error' => new external_value(PARAM_TEXT, 'Error message', VALUE_OPTIONAL),
        ]);
    }
}
