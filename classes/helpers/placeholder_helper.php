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
 * Placeholder helper for AI Mode welcome messages
 *
 * @package    local_dttutor
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dttutor\helpers;

/**
 * Helper class for processing placeholders in AI Mode messages
 *
 * @package    local_dttutor
 * @category   helpers
 * @copyright  2025 Industria Elearning <info@industriaelearning.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class placeholder_helper {

    /**
     * Replace placeholders in text with actual user/course/site data.
     *
     * Available placeholders:
     * - {username}: Full name of the user
     * - {firstname}: User's first name
     * - {lastname}: User's last name
     * - {email}: User's email address
     * - {sitename}: Name of the Moodle site
     * - {coursename}: Name of the current course (empty if not in a course)
     *
     * @param string $text Text containing placeholders
     * @return string Text with placeholders replaced
     * @since Moodle 4.5
     */
    public static function replace_placeholders(string $text): string {
        global $USER, $COURSE, $SITE;

        // Build replacements array.
        $replacements = [
            '{username}' => fullname($USER),
            '{firstname}' => $USER->firstname,
            '{lastname}' => $USER->lastname,
            '{email}' => $USER->email,
            '{sitename}' => format_string($SITE->fullname),
            '{coursename}' => isset($COURSE->id) && $COURSE->id > 1 ? format_string($COURSE->fullname) : '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * Get all available placeholders with their descriptions.
     *
     * @return array Associative array of placeholder => description
     * @since Moodle 4.5
     */
    public static function get_available_placeholders(): array {
        return [
            '{username}' => get_string('placeholder_username', 'local_dttutor'),
            '{firstname}' => get_string('placeholder_firstname', 'local_dttutor'),
            '{lastname}' => get_string('placeholder_lastname', 'local_dttutor'),
            '{email}' => get_string('placeholder_email', 'local_dttutor'),
            '{sitename}' => get_string('placeholder_sitename', 'local_dttutor'),
            '{coursename}' => get_string('placeholder_coursename', 'local_dttutor'),
        ];
    }
}
