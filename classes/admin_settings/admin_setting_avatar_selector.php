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
     * Constructor
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

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
        global $OUTPUT, $CFG;

        $default = $this->get_defaultsetting();
        $current = $this->get_setting();
        if ($current === null) {
            $current = $default;
        }

        $html = '';

        // Add custom CSS.
        $html .= '<style>
.avatar-selector-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 15px;
    margin: 20px 0;
    max-width: 800px;
}
.avatar-option {
    position: relative;
    cursor: pointer;
    border: 3px solid transparent;
    border-radius: 8px;
    padding: 5px;
    transition: all 0.2s ease;
    background: #f8f9fa;
}
.avatar-option:hover {
    border-color: #0f6cbf;
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.avatar-option.selected {
    border-color: #0066cc;
    background: #e3f2fd;
    box-shadow: 0 2px 8px rgba(0,102,204,0.3);
}
.avatar-option img {
    width: 100%;
    height: auto;
    display: block;
    border-radius: 4px;
}
.avatar-option-label {
    text-align: center;
    font-size: 12px;
    margin-top: 5px;
    font-weight: 600;
    color: #333;
}
.avatar-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.avatar-option.selected .avatar-option-label {
    color: #0066cc;
}
</style>';

        // Add JavaScript for interactive selection.
        $html .= '<script>
function selectAvatar(value) {
    document.querySelectorAll(".avatar-option").forEach(el => el.classList.remove("selected"));
    const selected = document.querySelector(`.avatar-option[data-value="${value}"]`);
    if (selected) {
        selected.classList.add("selected");
    }
    document.querySelector("input[name=\"s_local_dttutor_avatar\"]:checked").checked = false;
    const radio = document.querySelector(`input[name="s_local_dttutor_avatar"][value="${value}"]`);
    if (radio) {
        radio.checked = true;
    }
}
</script>';

        $html .= '<div class="avatar-selector-grid">';

        // Display all available avatars.
        for ($i = 1; $i <= 10; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $avatarpath = '/local/dttutor/pix/avatars/avatar_profesor_' . $num . '.png';
            $fullpath = $CFG->dirroot . $avatarpath;

            if (file_exists($fullpath)) {
                $selected = ($current === $num) ? 'selected' : '';
                $html .= '<div class="avatar-option ' . $selected . '" data-value="' . $num . '" onclick="selectAvatar(\'' . $num . '\')">';
                $html .= '<img src="' . $CFG->wwwroot . $avatarpath . '?v=' . time() . '" alt="Avatar ' . $i . '">';
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
