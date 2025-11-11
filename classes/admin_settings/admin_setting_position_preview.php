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
 * Custom admin setting for avatar position with live preview
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dttutor\admin_settings;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

/**
 * Admin setting for avatar position with live preview
 *
 * Stores position as JSON with preset, coordinates, drawer side and reference edges
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_position_preview extends \admin_setting {

    /**
     * Return the current setting
     *
     * @return mixed
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Save the position settings
     *
     * @param mixed $data
     * @return string Empty string if ok, error message otherwise
     */
    public function write_setting($data) {
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if ($decoded === null) {
                return get_string('error_invalid_position', 'local_dttutor');
            }

            if (
                !isset($decoded['preset']) || !isset($decoded['x']) || !isset($decoded['y']) ||
                !isset($decoded['drawerside']) || !isset($decoded['xref']) || !isset($decoded['yref'])
            ) {
                return get_string('error_invalid_position', 'local_dttutor');
            }

            if (!in_array($decoded['preset'], ['right', 'left', 'custom'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }

            if (!in_array($decoded['drawerside'], ['right', 'left'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }

            if (!in_array($decoded['xref'], ['left', 'right'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }

            if (!in_array($decoded['yref'], ['bottom', 'top'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }

            if ($decoded['preset'] === 'custom') {
                if (!$this->validate_css_value($decoded['x']) || !$this->validate_css_value($decoded['y'])) {
                    return get_string('error_invalid_coordinates', 'local_dttutor');
                }
            }
        }
        return $this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin');
    }

    /**
     * Validate CSS value (e.g., "10px", "2rem", "50%")
     *
     * @param string $value
     * @return bool
     */
    private function validate_css_value($value) {
        return preg_match('/^-?\d+(\.\d+)?(px|rem|em|%|vh|vw)$/', $value) === 1;
    }

    /**
     * Return HTML for the setting with live preview
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

        $currentdata = json_decode($current, true);
        if ($currentdata === null) {
            $currentdata = ['preset' => 'right', 'x' => '2rem', 'y' => '6rem',
                'drawerside' => 'right', 'xref' => 'left', 'yref' => 'bottom'];
        }

        $preset = $currentdata['preset'] ?? 'right';
        $xvalue = $currentdata['x'] ?? '2rem';
        $yvalue = $currentdata['y'] ?? '6rem';
        $drawerside = $currentdata['drawerside'] ?? 'right';
        $xref = $currentdata['xref'] ?? (strpos($xvalue, '-') === 0 ? 'right' : 'left');
        $yref = $currentdata['yref'] ?? (strpos($yvalue, '-') === 0 ? 'top' : 'bottom');

        $avatar = get_config('local_dttutor', 'avatar') ?? '01';
        $avatarurl = $CFG->wwwroot . '/local/dttutor/pix/avatars/avatar_profesor_' . $avatar . '.png';

        $customavatarurl = $this->get_custom_avatar_url();
        if ($customavatarurl) {
            $avatarurl = $customavatarurl;
        }

        $html = '';

        $html .= '<link rel="stylesheet" href="' . $CFG->wwwroot . '/local/dttutor/styles_admin.css">';

        // Load AMD module properly via $PAGE.
        $PAGE->requires->js_call_amd('local_dttutor/position_configurator', 'init');

        $html .= '<div class="position-configurator">';
        $html .= '<div class="position-controls">';
        $html .= '<div class="position-control-left">';
        $html .= '<div class="position-preset-group">';
        $html .= '<label>' . get_string('position_preset', 'local_dttutor') . '</label>';

        $presets = [
            'right' => get_string('position_right', 'local_dttutor'),
            'left' => get_string('position_left', 'local_dttutor'),
            'custom' => get_string('position_custom', 'local_dttutor'),
        ];

        foreach ($presets as $value => $label) {
            $checked = ($preset === $value) ? 'checked' : '';
            $selected = ($preset === $value) ? 'selected' : '';
            $html .= '<div class="preset-option ' . $selected . '" data-preset="' . $value . '">';
            $html .= '<input type="radio" name="position_preset" value="' . $value . '" ';
            $html .= 'id="preset_' . $value . '" ' . $checked . '>';
            $html .= '<label for="preset_' . $value . '">' . $label . '</label>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '<div class="drawer-side-group">';
        $html .= '<label for="drawer_side">' . get_string('drawer_side', 'local_dttutor') . '</label>';
        $html .= '<select id="drawer_side" name="drawer_side">';
        $html .= '<option value="right"' . ($drawerside === 'right' ? ' selected' : '') . '>' .
            get_string('drawer_side_right', 'local_dttutor') . '</option>';
        $html .= '<option value="left"' . ($drawerside === 'left' ? ' selected' : '') . '>' .
            get_string('drawer_side_left', 'local_dttutor') . '</option>';
        $html .= '</select>';
        $html .= '<div class="coord-help">' . get_string('drawer_side_help', 'local_dttutor') . '</div>';
        $html .= '</div>';

        $activeclass = ($preset === 'custom') ? 'active' : '';
        $html .= '<div class="custom-coords ' . $activeclass . '" id="custom-coords">';
        $html .= '<div class="coord-input-group">';
        $html .= '<label for="position_x">' . get_string('position_x', 'local_dttutor') . ':</label>';
        $html .= '<input type="text" id="position_x" value="' . s($xvalue) . '" placeholder="2rem">';
        $html .= '</div>';
        $html .= '<div class="coord-help">' . get_string('position_x_help', 'local_dttutor') . '</div>';

        $html .= '<div class="coord-input-group">';
        $html .= '<label for="position_y">' . get_string('position_y', 'local_dttutor') . ':</label>';
        $html .= '<input type="text" id="position_y" value="' . s($yvalue) . '" placeholder="6rem">';
        $html .= '</div>';
        $html .= '<div class="coord-help">' . get_string('position_y_help', 'local_dttutor') . '</div>';

        $html .= '<div class="reference-edge-group">';
        $html .= '<label>' . get_string('reference_edge_x', 'local_dttutor') . ':</label>';
        $html .= '<div class="reference-edge-options">';
        $html .= '<label><input type="radio" name="ref_x" value="left"';
        $html .= ($xref === 'left' ? ' checked' : '') . '> ';
        $html .= get_string('ref_left', 'local_dttutor') . '</label>';
        $html .= '<label><input type="radio" name="ref_x" value="right"';
        $html .= ($xref === 'right' ? ' checked' : '') . '> ';
        $html .= get_string('ref_right', 'local_dttutor') . '</label>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="reference-edge-group">';
        $html .= '<label>' . get_string('reference_edge_y', 'local_dttutor') . ':</label>';
        $html .= '<div class="reference-edge-options">';
        $html .= '<label><input type="radio" name="ref_y" value="bottom"';
        $html .= ($yref === 'bottom' ? ' checked' : '') . '> ';
        $html .= get_string('ref_bottom', 'local_dttutor') . '</label>';
        $html .= '<label><input type="radio" name="ref_y" value="top"';
        $html .= ($yref === 'top' ? ' checked' : '') . '> ';
        $html .= get_string('ref_top', 'local_dttutor') . '</label>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

        $html .= '</div>';

        $html .= '<div class="position-control-right">';
        $html .= '<div class="position-preview">';
        $html .= '<div class="preview-grid"></div>';
        $html .= '<div class="preview-label">' . get_string('preview', 'local_dttutor') . '</div>';
        $html .= '<img id="preview-avatar" class="preview-avatar" src="' . $avatarurl . '" alt="Avatar Preview">';
        $html .= '<div class="preview-coordinates" id="coords-display"></div>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</div>';

        $html .= '<input type="hidden" name="s_local_dttutor_avatar_position_data" ' .
            'id="id_s_local_dttutor_avatar_position_data" value="' . s($current) . '">';

        return format_admin_setting($this, $this->visiblename, $html, $this->description, true, '', $default, $query);
    }

    /**
     * Get custom avatar URL if it exists
     *
     * @return string|null
     */
    private function get_custom_avatar_url() {
        global $CFG;
        $fs = get_file_storage();
        $context = \context_system::instance();
        $files = $fs->get_area_files($context->id, 'local_dttutor', 'customavatar', 0, 'timemodified DESC', false);

        if (!empty($files)) {
            $file = reset($files);
            return \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            )->out();
        }

        return null;
    }
}
