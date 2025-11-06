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
 * Navbar button repositioning for AI Mode button
 *
 * @module     local_dttutor/navbar_button_position
 * @copyright  2025 Industria Elearning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    'use strict';

    return {
        init: function() {
            // Wait for DOM to be ready.
            $(document).ready(function() {
                // Find the AI Mode button.
                const aiButton = $('[data-action="open-ai-modal"]').closest('.popover-region');

                if (!aiButton.length) {
                    return; // Button not found, AI Mode is disabled.
                }

                // Find the user navigation area (right side of navbar).
                const userNavigation = $('#usernavigation');

                if (!userNavigation.length) {
                    return; // User navigation not found.
                }

                // Add a divider after the button (like other navbar items).
                aiButton.after('<div class="divider border-start h-75 align-self-center mx-1"></div>');

                // Prepend the button to the beginning of user navigation area.
                // This places it before search, language menu, notifications, messages, etc.
                userNavigation.prepend(aiButton);
            });
        }
    };
});
