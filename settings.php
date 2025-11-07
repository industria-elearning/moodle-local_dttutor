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
 * Settings for the Tutor-IA plugin.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Load custom admin settings.
require_once($CFG->dirroot . '/local/dttutor/classes/admin_settings/admin_setting_avatar_selector.php');
require_once($CFG->dirroot . '/local/dttutor/classes/admin_settings/admin_setting_custom_avatar.php');

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_dttutor', get_string('pluginname', 'local_dttutor'));
    $ADMIN->add('localplugins', $settings);

    // API Settings Section.
    $settings->add(new admin_setting_heading(
        'local_dttutor/apisettings',
        get_string('apiurl', 'local_dttutor'),
        ''
    ));

    // API URL.
    $settings->add(
        new admin_setting_configtext(
            'local_dttutor/apiurl',
            get_string('apiurl', 'local_dttutor'),
            get_string('apiurl_desc', 'local_dttutor'),
            'https://plugins-ai-dev.datacurso.com',
            PARAM_URL
        )
    );

    // API Token.
    $settings->add(
        new admin_setting_configpasswordunmask(
            'local_dttutor/apitoken',
            get_string('apitoken', 'local_dttutor'),
            get_string('apitoken_desc', 'local_dttutor'),
            ''
        )
    );

    // Enable/Disable Chat.
    $settings->add(
        new admin_setting_configcheckbox(
            'local_dttutor/enabled',
            get_string('enabled', 'local_dttutor'),
            get_string('enabled_desc', 'local_dttutor'),
            '1'
        )
    );

    // Avatar Selection.
    $settings->add(new admin_setting_heading(
        'local_dttutor/avatarsettings',
        get_string('avatar', 'local_dttutor'),
        ''
    ));

    // Visual avatar selector with previews.
    $settings->add(
        new \local_dttutor\admin_settings\admin_setting_avatar_selector(
            'local_dttutor/avatar',
            get_string('avatar', 'local_dttutor'),
            get_string('avatar_desc', 'local_dttutor'),
            '01'
        )
    );

    // Custom avatar upload.
    $settings->add(
        new \local_dttutor\admin_settings\admin_setting_custom_avatar(
            'local_dttutor/customavatar',
            get_string('customavatar', 'local_dttutor'),
            get_string('customavatar_desc', 'local_dttutor')
        )
    );

    // Avatar Position.
    $settings->add(
        new admin_setting_configselect(
            'local_dttutor/avatar_position',
            get_string('avatar_position', 'local_dttutor'),
            get_string('avatar_position_desc', 'local_dttutor'),
            'right',
            [
                'right' => get_string('position_right', 'local_dttutor'),
                'left' => get_string('position_left', 'local_dttutor'),
            ]
        )
    );

    // Welcome Message Section.
    $settings->add(new admin_setting_heading(
        'local_dttutor/welcomesettings',
        get_string('welcomesettings', 'local_dttutor'),
        ''
    ));

    // Welcome Message with Placeholder Support.
    $settings->add(
        new admin_setting_configtextarea(
            'local_dttutor/welcomemessage',
            get_string('welcomemessage_setting', 'local_dttutor'),
            get_string('welcomemessage_setting_desc', 'local_dttutor'),
            get_string('welcomemessage_default', 'local_dttutor'),
            PARAM_TEXT
        )
    );

    // Tutor Name Display.
    $settings->add(
        new admin_setting_configtext(
            'local_dttutor/tutorname',
            get_string('tutorname_setting', 'local_dttutor'),
            get_string('tutorname_setting_desc', 'local_dttutor'),
            get_string('tutorname_default', 'local_dttutor'),
            PARAM_TEXT
        )
    );
}
