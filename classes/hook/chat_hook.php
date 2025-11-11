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

namespace local_dttutor\hook;

use core\hook\output\before_footer_html_generation;

/**
 * Hook to load the Tutor-IA floating chat
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso <josue@datacurso.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chat_hook {
    /**
     * Hook to load the floating chat before the footer.
     *
     * @param before_footer_html_generation $hook The hook event.
     * @since Moodle 4.5
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        self::add_float_chat($hook);
    }

    /**
     * Checks if we are in a course context.
     * Returns true if the current page or context is related to a course or module.
     *
     * @return bool
     * @since Moodle 4.5
     */
    private static function is_course_context(): bool {
        global $PAGE, $COURSE;

        // Check if we are on a course page.
        if (
            $PAGE->pagelayout === 'course' ||
            $PAGE->pagelayout === 'incourse' ||
            strpos($PAGE->pagetype, 'course-') === 0 ||
            strpos($PAGE->pagetype, 'mod-') === 0
        ) {
            return true;
        }

        // Check if there is a valid course.
        if (isset($COURSE) && $COURSE->id > 1) {
            return true;
        }

        // Check context.
        $context = $PAGE->context;
        if (!$context) {
            return false;
        }
        if (
            $context->contextlevel == CONTEXT_COURSE ||
            $context->contextlevel == CONTEXT_MODULE
        ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the current module context is a quiz activity.
     * Returns true if we are viewing a quiz module.
     *
     * @return bool
     * @since Moodle 4.5
     */
    private static function is_quiz_module(): bool {
        global $PAGE;

        $context = $PAGE->context;

        // Only check if we're in a module context.
        if ($context->contextlevel != CONTEXT_MODULE) {
            return false;
        }

        // Get the course module information.
        $cm = get_coursemodule_from_id('', $context->instanceid, 0, false, IGNORE_MISSING);

        if (!$cm) {
            return false;
        }

        // Check if the module is a quiz.
        return ($cm->modname === 'quiz');
    }

    /**
     * Adds the Tutor-IA drawer to course pages for all users.
     *
     * @param before_footer_html_generation $hook The hook event.
     * @since Moodle 4.5
     */
    private static function add_float_chat(before_footer_html_generation $hook): void {
        global $PAGE, $COURSE, $USER, $OUTPUT;

        // Check if chat is enabled globally.
        if (!get_config('local_dttutor', 'enabled')) {
            return;
        }

        if (!self::is_course_context()) {
            return;
        }

        $courseid = $COURSE->id ?? 0;
        if ($courseid <= 1) {
            return; // Don't show on frontpage.
        }

        // Do not show chat in quiz activities.
        if (self::is_quiz_module()) {
            return;
        }

        // Detect cmid (Course Module ID) if we are in a module context.
        $cmid = 0;
        $context = $PAGE->context;
        if ($context->contextlevel == CONTEXT_MODULE) {
            $cmid = $context->instanceid;
        }

        // Detect user role.
        $userrole = self::get_user_role_in_course();
        $userroledisplay = ($userrole === 'teacher')
            ? get_string('teacher', 'local_dttutor')
            : get_string('student', 'local_dttutor');

        // Get configured avatar with fallback system.
        $avatarurl = self::get_avatar_url();

        // Get avatar position data (new JSON format).
        $positiondata = self::get_position_data();

        // Get tutor name from configuration (can contain placeholders).
        $tutorname = get_config('local_dttutor', 'tutorname');
        if (empty($tutorname)) {
            $tutorname = get_string('tutorname_default', 'local_dttutor');
        }
        // Process placeholders in tutor name (e.g., {teachername} will be replaced with actual teacher name).
        $tutorname = self::replace_placeholders($tutorname, $courseid);

        // Get and process welcome message with placeholders.
        $welcomemessage = get_config('local_dttutor', 'welcomemessage');
        if (empty($welcomemessage)) {
            $welcomemessage = get_string('welcomemessage_default', 'local_dttutor');
        }
        $welcomemessage = self::replace_placeholders($welcomemessage, $courseid);

        // Generate unique ID.
        $uniqid = uniqid('tia_');

        // Calculate position style for toggle button.
        $positionstyle = self::calculate_position_style($positiondata);

        // Get drawer side from configuration.
        $drawerside = $positiondata['drawerside'] ?? 'right';

        // Prepare data for templates.
        $toggledata = [
            'uniqid' => $uniqid,
            'avatarurl' => $avatarurl->out(false),
            'position' => $positiondata['preset'],
            'position_style' => $positionstyle,
        ];

        $drawerdata = [
            'uniqid' => $uniqid,
            'courseid' => $courseid,
            'cmid' => $cmid,
            'userid' => $USER->id,
            'userrole' => $userroledisplay,
            'tutorname' => $tutorname,
            'welcomemessage' => $welcomemessage,
            'avatarurl' => $avatarurl->out(false),
            'position' => $drawerside, // Use drawer side from configuration.
        ];

        // Render templates.
        $toggle = $OUTPUT->render_from_template('local_dttutor/tutor_ia_toggle', $toggledata);
        $drawer = $OUTPUT->render_from_template('local_dttutor/tutor_ia_drawer', $drawerdata);

        // Add HTML directly to footer using the hook.
        $hook->add_html($toggle . $drawer);
    }

    /**
     * Calculates the CSS inline style for button positioning.
     *
     * @param array $positiondata Position data array with 'preset', 'x', 'y' keys
     * @return string CSS style string
     * @since Moodle 4.5
     */
    private static function calculate_position_style(array $positiondata): string {
        $preset = $positiondata['preset'];
        $x = $positiondata['x'];
        $y = $positiondata['y'];

        $style = '';

        // Handle preset positions.
        if ($preset === 'right') {
            $style = "right: {$x}; bottom: {$y};";
        } else if ($preset === 'left') {
            $style = "left: {$x}; bottom: {$y};";
        } else if ($preset === 'custom') {
            // For custom, check if values are negative (position from opposite side).
            if (strpos($x, '-') === 0) {
                // Negative X means position from right.
                $xvalue = ltrim($x, '-');
                $style .= "right: {$xvalue}; ";
            } else {
                // Positive X means position from left.
                $style .= "left: {$x}; ";
            }

            if (strpos($y, '-') === 0) {
                // Negative Y means position from top.
                $yvalue = ltrim($y, '-');
                $style .= "top: {$yvalue};";
            } else {
                // Positive Y means position from bottom.
                $style .= "bottom: {$y};";
            }
        }

        return $style;
    }

    /**
     * Gets the position data from configuration with fallback support.
     *
     * Returns array with 'preset', 'x', 'y', and 'drawerside' keys.
     * Provides backward compatibility with old 'avatar_position' config.
     *
     * @return array Position data array
     * @since Moodle 4.5
     */
    private static function get_position_data(): array {
        // Try new JSON format first.
        $positiondata = get_config('local_dttutor', 'avatar_position_data');

        if (!empty($positiondata)) {
            $decoded = json_decode($positiondata, true);
            if ($decoded !== null && isset($decoded['preset'], $decoded['x'], $decoded['y'])) {
                // Ensure drawerside exists (backward compatibility).
                if (!isset($decoded['drawerside'])) {
                    // Infer from preset or default to right.
                    $decoded['drawerside'] = isset($decoded['preset']) && $decoded['preset'] === 'left' ? 'left' : 'right';
                }
                return $decoded;
            }
        }

        // Fallback to old format for backward compatibility.
        $oldposition = get_config('local_dttutor', 'avatar_position');
        if ($oldposition === 'left') {
            return [
                'preset' => 'left',
                'x' => '2rem',
                'y' => '6rem',
                'drawerside' => 'left',
            ];
        }

        // Default to right corner.
        return [
            'preset' => 'right',
            'x' => '2rem',
            'y' => '6rem',
            'drawerside' => 'right',
        ];
    }

    /**
     * Gets the configured avatar URL with fallback system.
     *
     * @return \moodle_url Avatar URL to use
     * @since Moodle 4.5
     */
    private static function get_avatar_url(): \moodle_url {
        global $CFG;

        // First priority: Check if custom avatar exists.
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
            return \moodle_url::make_pluginfile_url(
                $file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename()
            );
        }

        // Second priority: Use selected predefined avatar.
        $avatarnum = get_config('local_dttutor', 'avatar');

        // First fallback: If no configuration, use '01'.
        if (empty($avatarnum)) {
            $avatarnum = '01';
        }

        // Second fallback: If file doesn't exist, use '01'.
        $avatarpath = $CFG->dirroot . '/local/dttutor/pix/avatars/avatar_profesor_' . $avatarnum . '.png';
        if (!file_exists($avatarpath)) {
            $avatarnum = '01';

            // Last fallback: If even '01' doesn't exist, use generic Moodle icon.
            $defaultpath = $CFG->dirroot . '/local/dttutor/pix/avatars/avatar_profesor_01.png';
            if (!file_exists($defaultpath)) {
                // Use generic Moodle user icon.
                return new \moodle_url('/pix/u/f1.png');
            }
        }

        return new \moodle_url('/local/dttutor/pix/avatars/avatar_profesor_' . $avatarnum . '.png');
    }

    /**
     * Determines the user's role in the current course context.
     *
     * @return string User role: 'teacher' or 'student'
     * @since Moodle 4.5
     */
    private static function get_user_role_in_course(): string {
        global $COURSE, $USER;

        if (!isset($COURSE) || $COURSE->id <= 1) {
            return 'student';
        }

        $context = \context_course::instance($COURSE->id);

        if (
            has_capability('moodle/course:update', $context) ||
            has_capability('moodle/course:manageactivities', $context)
        ) {
            return 'teacher';
        }

        // Verify specific roles.
        $roles = get_user_roles($context, $USER->id);
        foreach ($roles as $role) {
            if (in_array($role->shortname, ['teacher', 'editingteacher', 'manager', 'coursecreator'])) {
                return 'teacher';
            }
        }

        return 'student';
    }

    /**
     * Gets the first teacher's name from the course.
     *
     * @param int $courseid Course ID
     * @return string First teacher's full name or empty string if not found
     * @since Moodle 4.5
     */
    private static function get_first_teacher_name(int $courseid): string {
        global $DB;

        if ($courseid <= 1) {
            return '';
        }

        $context = \context_course::instance($courseid);

        // Get all users with teacher role capabilities.
        // Include all fields required by fullname() to avoid debugging warnings.
        $teachers = get_enrolled_users(
            $context,
            'moodle/course:update',
            0,
            'u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename',
            'u.lastname ASC, u.firstname ASC',
            0,
            1
        );

        if (!empty($teachers)) {
            $teacher = reset($teachers);
            return fullname($teacher);
        }

        // Fallback: search by role shortname.
        // Include all fields required by fullname() to avoid debugging warnings.
        $sql = "SELECT u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename
                  FROM {user} u
                  JOIN {role_assignments} ra ON ra.userid = u.id
                  JOIN {role} r ON r.id = ra.roleid
                  JOIN {context} ctx ON ctx.id = ra.contextid
                 WHERE ctx.contextlevel = :contextlevel
                   AND ctx.instanceid = :courseid
                   AND r.shortname IN ('teacher', 'editingteacher', 'manager')
              ORDER BY u.lastname ASC, u.firstname ASC
                 LIMIT 1";

        $params = [
            'contextlevel' => CONTEXT_COURSE,
            'courseid' => $courseid,
        ];

        $teacher = $DB->get_record_sql($sql, $params);

        if ($teacher) {
            return fullname($teacher);
        }

        return '';
    }

    /**
     * Replaces placeholders in text with actual values.
     *
     * @param string $text Text containing placeholders
     * @param int $courseid Course ID
     * @return string Text with placeholders replaced
     * @since Moodle 4.5
     */
    private static function replace_placeholders(string $text, int $courseid): string {
        global $USER, $COURSE, $DB;

        // Get teacher name from course.
        $teachername = self::get_first_teacher_name($courseid);

        // If no teacher found, use a generic placeholder text.
        // Note: The tutorname config is NOT used here as fallback because it might contain
        // the {teachername} placeholder itself, which would cause infinite recursion.
        if (empty($teachername)) {
            $teachername = get_string('tutorname_default', 'local_dttutor');
        }

        // Load complete user record to ensure all fields required by fullname() are present.
        $userrecord = $DB->get_record('user', ['id' => $USER->id],
            'id, firstname, lastname, firstnamephonetic, lastnamephonetic, middlename, alternatename');

        // Prepare replacement array.
        $replacements = [
            '{teachername}' => $teachername,
            '{coursename}' => $COURSE->fullname ?? '',
            '{username}' => $userrecord ? fullname($userrecord) : fullname($USER),
            '{firstname}' => $USER->firstname,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
}
