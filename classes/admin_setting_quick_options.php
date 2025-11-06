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
 * Custom admin setting for Quick Start Options
 *
 * @package    local_dttutor
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dttutor;

/**
 * Admin setting for Quick Start Options with visual interface
 *
 * @package    local_dttutor
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_quick_options extends \admin_setting {

    /**
     * Constructor
     *
     * @param string $name unique name
     * @param string $visiblename display name
     * @param string $description description
     * @param mixed $defaultsetting default setting value
     */
    public function __construct($name, $visiblename, $description, $defaultsetting) {
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Store new setting
     *
     * @param mixed $data string or array, must not be NULL
     * @return string empty string if ok, string error message otherwise
     */
    public function write_setting($data) {
        // Data comes as JSON string from JavaScript.
        if (is_string($data)) {
            // Validate JSON.
            $decoded = json_decode($data, true);
            if ($decoded === null && $data !== 'null' && $data !== '[]') {
                return get_string('error_invalid_json', 'local_dttutor');
            }
            return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
        }
        return get_string('errorsetting', 'admin');
    }

    /**
     * Return an XHTML string for the setting
     *
     * @param mixed $data
     * @param string $query
     * @return string Returns an XHTML string
     */
    public function output_html($data, $query = '') {
        global $OUTPUT, $PAGE;

        // Parse current value.
        $options = [];
        if (!empty($data)) {
            $decoded = json_decode($data, true);
            if (is_array($decoded)) {
                $options = $decoded;
            }
        }

        // If no options, use defaults.
        if (empty($options)) {
            $options = [
                ['icon' => '✍️', 'label' => 'Write', 'prompt' => 'Help me write...'],
                ['icon' => '🔍', 'label' => 'Research', 'prompt' => 'Research about...'],
                ['icon' => '📚', 'label' => 'Learn', 'prompt' => 'Teach me about...'],
            ];
        }

        // Initialize JavaScript module.
        $PAGE->requires->js_call_amd('local_dttutor/quick_options_manager', 'init', [
            'fieldname' => $this->get_full_name(),
            'options' => $options,
        ]);

        // Prepare template context.
        $context = [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'options' => $options,
            'forceltr' => $this->get_force_ltr(),
        ];

        $element = $OUTPUT->render_from_template('local_dttutor/admin_setting_quick_options', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', null, $query);
    }
}
