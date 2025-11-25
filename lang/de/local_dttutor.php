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

$string['avatar'] = 'Tutor-IA Avatar';
$string['avatar_desc'] = 'Wählen Sie den Avatar aus, der auf der schwebenden Chat-Schaltfläche des Tutor-IA angezeigt werden soll. Wenn keiner ausgewählt ist oder die Datei nicht existiert, wird standardmäßig Avatar 1 verwendet.';
$string['avatar_position'] = 'Avatar-Position';
$string['avatar_position_desc'] = 'Konfigurieren Sie, wo die schwebende Avatar-Schaltfläche des Tutor-IA angezeigt wird. Wählen Sie eine vordefinierte Eckenposition oder passen Sie die genauen X,Y-Koordinaten an. Die Live-Vorschau zeigt, wie es erscheinen wird.';
$string['cachedef_sessions'] = 'Cache für Tutor-IA Chat-Sitzungen';
$string['clear_selection'] = 'Auswahl löschen';
$string['close'] = 'Tutor IA schließen';
$string['configure_now'] = 'Jetzt konfigurieren';
$string['custom_prompt'] = 'Benutzerdefinierte Eingabeaufforderung';
$string['custom_prompt_desc'] = 'Benutzerdefinierte Anweisungen zur Steuerung des KI-Tutor-Verhaltens. Verwenden Sie dieses Feld, um spezifische Richtlinien, Ton oder Wissensgrenzen für den Tutor bereitzustellen.';
$string['customavatar'] = 'Benutzerdefinierter Avatar';
$string['customavatar_desc'] = 'Laden Sie Ihr eigenes benutzerdefiniertes Avatar-Bild hoch. Dies überschreibt den ausgewählten vordefinierten Avatar.';
$string['customavatar_dimensions'] = 'Empfohlene Abmessungen: 200x200 Pixel. Unterstützte Formate: PNG, JPG, JPEG, SVG. Maximale Dateigröße: 512KB.';
$string['debug_force_reindex'] = 'Kontext neu indizieren erzwingen';
$string['debug_mode'] = 'Debug-Modus';
$string['debug_mode_desc'] = 'Debug-Optionen in der Chat-Oberfläche aktivieren. Wenn aktiviert, sehen Benutzer zusätzliche Debug-Steuerelemente wie das Kontrollkästchen zum erzwungenen Neuindizieren.';
$string['drawer_side'] = 'Öffnungsseite des Schubfachs';
$string['drawer_side_help'] = 'Wählen Sie, von welcher Seite das Chat-Schubfach geöffnet wird. Dies ist unabhängig von der Position der Avatar-Schaltfläche.';
$string['drawer_side_left'] = 'Von links öffnen';
$string['drawer_side_right'] = 'Von rechts öffnen';
$string['dttutor:use'] = 'Tutor-IA verwenden';
$string['enabled'] = 'Chat aktivieren';
$string['enabled_desc'] = 'Tutor-IA Chat global aktivieren oder deaktivieren';
$string['error_api_not_configured'] = 'API-Konfiguration fehlt. Bitte überprüfen Sie Ihre Einstellungen.';
$string['error_api_request_failed'] = 'API-Anfragefehler: {$a}';
$string['error_cache_unavailable'] = 'Der Chat-Dienst ist vorübergehend nicht verfügbar. Bitte versuchen Sie, die Seite zu aktualisieren.';
$string['error_empty_message'] = 'Die Nachricht darf nicht leer sein';
$string['error_http_code'] = 'HTTP-Fehler {$a}';
$string['error_insufficient_tokens'] = 'Es sind nicht genügend KI-Credits verfügbar, um Ihre Anfrage zu bearbeiten. Bitte kontaktieren Sie Ihren Administrator, um weitere Credits hinzuzufügen und den KI-Tutor weiter zu nutzen.';
$string['error_insufficient_tokens_short'] = 'Unzureichende Credits';
$string['error_license_not_allowed'] = 'Ihre Website-Lizenz erlaubt keinen Zugriff auf den KI-Tutor-Dienst. Bitte kontaktieren Sie Ihren Administrator, um Ihren Lizenzstatus zu überprüfen oder Ihren Plan zu aktualisieren.';
$string['error_license_not_allowed_short'] = 'Lizenzfehler';
$string['error_invalid_api_response'] = 'Ungültige API-Antwort';
$string['error_invalid_coordinates'] = 'Ungültige Koordinaten. Bitte verwenden Sie gültige CSS-Werte (z.B. 10px, 2rem, 50%)';
$string['error_invalid_message'] = 'Bitte geben Sie eine gültige Nachricht ein';
$string['error_invalid_position'] = 'Ungültige Positionsdaten';
$string['error_metadata_too_large'] = 'Die mit Ihrer Nachricht gesendeten Metadaten sind zu groß. Bitte versuchen Sie es erneut.';
$string['error_no_credits'] = 'Unzureichende KI-Credits verfügbar.';
$string['error_no_credits_short'] = 'Keine Credits Verfügbar';
$string['error_selected_text_too_large'] = 'Der ausgewählte Text ist zu groß. Bitte wählen Sie einen kleineren Abschnitt.';
$string['error_webservice_not_configured'] = 'Der KI-Tutor-Chat ist nicht richtig konfiguriert und ist derzeit nicht verfügbar.';
$string['error_webservice_not_configured_action'] = 'Bitte kontaktieren Sie Ihren Website-Administrator oder melden Sie dieses Problem, um den Chat-Service zu aktivieren.';
$string['error_webservice_not_configured_admin'] = 'Der Webservice des Datacurso KI-Providers muss konfiguriert werden, bevor der KI-Tutor verwendet werden kann. <a href="{$a}" target="_blank">Klicken Sie hier, um ihn jetzt zu konfigurieren</a>.';
$string['error_webservice_not_configured_admin_inline'] = 'Der Webservice des Datacurso KI-Providers muss konfiguriert werden, bevor der KI-Tutor verwendet werden kann.';
$string['error_webservice_not_configured_short'] = 'Chat-Service nicht verfügbar';
$string['off_topic_detection_enabled'] = 'Off-Topic-Erkennung aktivieren';
$string['off_topic_detection_enabled_desc'] = 'Wenn aktiviert, erkennt der KI-Tutor Off-Topic-Nachrichten und reagiert entsprechend der unten konfigurierten Strenge.';
$string['off_topic_strictness'] = 'Off-Topic-Strenge';
$string['off_topic_strictness_desc'] = 'Kontrollieren Sie, wie streng die Off-Topic-Erkennung ist. Permissiv erlaubt mehr Flexibilität, während streng nur kursbezogene Gespräche erzwingt.';
$string['off_topic_strictness_moderate'] = 'Moderat';
$string['off_topic_strictness_permissive'] = 'Permissiv';
$string['off_topic_strictness_strict'] = 'Streng';
$string['open'] = 'Tutor IA öffnen';
$string['pluginname'] = 'Tutor IA';
$string['position_custom'] = 'Benutzerdefinierte Position';
$string['position_left'] = 'Untere linke Ecke';
$string['position_preset'] = 'Vordefinierte Position';
$string['position_right'] = 'Untere rechte Ecke';
$string['position_x'] = 'Horizontale Position (X)';
$string['position_x_help'] = 'Abstand vom linken Rand. Beispiele: 2rem, 20px, 5%. Verwenden Sie negative Werte, um vom rechten Rand zu positionieren.';
$string['position_y'] = 'Vertikale Position (Y)';
$string['position_y_help'] = 'Abstand vom unteren Rand. Beispiele: 6rem, 80px, 10%. Verwenden Sie negative Werte, um vom oberen Rand zu positionieren.';
$string['preview'] = 'Live-Vorschau';
$string["ref_bottom"] = "Unten";
$string["ref_left"] = "Links";
$string["ref_right"] = "Rechts";
$string["ref_top"] = "Oben";
$string["reference_edge_x"] = "Horizontale Referenzkante";
$string["reference_edge_y"] = "Vertikale Referenzkante";
$string['selection_indicator'] = '{$a} Zeilen ausgewählt';
$string['sendmessage'] = 'Nachricht senden';
$string['sessionnotready'] = 'Die Tutor-IA Sitzung ist nicht bereit. Bitte versuchen Sie es erneut.';
$string['student'] = 'Student';
$string['teacher'] = 'Lehrer';
$string['tutorcustomization'] = 'Tutor-Anpassung';
$string['tutorname_default'] = 'KI-Tutor';
$string['tutorname_setting'] = 'Tutor-Name';
$string['tutorname_setting_desc'] = 'Konfigurieren Sie den Namen, der im Chat-Header angezeigt wird. Sie können {teachername} verwenden, um den tatsächlichen Lehrernamen aus dem Kurs anzuzeigen, oder einen benutzerdefinierten Namen eingeben. Beispiele: "{teachername}" zeigt "Max Müller", "KI-Assistent" zeigt "KI-Assistent".';
$string['typemessage'] = 'Geben Sie Ihre Nachricht ein...';
$string['unauthorized'] = 'Unbefugter Zugriff';
$string['welcomemessage'] = 'Hallo! Ich bin Ihr KI-Assistent. Wie kann ich Ihnen heute helfen?';
$string['welcomemessage_default'] = 'Hallo! Ich bin {teachername}, Ihr KI-Assistent. Wie kann ich Ihnen heute helfen?';
$string['welcomemessage_setting'] = 'Willkommensnachricht';
$string['welcomemessage_setting_desc'] = 'Passen Sie die Willkommensnachricht an, die beim Öffnen des Chats angezeigt wird. Sie können Platzhalter verwenden: {teachername}, {coursename}, {username}, {firstname}';
