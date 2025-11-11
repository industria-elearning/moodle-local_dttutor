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
 * Custom admin setting for visual avatar selection
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dttutor\admin_settings;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Admin setting for visual avatar selection with previews
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_avatar_selector extends \admin_setting {

    /**
     * Return the current setting
     *
     * @return mixed
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Save the selected avatar
     *
     * @param mixed $data
     * @return string Empty string if ok, error message otherwise
     */
    public function write_setting($data) {
        return $this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin');
    }

    /**
     * Return HTML for the setting
     *
     * @param mixed $data
     * @param string $query
     * @return string HTML
     */
    public function output_html($data, $query = '') {
        global $OUTPUT, $CFG, $PAGE;

        $default = $this->get_defaultsetting();
        $current = $this->get_setting();
        if ($current === null) {
            $current = $default;
        }

        $html = '';

        $html .= '<link rel="stylesheet" href="' . $CFG->wwwroot . '/local/dttutor/styles_admin.css">';

        // Load AMD module properly via $PAGE.
        $PAGE->requires->js_call_amd('local_dttutor/avatar_selector', 'init');

        $html .= '<div class="avatar-selector-grid">';

        // Display all available avatars.
        for ($i = 1; $i <= 10; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $avatarpath = '/local/dttutor/pix/avatars/avatar_profesor_' . $num . '.png';
            $fullpath = $CFG->dirroot . $avatarpath;

            if (file_exists($fullpath)) {
                $selected = ($current === $num) ? 'selected' : '';
                $html .= '<div class="avatar-option ' . $selected . '" data-value="' . $num . '" ';
                $html .= 'onclick="selectDttutorAvatar(\'' . $num . '\')">';
                $html .= '<img src="' . $CFG->wwwroot . $avatarpath . '?v=' . time() . '" ';
                $html .= 'alt="Avatar ' . $i . '">';
                $html .= '<div class="avatar-option-label">Avatar ' . $i . '</div>';
                $html .= '<input type="radio" name="s_local_dttutor_avatar" value="' . $num . '"';
                if ($current === $num) {
                    $html .= ' checked="checked"';
                }
                $html .= '>';
                $html .= '</div>';
            }
        }

        $html .= '</div>';

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', $default, $query);
    }
}
