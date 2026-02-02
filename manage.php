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
 * AI Tutor course management page
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

use local_dttutor\course_config;

$courseid = required_param('id', PARAM_INT);

require_login($courseid);
$course = get_course($courseid);
$context = context_course::instance($courseid);

// Check capability.
require_capability('moodle/course:update', $context);

// Check plugin is enabled.
if (!get_config('local_dttutor', 'enabled')) {
    throw new moodle_exception('error_api_not_configured', 'local_dttutor');
}

// Set up page.
$PAGE->set_url('/local/dttutor/manage.php', ['id' => $courseid]);
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('manage_tutor', 'local_dttutor'));
$PAGE->set_heading($course->fullname);

// Get course configuration.
$config = course_config::get_by_course($courseid);

// Load JavaScript modules.
$PAGE->requires->js_call_amd('local_dttutor/indexing_progress', 'init', [
    $courseid,
    $config->indexing_status,
    $config->indexing_task_id ?? '',
]);
// Course materials module re-enabled for tutor toggle functionality.
$PAGE->requires->js_call_amd('local_dttutor/course_materials', 'init', [$courseid]);

// Course materials upload handler removed - see REMOVED_FEATURES.md.
/*
// Handle file upload form submission using $_FILES.
if (optional_param('savefiles', 0, PARAM_INT) && !empty($_FILES['uploadfiles']['name'])) {
    require_sesskey();

    $fs = get_file_storage();

    // Process each uploaded file.
    $uploadedcount = 0;
    foreach ($_FILES['uploadfiles']['name'] as $key => $filename) {
        if ($_FILES['uploadfiles']['error'][$key] == UPLOAD_ERR_OK) {
            $tmpfile = $_FILES['uploadfiles']['tmp_name'][$key];

            // Validate PDF.
            if (pathinfo($filename, PATHINFO_EXTENSION) !== 'pdf') {
                continue;
            }

            // Prepare file record.
            $filerecord = [
                'contextid' => $context->id,
                'component' => 'local_dttutor',
                'filearea' => 'course_materials',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => clean_filename($filename),
                'userid' => $USER->id,
            ];

            // Delete existing file with same name.
            if ($oldfile = $fs->get_file(
                $filerecord['contextid'],
                $filerecord['component'],
                $filerecord['filearea'],
                $filerecord['itemid'],
                $filerecord['filepath'],
                $filerecord['filename']
            )) {
                $oldfile->delete();
            }

            // Create file from uploaded file.
            $fs->create_file_from_pathname($filerecord, $tmpfile);
            $uploadedcount++;
        }
    }

    if ($uploadedcount > 0) {
        redirect(
            $PAGE->url,
            get_string('material_uploaded', 'local_dttutor'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
}
*/

// Course materials retrieval removed - see REMOVED_FEATURES.md.
/*
// Get uploaded materials.
$fs = get_file_storage();
$files = $fs->get_area_files(
    $context->id,
    'local_dttutor',
    'course_materials',
    0,
    'filename',
    false
);

$materials = [];
foreach ($files as $file) {
    $downloadurl = moodle_url::make_pluginfile_url(
        $file->get_contextid(),
        $file->get_component(),
        $file->get_filearea(),
        $file->get_itemid(),
        $file->get_filepath(),
        $file->get_filename()
    );

    $materials[] = [
        'filename' => $file->get_filename(),
        'filesize' => display_size($file->get_filesize()),
        'timemodified' => userdate($file->get_timemodified(), get_string('strftimedatetimeshort')),
        'download_url' => $downloadurl->out(false),
    ];
}
*/

// Determine indexing status badge class.
$statusbadgeclass = 'badge-secondary';
switch ($config->indexing_status) {
    case 'completed':
        $statusbadgeclass = 'badge-success';
        break;
    case 'running':
        $statusbadgeclass = 'badge-info';
        break;
    case 'failed':
        $statusbadgeclass = 'badge-danger';
        break;
}

// Prepare template context.
$templatecontext = [
    'courseid' => $courseid,
    'indexing_enabled' => (bool)$config->indexing_enabled,
    'indexing_status' => $config->indexing_status,
    'indexing_status_string' => get_string('indexing_' . $config->indexing_status, 'local_dttutor'),
    'indexing_status_badge_class' => $statusbadgeclass,
    'last_indexed' => $config->last_indexed_at ? userdate($config->last_indexed_at) : null,
    'has_last_indexed' => !empty($config->last_indexed_at),
    'indexing_task_id' => $config->indexing_task_id ?? '',
    'show_progress' => $config->indexing_status === 'running',
    'can_enable_tutor' => $config->indexing_status === 'completed',
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manage_tutor', 'local_dttutor'));
echo $OUTPUT->render_from_template('local_dttutor/manage_course', $templatecontext);
echo $OUTPUT->footer();
