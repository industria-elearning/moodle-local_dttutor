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
 * English language strings for Tutor-IA plugin.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apitoken'] = 'Authentication token';
$string['apitoken_desc'] = 'Authentication token for the Tutor-AI API';
$string['apiurl'] = 'Tutor-AI API URL';
$string['apiurl_desc'] = 'Base URL of the Tutor-AI API (e.g., https://plugins-ai-dev.datacurso.com)';
$string['avatar'] = 'Tutor-AI avatar';
$string['avatar_desc'] = 'Select the avatar to display on the Tutor-AI floating chat button. If none is selected or the file does not exist, Avatar 1 will be used by default.';
$string['avatar_position'] = 'Avatar position';
$string['avatar_position_desc'] = 'Select the corner where the Tutor-AI floating chat button will be displayed. By default it appears in the bottom right corner.';
$string['cachedef_sessions'] = 'Cache for Tutor-AI chat sessions';
$string['customavatar'] = 'Custom avatar';
$string['customavatar_desc'] = 'Upload your own custom avatar image. This will override the selected predefined avatar.';
$string['customavatar_dimensions'] = 'Recommended dimensions: 200x200 pixels. Supported formats: PNG, JPG, JPEG, SVG. Maximum file size: 512KB.';
$string['close'] = 'Close Tutor AI';
$string['dttutor:use'] = 'Use Tutor-AI';
$string['enabled'] = 'Enable Chat';
$string['enabled_desc'] = 'Enable or disable the Tutor-AI chat globally';
$string['error_api_not_configured'] = 'API configuration is missing. Please check your settings.';
$string['error_api_request_failed'] = 'API request error: {$a}';
$string['error_http_code'] = 'HTTP error {$a}';
$string['error_invalid_api_response'] = 'Invalid API response';
$string['open'] = 'Open Tutor AI';
$string['pluginname'] = 'Tutor AI';
$string['position_left'] = 'Bottom left corner';
$string['position_right'] = 'Bottom right corner';
$string['sendmessage'] = 'Send message';
$string['sessionnotready'] = 'The Tutor-AI session is not ready. Please try again.';
$string['student'] = 'Student';
$string['teacher'] = 'Teacher';
$string['tutorname_default'] = 'AI Tutor';
$string['tutorname_setting'] = 'Tutor name';
$string['tutorname_setting_desc'] = 'Configure the name to display in the chat header. You can use {teachername} to show the actual teacher\'s name from the course, or enter a custom name. Examples: "{teachername}" will show "John Doe", "AI Assistant" will show "AI Assistant".';
$string['typemessage'] = 'Type your message...';
$string['unauthorized'] = 'Unauthorized access';
$string['welcomemessage'] = 'Hello! I\'m your AI assistant. How can I help you today?';
$string['welcomemessage_default'] = 'Hello! I\'m {teachername}, your AI assistant. How can I help you today?';
$string['welcomemessage_setting'] = 'Welcome message';
$string['welcomemessage_setting_desc'] = 'Customize the welcome message displayed when the chat is opened. You can use placeholders: {teachername}, {coursename}, {username}, {firstname}';
$string['welcomesettings'] = 'Welcome Message Settings';
