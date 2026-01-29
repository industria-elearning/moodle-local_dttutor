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
 * Library functions for Tutor-IA plugin
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extend course navigation with AI Tutor Management link
 *
 * @param navigation_node $parentnode The parent node
 * @param stdClass $course The course object
 * @param context_course $context The course context
 * @return void
 */
function local_dttutor_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    // Only show for teachers/managers.
    if (!has_capability('moodle/course:update', $context)) {
        return;
    }

    // Only if plugin is enabled.
    if (!get_config('local_dttutor', 'enabled')) {
        return;
    }

    $url = new moodle_url('/local/dttutor/manage.php', ['id' => $course->id]);
    $node = navigation_node::create(
        get_string('manage_tutor', 'local_dttutor'),
        $url,
        navigation_node::TYPE_SETTING,
        null,
        'dttutormanage',
        new pix_icon('i/settings', '')
    );

    // Add to course secondary navigation (More menu).
    $parentnode->add_node($node);
}

/**
 * Serves the files from the local_dttutor file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function local_dttutor_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    global $CFG;

    // Handle custom avatar (system context).
    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'customavatar') {
        $itemid = array_shift($args);
        $filename = array_pop($args);
        $filepath = !$args ? '/' : '/' . implode('/', $args) . '/';

        $fs = get_file_storage();
        $file = $fs->get_file($context->id, 'local_dttutor', $filearea, $itemid, $filepath, $filename);

        if (!$file || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 86400, 0, false, $options);
        return;
    }

    // Handle course materials (course context).
    if ($context->contextlevel == CONTEXT_COURSE && $filearea === 'course_materials') {
        // Verify user has access to course.
        require_login($course);
        require_capability('local/dttutor:use', $context);

        $itemid = array_shift($args);
        $filename = array_pop($args);
        $filepath = !$args ? '/' : '/' . implode('/', $args) . '/';

        $fs = get_file_storage();
        $file = $fs->get_file($context->id, 'local_dttutor', $filearea, $itemid, $filepath, $filename);

        if (!$file || $file->is_directory()) {
            return false;
        }

        send_stored_file($file, 86400, 0, false, $options);
        return;
    }

    return false;
}
