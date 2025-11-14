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

$string['avatar'] = 'Avatar Tutor-IA';
$string['avatar_desc'] = 'Sélectionnez l\'avatar à afficher sur le bouton flottant du chat Tutor-IA. Si aucun n\'est sélectionné ou si le fichier n\'existe pas, l\'Avatar 1 sera utilisé par défaut.';
$string['avatar_position'] = 'Position de l\'avatar';
$string['avatar_position_desc'] = 'Configurez où le bouton flottant de l\'avatar Tutor-IA sera affiché. Choisissez une position de coin prédéfinie ou personnalisez les coordonnées X,Y exactes. L\'aperçu en direct montre comment il apparaîtra.';
$string['cachedef_sessions'] = 'Cache pour les sessions de chat Tutor-IA';
$string['close'] = 'Fermer Tutor IA';
$string['configure_now'] = 'Configurer maintenant';
$string['custom_prompt'] = 'Invite personnalisée';
$string['custom_prompt_desc'] = 'Instructions personnalisées pour contrôler le comportement du tuteur IA. Utilisez ce champ pour fournir des directives spécifiques, le ton ou les limites de connaissances pour le tuteur.';
$string['customavatar'] = 'Avatar personnalisé';
$string['customavatar_desc'] = 'Téléchargez votre propre image d\'avatar personnalisée. Cela remplacera l\'avatar prédéfini sélectionné.';
$string['customavatar_dimensions'] = 'Dimensions recommandées: 200x200 pixels. Formats pris en charge: PNG, JPG, JPEG, SVG. Taille maximale du fichier: 512KB.';
$string['drawer_side'] = 'Côté d\'ouverture du tiroir';
$string['drawer_side_help'] = 'Choisissez de quel côté le tiroir de chat s\'ouvrira. Ceci est indépendant de la position du bouton avatar.';
$string['drawer_side_left'] = 'Ouvrir depuis la gauche';
$string['drawer_side_right'] = 'Ouvrir depuis la droite';
$string['dttutor:use'] = 'Utiliser Tutor-IA';
$string['enabled'] = 'Activer le chat';
$string['enabled_desc'] = 'Activer ou désactiver le chat Tutor-IA globalement';
$string['error_api_not_configured'] = 'La configuration de l\'API est manquante. Veuillez vérifier vos paramètres.';
$string['error_api_request_failed'] = 'Erreur de requête API: {$a}';
$string['error_cache_unavailable'] = 'Le service de chat est temporairement indisponible. Veuillez essayer de rafraîchir la page.';
$string['error_empty_message'] = 'Le message ne peut pas être vide';
$string['error_http_code'] = 'Erreur HTTP {$a}';
$string['error_invalid_api_response'] = 'Réponse API invalide';
$string['error_invalid_coordinates'] = 'Coordonnées invalides. Veuillez utiliser des valeurs CSS valides (ex: 10px, 2rem, 50%)';
$string['error_invalid_message'] = 'Veuillez entrer un message valide';
$string['error_invalid_position'] = 'Données de position invalides';
$string['error_no_credits'] = 'Crédits IA insuffisants disponibles.';
$string['error_no_credits_short'] = 'Aucun Crédit Disponible';
$string['error_webservice_not_configured'] = 'Le chat du Tuteur IA n\'est pas correctement configuré et est actuellement indisponible.';
$string['error_webservice_not_configured_action'] = 'Veuillez contacter l\'administrateur du site ou signaler ce problème pour activer le service de chat.';
$string['error_webservice_not_configured_admin'] = 'Le webservice du Fournisseur IA Datacurso doit être configuré avant d\'utiliser le Tuteur IA. ' .
    '<a href="{$a}" target="_blank">Cliquez ici pour le configurer maintenant</a>.';
$string['error_webservice_not_configured_admin_inline'] = 'Le webservice du Fournisseur IA Datacurso doit être configuré avant d\'utiliser le Tuteur IA.';
$string['error_webservice_not_configured_short'] = 'Service de chat indisponible';
$string['off_topic_detection_enabled'] = 'Activer la détection hors sujet';
$string['off_topic_detection_enabled_desc'] = 'Lorsqu\'activé, le tuteur IA détectera et répondra aux messages hors sujet selon le niveau de rigueur configuré ci-dessous.';
$string['off_topic_strictness'] = 'Rigueur hors sujet';
$string['off_topic_strictness_desc'] = 'Contrôlez la rigueur de la détection hors sujet. Permissif permet plus de flexibilité, tandis que strict impose des conversations liées uniquement au cours.';
$string['off_topic_strictness_moderate'] = 'Modéré';
$string['off_topic_strictness_permissive'] = 'Permissif';
$string['off_topic_strictness_strict'] = 'Strict';
$string['open'] = 'Ouvrir Tutor IA';
$string['pluginname'] = 'Tutor IA';
$string['position_custom'] = 'Position personnalisée';
$string['position_left'] = 'Coin inférieur gauche';
$string['position_preset'] = 'Position prédéfinie';
$string['position_right'] = 'Coin inférieur droit';
$string['position_x'] = 'Position horizontale (X)';
$string['position_x_help'] = 'Distance depuis le bord gauche. Exemples: 2rem, 20px, 5%. Utilisez des valeurs négatives pour positionner depuis le bord droit.';
$string['position_y'] = 'Position verticale (Y)';
$string['position_y_help'] = 'Distance depuis le bord inférieur. Exemples: 6rem, 80px, 10%. Utilisez des valeurs négatives pour positionner depuis le bord supérieur.';
$string['preview'] = 'Aperçu en direct';
$string["ref_bottom"] = "Bas";
$string["ref_left"] = "Gauche";
$string["ref_right"] = "Droite";
$string["ref_top"] = "Haut";
$string["reference_edge_x"] = "Bord de référence horizontal";
$string["reference_edge_y"] = "Bord de référence vertical";
$string['sendmessage'] = 'Envoyer le message';
$string['sessionnotready'] = 'La session Tutor-IA n\'est pas prête. Veuillez réessayer.';
$string['student'] = 'Étudiant';
$string['teacher'] = 'Enseignant';
$string['tutorcustomization'] = 'Personnalisation du tuteur';
$string['tutorname_default'] = 'Tuteur IA';
$string['tutorname_setting'] = 'Nom du tuteur';
$string['tutorname_setting_desc'] = 'Configurez le nom à afficher dans l\'en-tête du chat. Vous pouvez utiliser {teachername} pour afficher le nom réel de l\'enseignant du cours, ou entrer un nom personnalisé. Exemples: "{teachername}" affichera "Jean Dupont", "Assistant IA" affichera "Assistant IA".';
$string['typemessage'] = 'Tapez votre message...';
$string['unauthorized'] = 'Accès non autorisé';
$string['welcomemessage'] = 'Bonjour! Je suis votre assistant IA. Comment puis-je vous aider aujourd\'hui?';
$string['welcomemessage_default'] = 'Bonjour! Je suis {teachername}, votre assistant IA. Comment puis-je vous aider aujourd\'hui?';
$string['welcomemessage_setting'] = 'Message de bienvenue';
$string['welcomemessage_setting_desc'] = 'Personnalisez le message de bienvenue affiché lors de l\'ouverture du chat. Vous pouvez utiliser des placeholders: {teachername}, {coursename}, {username}, {firstname}';
