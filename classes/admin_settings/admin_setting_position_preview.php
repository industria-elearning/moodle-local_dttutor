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
 * Stores position as JSON: {"preset":"right|left|custom","x":"value","y":"value","drawerside":"right|left","xref":"left|right","yref":"bottom|top"}
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
            if (!isset($decoded['preset']) || !isset($decoded['x']) || !isset($decoded['y']) ||
                !isset($decoded['drawerside']) || !isset($decoded['xref']) || !isset($decoded['yref'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }
            // Validate preset.
            if (!in_array($decoded['preset'], ['right', 'left', 'custom'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }
            // Validate drawer side.
            if (!in_array($decoded['drawerside'], ['right', 'left'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }
            // Validate reference edges.
            if (!in_array($decoded['xref'], ['left', 'right'])) {
                return get_string('error_invalid_position', 'local_dttutor');
            }
            if (!in_array($decoded['yref'], ['bottom', 'top'])) {
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
            $currentdata = ['preset' => 'right', 'x' => '2rem', 'y' => '6rem', 'drawerside' => 'right', 'xref' => 'left', 'yref' => 'bottom'];
        }

        $preset = $currentdata['preset'] ?? 'right';
        $xvalue = $currentdata['x'] ?? '2rem';
        $yvalue = $currentdata['y'] ?? '6rem';
        $drawerside = $currentdata['drawerside'] ?? 'right';

        // Infer reference edges from negative values if not explicitly set.
        $xref = $currentdata['xref'] ?? (strpos($xvalue, '-') === 0 ? 'right' : 'left');
        $yref = $currentdata['yref'] ?? (strpos($yvalue, '-') === 0 ? 'top' : 'bottom');

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
.drawer-side-group {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 4px;
}
.drawer-side-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
}
.drawer-side-group select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
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
.reference-edge-group {
    margin-top: 15px;
    padding: 10px;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}
.reference-edge-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 0.9rem;
}
.reference-edge-options {
    display: flex;
    gap: 15px;
}
.reference-edge-options label {
    display: flex;
    align-items: center;
    font-weight: normal;
    margin-bottom: 0;
}
.reference-edge-options input[type="radio"] {
    margin-right: 5px;
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
    cursor: move;
    user-select: none;
}
.preview-avatar:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,0.4);
}
.preview-avatar.dragging {
    box-shadow: 0 8px 20px rgba(0,0,0,0.5);
    opacity: 0.9;
    cursor: grabbing;
    z-index: 1000;
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
    const xRefRadios = document.querySelectorAll(\'input[name="ref_x"]\');
    const yRefRadios = document.querySelectorAll(\'input[name="ref_y"]\');
    const drawerSideSelect = document.getElementById(\'drawer_side\');
    const previewAvatar = document.getElementById(\'preview-avatar\');
    const previewContainer = document.querySelector(\'.position-preview\');
    const coordsDisplay = document.getElementById(\'coords-display\');
    const hiddenInput = document.getElementById(\'id_s_local_dttutor_avatar_position_data\');

    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let initialLeft = 0;
    let initialTop = 0;

    function pixelsToRem(pixels) {
        const fontSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
        return (pixels / fontSize).toFixed(2) + \'rem\';
    }

    function cssValueToPixels(value, containerSize) {
        if (value.endsWith(\'px\')) {
            return parseFloat(value);
        } else if (value.endsWith(\'rem\')) {
            const fontSize = parseFloat(getComputedStyle(document.documentElement).fontSize);
            return parseFloat(value) * fontSize;
        } else if (value.endsWith(\'em\')) {
            const fontSize = parseFloat(getComputedStyle(previewAvatar).fontSize);
            return parseFloat(value) * fontSize;
        } else if (value.endsWith(\'%\')) {
            return (parseFloat(value) / 100) * containerSize;
        } else if (value.endsWith(\'vh\')) {
            return (parseFloat(value) / 100) * window.innerHeight;
        } else if (value.endsWith(\'vw\')) {
            return (parseFloat(value) / 100) * window.innerWidth;
        }
        return 0;
    }

    function updatePreview() {
        const preset = document.querySelector(\'input[name="position_preset"]:checked\').value;
        const drawerSide = drawerSideSelect.value;
        const xRef = document.querySelector(\'input[name="ref_x"]:checked\')?.value || \'left\';
        const yRef = document.querySelector(\'input[name="ref_y"]:checked\')?.value || \'bottom\';
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
            // Custom positioning - convert to absolute pixels for consistent drag behavior.
            const rect = previewContainer.getBoundingClientRect();
            const avatarWidth = previewAvatar.offsetWidth;
            const avatarHeight = previewAvatar.offsetHeight;

            const xSide = x.startsWith(\'-\') ? \'right\' : \'left\';
            const ySide = y.startsWith(\'-\') ? \'top\' : \'bottom\';

            const xValue = x.replace(\'-\', \'\');
            const yValue = y.replace(\'-\', \'\');

            // Convert CSS values to pixels.
            const xPx = cssValueToPixels(xValue, rect.width);
            const yPx = cssValueToPixels(yValue, rect.height);

            // Calculate absolute left and top positions.
            let leftPx, topPx;

            if (xSide === \'left\') {
                leftPx = xPx;
            } else {
                leftPx = rect.width - xPx - avatarWidth;
            }

            if (ySide === \'bottom\') {
                topPx = rect.height - yPx - avatarHeight;
            } else {
                topPx = yPx;
            }

            // Set absolute positioning.
            previewAvatar.style.left = leftPx + \'px\';
            previewAvatar.style.top = topPx + \'px\';
            previewAvatar.style.right = \'auto\';
            previewAvatar.style.bottom = \'auto\';
        }

        // Update coordinates display.
        const xDisplay = preset === \'custom\' ? `${x} (from ${xRef})` : x;
        const yDisplay = preset === \'custom\' ? `${y} (from ${yRef})` : y;
        coordsDisplay.textContent = `Position: ${preset === \'custom\' ? `X: ${xDisplay}, Y: ${yDisplay}` : preset + \' corner\'} | Drawer: ${drawerSide}`;

        // Update hidden input with JSON data.
        const data = {
            preset: preset,
            x: x,
            y: y,
            drawerside: drawerSide,
            xref: xRef,
            yref: yRef
        };
        hiddenInput.value = JSON.stringify(data);

        // Update preset option styling.
        document.querySelectorAll(\'.preset-option\').forEach(opt => opt.classList.remove(\'selected\'));
        const selectedOption = document.querySelector(`.preset-option[data-preset="${preset}"]`);
        if (selectedOption) {
            selectedOption.classList.add(\'selected\');
        }
    }

    // Drag and drop functionality.
    function handleMouseMove(e) {
        if (!isDragging) return;

        e.preventDefault();

        const rect = previewContainer.getBoundingClientRect();
        const deltaX = e.clientX - startX;
        const deltaY = e.clientY - startY;

        let newLeft = initialLeft + deltaX;
        let newTop = initialTop + deltaY;

        // Constrain within preview container.
        const avatarWidth = previewAvatar.offsetWidth;
        const avatarHeight = previewAvatar.offsetHeight;
        newLeft = Math.max(0, Math.min(newLeft, rect.width - avatarWidth));
        newTop = Math.max(0, Math.min(newTop, rect.height - avatarHeight));

        // Calculate position from edges.
        const fromRight = rect.width - newLeft - avatarWidth;
        const fromBottom = rect.height - newTop - avatarHeight;

        // Get selected reference edges.
        const xRef = document.querySelector(\'input[name="ref_x"]:checked\')?.value || \'left\';
        const yRef = document.querySelector(\'input[name="ref_y"]:checked\')?.value || \'bottom\';

        // Calculate and set values based on reference edges.
        if (xRef === \'left\') {
            xInput.value = pixelsToRem(newLeft);
        } else {
            // Reference from right - use negative value.
            xInput.value = \'-\' + pixelsToRem(fromRight);
        }

        if (yRef === \'bottom\') {
            yInput.value = pixelsToRem(fromBottom);
        } else {
            // Reference from top - use negative value.
            yInput.value = \'-\' + pixelsToRem(newTop);
        }

        // Update avatar position visually using absolute positioning.
        previewAvatar.style.left = newLeft + \'px\';
        previewAvatar.style.top = newTop + \'px\';
        previewAvatar.style.right = \'auto\';
        previewAvatar.style.bottom = \'auto\';

        // Update display.
        coordsDisplay.textContent = \'Position: X: \' + xInput.value + \' (from \' + xRef + \'), Y: \' + yInput.value + \' (from \' + yRef + \') | Drawer: \' + drawerSideSelect.value;
    }

    function handleMouseUp() {
        if (isDragging) {
            isDragging = false;
            previewAvatar.classList.remove(\'dragging\');
            document.removeEventListener(\'mousemove\', handleMouseMove);
            document.removeEventListener(\'mouseup\', handleMouseUp);

            // Update the hidden input with final values.
            updatePreview();
        }
    }

    previewAvatar.addEventListener(\'mousedown\', function(e) {
        e.preventDefault();
        isDragging = true;
        previewAvatar.classList.add(\'dragging\');

        const rect = previewContainer.getBoundingClientRect();
        const avatarRect = previewAvatar.getBoundingClientRect();

        startX = e.clientX;
        startY = e.clientY;

        // Get current position relative to container.
        initialLeft = avatarRect.left - rect.left;
        initialTop = avatarRect.top - rect.top;

        // Switch to custom preset when dragging.
        const customRadio = document.querySelector(\'input[name="position_preset"][value="custom"]\');
        if (customRadio) {
            customRadio.checked = true;
            customCoordsDiv.classList.add(\'active\');
        }

        // Attach move and up handlers.
        document.addEventListener(\'mousemove\', handleMouseMove);
        document.addEventListener(\'mouseup\', handleMouseUp);
    });

    // Event listeners.
    presetRadios.forEach(radio => {
        radio.addEventListener(\'change\', updatePreview);
    });

    xInput.addEventListener(\'input\', updatePreview);
    yInput.addEventListener(\'input\', updatePreview);
    drawerSideSelect.addEventListener(\'change\', updatePreview);

    xRefRadios.forEach(radio => {
        radio.addEventListener(\'change\', updatePreview);
    });

    yRefRadios.forEach(radio => {
        radio.addEventListener(\'change\', updatePreview);
    });

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

        // Drawer side selector.
        $html .= '<div class="drawer-side-group">';
        $html .= '<label for="drawer_side">' . get_string('drawer_side', 'local_dttutor') . '</label>';
        $html .= '<select id="drawer_side" name="drawer_side">';
        $html .= '<option value="right"' . ($drawerside === 'right' ? ' selected' : '') . '>' . get_string('drawer_side_right', 'local_dttutor') . '</option>';
        $html .= '<option value="left"' . ($drawerside === 'left' ? ' selected' : '') . '>' . get_string('drawer_side_left', 'local_dttutor') . '</option>';
        $html .= '</select>';
        $html .= '<div class="coord-help">' . get_string('drawer_side_help', 'local_dttutor') . '</div>';
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

        // Reference edge selectors.
        $html .= '<div class="reference-edge-group">';
        $html .= '<label>' . get_string('reference_edge_x', 'local_dttutor') . ':</label>';
        $html .= '<div class="reference-edge-options">';
        $html .= '<label><input type="radio" name="ref_x" value="left"' . ($xref === 'left' ? ' checked' : '') . '> ' . get_string('ref_left', 'local_dttutor') . '</label>';
        $html .= '<label><input type="radio" name="ref_x" value="right"' . ($xref === 'right' ? ' checked' : '') . '> ' . get_string('ref_right', 'local_dttutor') . '</label>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="reference-edge-group">';
        $html .= '<label>' . get_string('reference_edge_y', 'local_dttutor') . ':</label>';
        $html .= '<div class="reference-edge-options">';
        $html .= '<label><input type="radio" name="ref_y" value="bottom"' . ($yref === 'bottom' ? ' checked' : '') . '> ' . get_string('ref_bottom', 'local_dttutor') . '</label>';
        $html .= '<label><input type="radio" name="ref_y" value="top"' . ($yref === 'top' ? ' checked' : '') . '> ' . get_string('ref_top', 'local_dttutor') . '</label>';
        $html .= '</div>';
        $html .= '</div>';

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
