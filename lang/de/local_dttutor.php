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
 * German language strings for Tutor-IA plugin.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Settings.
$string['apitoken'] = 'Authentifizierungs-Token';
$string['apitoken_desc'] = 'Authentifizierungs-Token für die Tutor-IA API';
$string['apiurl'] = 'Tutor-IA API-URL';
$string['apiurl_desc'] = 'Basis-URL der Tutor-IA API (z.B. https://plugins-ai-dev.datacurso.com)';
$string['avatar'] = 'Tutor-IA Avatar';
$string['avatar_desc'] = 'Wählen Sie den Avatar aus, der auf der schwebenden Chat-Schaltfläche des Tutor-IA angezeigt werden soll. Wenn keiner ausgewählt ist oder die Datei nicht existiert, wird standardmäßig Avatar 1 verwendet.';
$string['avatar_position'] = 'Avatar-Position';
$string['avatar_position_desc'] = 'Wählen Sie die Ecke aus, in der die schwebende Chat-Schaltfläche des Tutor-IA angezeigt werden soll. Standardmäßig wird sie in der unteren rechten Ecke angezeigt.';

// Cache.
$string['cachedef_sessions'] = 'Cache für Tutor-IA Chat-Sitzungen';

// UI strings.
$string['close'] = 'Tutor IA schließen';

// Capabilities.
$string['dttutor:use'] = 'Tutor-IA verwenden';

$string['enabled'] = 'Chat aktivieren';
$string['enabled_desc'] = 'Tutor-IA Chat global aktivieren oder deaktivieren';

// Error messages.
$string['error_api_not_configured'] = 'API-Konfiguration fehlt. Bitte überprüfen Sie Ihre Einstellungen.';
$string['error_api_request_failed'] = 'API-Anfragefehler: {$a}';
$string['error_http_code'] = 'HTTP-Fehler {$a}';
$string['error_invalid_api_response'] = 'Ungültige API-Antwort';

$string['open'] = 'Tutor IA öffnen';
$string['pluginname'] = 'Tutor IA';
$string['position_left'] = 'Untere linke Ecke';
$string['position_right'] = 'Untere rechte Ecke';
$string['sendmessage'] = 'Nachricht senden';
$string['sessionnotready'] = 'Die Tutor-IA Sitzung ist nicht bereit. Bitte versuchen Sie es erneut.';
$string['student'] = 'Student';
$string['teacher'] = 'Lehrer';
$string['typemessage'] = 'Geben Sie Ihre Nachricht ein...';
$string['unauthorized'] = 'Unbefugter Zugriff';
$string['welcomemessage'] = 'Hallo! Ich bin Ihr KI-Assistent. Wie kann ich Ihnen heute helfen?';
