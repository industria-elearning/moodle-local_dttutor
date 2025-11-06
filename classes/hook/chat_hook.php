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

        // Get avatar position (right/left).
        $position = get_config('local_dttutor', 'avatar_position');
        if (empty($position)) {
            $position = 'right'; // Default: right.
        }

        // Calculate bottom position dynamically based on Moodle's footer-popover.
        // The footer-popover is at bottom: 2rem, and communication at 4rem.
        // We place the avatar at 6rem to be above both.
        $bottomposition = '6rem';

        // Generate unique ID.
        $uniqid = uniqid('tia_');

        // Prepare data for templates.
        $toggledata = [
            'uniqid' => $uniqid,
            'avatarurl' => $avatarurl->out(false),
            'position' => $position,
            'bottomposition' => $bottomposition,
        ];

        $drawerdata = [
            'uniqid' => $uniqid,
            'courseid' => $courseid,
            'cmid' => $cmid,
            'userid' => $USER->id,
            'userrole' => $userroledisplay,
            'avatarurl' => $avatarurl->out(false),
            'position' => $position,
        ];

        // Render templates.
        $toggle = $OUTPUT->render_from_template('local_dttutor/tutor_ia_toggle', $toggledata);
        $drawer = $OUTPUT->render_from_template('local_dttutor/tutor_ia_drawer', $drawerdata);

        // Add HTML directly to footer using the hook.
        $hook->add_html($toggle . $drawer);
    }

    /**
     * Gets the configured avatar URL with fallback system.
     *
     * @return \moodle_url Avatar URL to use
     * @since Moodle 4.5
     */
    private static function get_avatar_url(): \moodle_url {
        global $CFG;

        // Get configuration.
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
}
