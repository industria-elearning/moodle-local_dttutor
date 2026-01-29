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
 * External service to upload course material (PDF)
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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External service to upload course material (PDF)
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class upload_course_material extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 4.5
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
            'draftitemid' => new external_value(PARAM_INT, 'Draft item ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Upload course material
     *
     * @param int $courseid Course ID
     * @param int $draftitemid Draft item ID from file picker
     * @return array Upload status
     * @since Moodle 4.5
     */
    public static function execute(int $courseid, int $draftitemid): array {
        global $USER;

        // 1. Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'draftitemid' => $draftitemid,
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

        // 5. Save files from draft area to permanent file area.
        $fs = get_file_storage();

        // File manager options.
        $options = [
            'subdirs' => false,
            'maxfiles' => 50,
            'maxbytes' => 10485760, // 10 MB.
            'accepted_types' => ['application/pdf', '.pdf'],
        ];

        // Save files from draft area.
        file_save_draft_area_files(
            $params['draftitemid'],
            $context->id,
            'local_dttutor',
            'course_materials',
            0,
            $options
        );

        // Get uploaded files count.
        $files = $fs->get_area_files(
            $context->id,
            'local_dttutor',
            'course_materials',
            0,
            'filename',
            false
        );

        return [
            'success' => true,
            'message' => get_string('material_uploaded', 'local_dttutor'),
            'files_count' => count($files),
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
            'message' => new external_value(PARAM_TEXT, 'Status message'),
            'files_count' => new external_value(PARAM_INT, 'Total files count'),
        ]);
    }
}
