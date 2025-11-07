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
 * Custom admin setting for uploading custom avatar
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dttutor\admin_settings;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Admin setting for uploading custom avatar image
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_custom_avatar extends \admin_setting_configstoredfile {
    /**
     * Constructor
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     */
    public function __construct($name, $visiblename, $description) {
        parent::__construct(
            $name,
            $visiblename,
            $description,
            'customavatar',
            0,
            ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg']]
        );
    }

    /**
     * Return HTML for the setting
     *
     * @param mixed $data
     * @param string $query
     * @return string HTML
     */
    public function output_html($data, $query = '') {
        global $CFG, $OUTPUT;

        $html = parent::output_html($data, $query);

        // Add information about image dimensions.
        $info = \html_writer::div(
            get_string('customavatar_dimensions', 'local_dttutor'),
            'alert alert-info mt-2',
            ['style' => 'max-width: 600px;']
        );

        // Check if custom avatar exists and show preview.
        $fs = get_file_storage();
        $files = $fs->get_area_files(
            \context_system::instance()->id,
            'local_dttutor',
            'customavatar',
            0,
            'timemodified DESC',
            false
        );

        if (!empty($files)) {
            $file = reset($files);
            $url = \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );

            $preview = \html_writer::div(
                \html_writer::img($url, 'Custom avatar preview', ['style' => 'max-width: 150px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);']),
                'mt-2'
            );

            $html .= $preview;
        }

        $html .= $info;

        return $html;
    }
}
