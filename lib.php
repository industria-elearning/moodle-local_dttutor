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
 * Library functions for local_dttutor plugin
 *
 * @package    local_dttutor
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Render output for the navbar.
 *
 * This callback is auto-discovered by Moodle's navbar_plugin_output() function
 * and allows plugins to inject HTML into the site navigation bar.
 *
 * @param renderer_base $renderer The page renderer
 * @return string HTML to be injected into the navbar
 * @since Moodle 4.5
 */
function local_dttutor_render_navbar_output(\renderer_base $renderer): string {
    global $USER, $PAGE;

    // Early bail out if user is not logged in or is a guest.
    if (!isloggedin() || isguestuser()) {
        return '';
    }

    // Check if AI Mode is enabled.
    if (!get_config('local_dttutor', 'aimode_enabled')) {
        return '';
    }

    // Initialize JavaScript to reposition the button to the left side.
    $PAGE->requires->js_call_amd('local_dttutor/navbar_button_position', 'init');

    // Prepare context for template.
    $context = [
        'userid' => $USER->id,
        'buttontext' => get_string('aimode_button', 'local_dttutor'),
    ];

    // Render and return the navbar button template.
    return $renderer->render_from_template('local_dttutor/navbar_aimode_button', $context);
}
