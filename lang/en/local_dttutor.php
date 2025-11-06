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

$string['pluginname'] = 'Tutor AI';
$string['open'] = 'Open Tutor AI';
$string['close'] = 'Close Tutor AI';
$string['sendmessage'] = 'Send message';
$string['typemessage'] = 'Type your message...';
$string['welcomemessage'] = 'Hello! I\'m your AI assistant. How can I help you today?';
$string['teacher'] = 'Teacher';
$string['student'] = 'Student';

// Settings.
$string['avatar'] = 'Tutor-AI avatar';
$string['avatar_desc'] = 'Select the avatar to display on the Tutor-AI floating chat button. If none is selected or the file does not exist, Avatar 1 will be used by default.';
$string['avatar_position'] = 'Avatar position';
$string['avatar_position_desc'] = 'Select the corner where the Tutor-AI floating chat button will be displayed. By default it appears in the bottom right corner.';
$string['position_right'] = 'Bottom right corner';
$string['position_left'] = 'Bottom left corner';
$string['apiurl'] = 'Tutor-AI API URL';
$string['apiurl_desc'] = 'Base URL of the Tutor-AI API (e.g., https://plugins-ai-dev.datacurso.com)';
$string['apitoken'] = 'Authentication token';
$string['apitoken_desc'] = 'Authentication token for the Tutor-AI API';
$string['enabled'] = 'Enable Chat';
$string['enabled_desc'] = 'Enable or disable the Tutor-AI chat globally';

// Error messages.
$string['error_api_not_configured'] = 'API configuration is missing. Please check your settings.';
$string['sessionnotready'] = 'The Tutor-AI session is not ready. Please try again.';
$string['unauthorized'] = 'Unauthorized access';
$string['error_api_request_failed'] = 'API request error: {$a}';
$string['error_http_code'] = 'HTTP error {$a}';
$string['error_invalid_api_response'] = 'Invalid API response';

// Cache.
$string['cachedef_sessions'] = 'Cache for Tutor-AI chat sessions';

// Capabilities.
$string['dttutor:use'] = 'Use Tutor-AI';

// AI Mode.
$string['aimode_button'] = 'AI Mode';
$string['aimode_settings'] = 'AI Mode Settings';
$string['aimode_settings_desc'] = 'Configure the AI Mode fullscreen modal that appears in the navbar';
$string['aimode_enabled'] = 'Enable AI Mode';
$string['aimode_enabled_desc'] = 'Show the AI Mode button in the site navigation bar. Users can click it to open a fullscreen AI chat interface.';
$string['aimode_welcome'] = 'Welcome Message';
$string['aimode_welcome_desc'] = 'Greeting message shown when AI Mode modal opens. Available placeholders: {username} (full name), {firstname}, {lastname}, {email}, {sitename}, {coursename}';
$string['aimode_quick_options'] = 'Quick Start Options';
$string['aimode_quick_options_desc'] = 'JSON array of quick start buttons. Format: [{"icon":"emoji", "label":"Button Text", "prompt":"Pre-filled message"}]. Users can click these to quickly start a conversation.';
$string['aimode_placeholder'] = 'Ask AI anything...';
$string['aimode_close'] = 'Close AI Mode';

// Placeholders.
$string['placeholder_username'] = 'User\'s full name';
$string['placeholder_firstname'] = 'User\'s first name';
$string['placeholder_lastname'] = 'User\'s last name';
$string['placeholder_email'] = 'User\'s email address';
$string['placeholder_sitename'] = 'Site name';
$string['placeholder_coursename'] = 'Current course name';

// Quick Options Manager.
$string['quickoption_icon'] = 'Icon';
$string['quickoption_label'] = 'Label';
$string['quickoption_prompt'] = 'Prompt';
$string['quickoption_actions'] = 'Actions';
$string['quickoption_add'] = 'Add';
$string['quickoption_icon_help'] = 'Emoji or symbol';
$string['quickoption_add_new'] = 'Add New Quick Option';
$string['quickoption_help_title'] = 'How to use:';
$string['quickoption_help_text'] = 'Use the table above to manage your quick start options. You can add, delete, and reorder options. Each option requires an icon (emoji), a label (button text), and a prompt (pre-filled message). Users will see these as clickable buttons when they open AI Mode.';
$string['quickoption_validation_error'] = 'Please fill in all fields (icon, label, and prompt)';
$string['quickoption_delete_confirm'] = 'Are you sure you want to delete this option?';
$string['error_invalid_json'] = 'Invalid JSON format';
