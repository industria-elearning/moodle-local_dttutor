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
 * External service to get course materials for backend consumption
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
use core_external\external_multiple_structure;
use local_dttutor\course_config;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External service to get course materials for backend consumption
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_course_materials extends external_api {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 4.5
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Get course materials and configuration
     *
     * @param int $courseid Course ID
     * @return array Course materials and configuration
     * @since Moodle 4.5
     */
    public static function execute(int $courseid): array {
        global $CFG;

        // 1. Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
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
        require_capability('local/dttutor:use', $context);
        require_capability('moodle/course:view', $context);

        // 5. Get course configuration.
        $config = course_config::get_by_course($params['courseid']);

        // Get custom prompt (course-specific overrides global).
        $customprompt = $config->custom_prompt;
        if (empty($customprompt)) {
            $customprompt = get_config('local_dttutor', 'custom_prompt');
        }

        // 6. Get uploaded PDFs.
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            $context->id,
            'local_dttutor',
            'course_materials',
            0,
            'filename',
            false  // Exclude directories.
        );

        $materials = [];
        foreach ($files as $file) {
            // Generate download URL with token for external access.
            $downloadurl = \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename(),
                true // Force download.
            );

            $materials[] = [
                'filename' => $file->get_filename(),
                'filesize' => $file->get_filesize(),
                'mimetype' => $file->get_mimetype(),
                'timecreated' => $file->get_timecreated(),
                'download_url' => $downloadurl->out(false),
            ];
        }

        return [
            'custom_prompt' => $customprompt ?? '',
            'indexing_enabled' => (bool)$config->indexing_enabled,
            'materials' => $materials,
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
            'custom_prompt' => new external_value(PARAM_TEXT, 'Custom prompt'),
            'indexing_enabled' => new external_value(PARAM_BOOL, 'Indexing enabled'),
            'materials' => new external_multiple_structure(
                new external_single_structure([
                    'filename' => new external_value(PARAM_FILE, 'File name'),
                    'filesize' => new external_value(PARAM_INT, 'File size in bytes'),
                    'mimetype' => new external_value(PARAM_TEXT, 'MIME type'),
                    'timecreated' => new external_value(PARAM_INT, 'Time created'),
                    'download_url' => new external_value(PARAM_URL, 'Download URL'),
                ])
            ),
        ]);
    }
}
