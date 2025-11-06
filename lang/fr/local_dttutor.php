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
 * French language strings for Tutor-IA plugin.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Settings.
$string['apitoken'] = 'Jeton d\'authentification';
$string['apitoken_desc'] = 'Jeton d\'authentification pour l\'API Tutor-IA';
$string['apiurl'] = 'URL de l\'API Tutor-IA';
$string['apiurl_desc'] = 'URL de base de l\'API Tutor-IA (ex: https://plugins-ai-dev.datacurso.com)';
$string['avatar'] = 'Avatar Tutor-IA';
$string['avatar_desc'] = 'Sélectionnez l\'avatar à afficher sur le bouton flottant du chat Tutor-IA. Si aucun n\'est sélectionné ou si le fichier n\'existe pas, l\'Avatar 1 sera utilisé par défaut.';
$string['avatar_position'] = 'Position de l\'avatar';
$string['avatar_position_desc'] = 'Sélectionnez le coin où le bouton flottant du chat Tutor-IA sera affiché. Par défaut, il apparaît dans le coin inférieur droit.';

// Cache.
$string['cachedef_sessions'] = 'Cache pour les sessions de chat Tutor-IA';

// UI strings.
$string['close'] = 'Fermer Tutor IA';

// Capabilities.
$string['dttutor:use'] = 'Utiliser Tutor-IA';

$string['enabled'] = 'Activer le chat';
$string['enabled_desc'] = 'Activer ou désactiver le chat Tutor-IA globalement';

// Error messages.
$string['error_api_not_configured'] = 'La configuration de l\'API est manquante. Veuillez vérifier vos paramètres.';
$string['error_api_request_failed'] = 'Erreur de requête API: {$a}';
$string['error_http_code'] = 'Erreur HTTP {$a}';
$string['error_invalid_api_response'] = 'Réponse API invalide';

$string['open'] = 'Ouvrir Tutor IA';
$string['pluginname'] = 'Tutor IA';
$string['position_left'] = 'Coin inférieur gauche';
$string['position_right'] = 'Coin inférieur droit';
$string['sendmessage'] = 'Envoyer le message';
$string['sessionnotready'] = 'La session Tutor-IA n\'est pas prête. Veuillez réessayer.';
$string['student'] = 'Étudiant';
$string['teacher'] = 'Enseignant';
$string['typemessage'] = 'Tapez votre message...';
$string['unauthorized'] = 'Accès non autorisé';
$string['welcomemessage'] = 'Bonjour! Je suis votre assistant IA. Comment puis-je vous aider aujourd\'hui?';
