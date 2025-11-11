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
 * Stores position as JSON: {"preset":"right|left|custom","x":"value","y":"value"}
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_position_preview extends \admin_setting {
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
        $value = $this->config_read($this->name);
        if ($value === null) {
            return $this->get_defaultsetting();
        }
        return $value;
    }

    /**
     * Save the position settings
     *
     * @param mixed $data
     * @return string Empty string if ok, error message otherwise
     */
    public function write_setting($data) {
        // Data comes as JSON string from JavaScript.
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if ($decoded === null) {
                return get_string('error_invalid_position', 'local_dttutor');
            }
            // Validate data structure.
            if (!isset($decoded['preset']) || !isset($decoded['x']) || !isset($decoded['y'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }
            // Validate preset.
            if (!in_array($decoded['preset'], ['right', 'left', 'custom'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }
            // Validate coordinates (should be valid CSS values).
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
        // Allow numeric values with units: px, rem, em, %, vh, vw.
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
        global $OUTPUT, $CFG;

        $default = $this->get_defaultsetting();
        $current = $this->get_setting();
        if ($current === null) {
            $current = $default;
        }

        // Decode current value.
        $currentdata = json_decode($current, true);
        if ($currentdata === null) {
            // Fallback to right corner default.
            $currentdata = ['preset' => 'right', 'x' => '2rem', 'y' => '6rem'];
        }

        $preset = $currentdata['preset'] ?? 'right';
        $xvalue = $currentdata['x'] ?? '2rem';
        $yvalue = $currentdata['y'] ?? '6rem';

        // Get current avatar for preview.
        $avatar = get_config('local_dttutor', 'avatar') ?? '01';
        $avatarurl = $CFG->wwwroot . '/local/dttutor/pix/avatars/avatar_profesor_' . $avatar . '.png';

        // Check if custom avatar exists.
        $customavatarurl = $this->get_custom_avatar_url();
        if ($customavatarurl) {
            $avatarurl = $customavatarurl;
        }

        $html = '';

        // Add CSS for the position configurator.
        $html .= '<style>
.position-configurator {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    max-width: 900px;
}
.position-controls {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}
.position-control-left {
    padding-right: 20px;
    border-right: 1px solid #dee2e6;
}
.position-preset-group {
    margin-bottom: 20px;
}
.position-preset-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}
.preset-option {
    display: flex;
    align-items: center;
    padding: 10px;
    margin: 5px 0;
    border: 2px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.2s ease;
}
.preset-option:hover {
    border-color: #0f6cbf;
    background: #f8f9fa;
}
.preset-option.selected {
    border-color: #0066cc;
    background: #e3f2fd;
}
.preset-option input[type="radio"] {
    margin-right: 10px;
}
.custom-coords {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
    display: none;
}
.custom-coords.active {
    display: block;
}
.coord-input-group {
    margin-bottom: 10px;
}
.coord-input-group label {
    display: inline-block;
    width: 120px;
    font-weight: 600;
}
.coord-input-group input {
    width: 150px;
    padding: 5px 10px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}
.coord-help {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 5px;
}
.position-preview {
    position: relative;
    width: 100%;
    height: 400px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.preview-label {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(255,255,255,0.9);
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 600;
}
.preview-avatar {
    position: absolute;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
    cursor: pointer;
}
.preview-avatar:hover {
    transform: scale(1.1);
}
.preview-coordinates {
    position: absolute;
    bottom: 10px;
    left: 10px;
    right: 10px;
    background: rgba(255,255,255,0.9);
    padding: 10px;
    border-radius: 4px;
    font-size: 0.85rem;
    text-align: center;
}
.preview-grid {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px);
    background-size: 50px 50px;
    pointer-events: none;
}
</style>';

        // Add JavaScript for interactive controls.
        $html .= '<script>
function initPositionConfigurator() {
    const presetRadios = document.querySelectorAll(\'input[name="position_preset"]\');
    const customCoordsDiv = document.getElementById(\'custom-coords\');
    const xInput = document.getElementById(\'position_x\');
    const yInput = document.getElementById(\'position_y\');
    const previewAvatar = document.getElementById(\'preview-avatar\');
    const coordsDisplay = document.getElementById(\'coords-display\');
    const hiddenInput = document.getElementById(\'id_s_local_dttutor_avatar_position_data\');

    function updatePreview() {
        const preset = document.querySelector(\'input[name="position_preset"]:checked\').value;
        let x = xInput.value;
        let y = yInput.value;

        // Update visibility of custom coords.
        if (preset === \'custom\') {
            customCoordsDiv.classList.add(\'active\');
        } else {
            customCoordsDiv.classList.remove(\'active\');
            // Set default values for presets.
            if (preset === \'right\') {
                x = \'2rem\';
                y = \'6rem\';
            } else if (preset === \'left\') {
                x = \'2rem\';
                y = \'6rem\';
            }
        }

        // Update preview avatar position.
        if (preset === \'right\') {
            previewAvatar.style.right = x;
            previewAvatar.style.left = \'auto\';
            previewAvatar.style.bottom = y;
            previewAvatar.style.top = \'auto\';
        } else if (preset === \'left\') {
            previewAvatar.style.left = x;
            previewAvatar.style.right = \'auto\';
            previewAvatar.style.bottom = y;
            previewAvatar.style.top = \'auto\';
        } else {
            // Custom positioning.
            const xSide = x.startsWith(\'-\') ? \'right\' : \'left\';
            const ySide = y.startsWith(\'-\') ? \'top\' : \'bottom\';

            previewAvatar.style[xSide] = x.replace(\'-\', \'\');
            previewAvatar.style[xSide === \'left\' ? \'right\' : \'left\'] = \'auto\';
            previewAvatar.style[ySide] = y.replace(\'-\', \'\');
            previewAvatar.style[ySide === \'bottom\' ? \'top\' : \'bottom\'] = \'auto\';
        }

        // Update coordinates display.
        coordsDisplay.textContent = `Position: ${preset === \'custom\' ? `X: ${x}, Y: ${y}` : preset + \' corner\'}`;

        // Update hidden input with JSON data.
        const data = {
            preset: preset,
            x: x,
            y: y
        };
        hiddenInput.value = JSON.stringify(data);

        // Update preset option styling.
        document.querySelectorAll(\'.preset-option\').forEach(opt => opt.classList.remove(\'selected\'));
        const selectedOption = document.querySelector(`.preset-option[data-preset="${preset}"]`);
        if (selectedOption) {
            selectedOption.classList.add(\'selected\');
        }
    }

    // Event listeners.
    presetRadios.forEach(radio => {
        radio.addEventListener(\'change\', updatePreview);
    });

    xInput.addEventListener(\'input\', updatePreview);
    yInput.addEventListener(\'input\', updatePreview);

    // Preset option click handlers.
    document.querySelectorAll(\'.preset-option\').forEach(opt => {
        opt.addEventListener(\'click\', function() {
            const preset = this.dataset.preset;
            document.querySelector(`input[name="position_preset"][value="${preset}"]`).checked = true;
            updatePreview();
        });
    });

    // Initial update.
    updatePreview();
}

// Run on page load.
if (document.readyState === \'loading\') {
    document.addEventListener(\'DOMContentLoaded\', initPositionConfigurator);
} else {
    initPositionConfigurator();
}
</script>';

        // Build the HTML structure.
        $html .= '<div class="position-configurator">';
        $html .= '<div class="position-controls">';

        // Left panel: Controls.
        $html .= '<div class="position-control-left">';
        $html .= '<div class="position-preset-group">';
        $html .= '<label>' . get_string('position_preset', 'local_dttutor') . '</label>';

        // Preset options.
        $presets = [
            'right' => get_string('position_right', 'local_dttutor'),
            'left' => get_string('position_left', 'local_dttutor'),
            'custom' => get_string('position_custom', 'local_dttutor'),
        ];

        foreach ($presets as $value => $label) {
            $checked = ($preset === $value) ? 'checked' : '';
            $selected = ($preset === $value) ? 'selected' : '';
            $html .= '<div class="preset-option ' . $selected . '" data-preset="' . $value . '">';
            $html .= '<input type="radio" name="position_preset" value="' . $value . '" id="preset_' . $value . '" ' . $checked . '>';
            $html .= '<label for="preset_' . $value . '">' . $label . '</label>';
            $html .= '</div>';
        }

        $html .= '</div>';

        // Custom coordinates (shown when custom is selected).
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
        $html .= '</div>';

        $html .= '</div>';

        // Right panel: Preview.
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

        // Hidden input to store the JSON data.
        $html .= '<input type="hidden" name="s_local_dttutor_avatar_position_data" id="id_s_local_dttutor_avatar_position_data" value="' . s($current) . '">';

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
