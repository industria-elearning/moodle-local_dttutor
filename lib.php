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

defined('MOODLE_INTERNAL') || die();

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

    // Check the contextlevel is as expected - system context only.
    if ($context->contextlevel != CONTEXT_SYSTEM) {
        return false;
    }

    // Make sure the filearea is one that we handle.
    if ($filearea !== 'customavatar') {
        return false;
    }

    // The itemid is always 0 for custom avatar.
    $itemid = array_shift($args);

    // Extract the filename from the remaining args.
    $filename = array_pop($args);

    // Build the filepath.
    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    // Retrieve the file from the files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'local_dttutor', $filearea, $itemid, $filepath, $filename);

    if (!$file || $file->is_directory()) {
        return false;
    }

    // Send the file with caching headers (cache for 1 day).
    // No force download - display inline in browser.
    send_stored_file($file, 86400, 0, false, $options);
}
