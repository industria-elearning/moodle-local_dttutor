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
 * Spanish language strings for Tutor-IA plugin.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Tutor IA';
$string['open'] = 'Abrir Tutor IA';
$string['close'] = 'Cerrar Tutor IA';
$string['sendmessage'] = 'Enviar mensaje';
$string['typemessage'] = 'Escribe tu mensaje...';
$string['welcomemessage'] = '¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?';
$string['teacher'] = 'Profesor';
$string['student'] = 'Estudiante';

// Settings.
$string['avatar'] = 'Avatar del Tutor-IA';
$string['avatar_desc'] = 'Selecciona el avatar que se mostrará en el botón flotante del chat Tutor-IA. Si no se selecciona ninguno o el archivo no existe, se usará el Avatar 1 por defecto.';
$string['avatar_position'] = 'Posición del avatar';
$string['avatar_position_desc'] = 'Selecciona la esquina donde se mostrará el botón flotante del chat Tutor-IA. Por defecto aparece en la esquina inferior derecha.';
$string['position_right'] = 'Esquina inferior derecha';
$string['position_left'] = 'Esquina inferior izquierda';
$string['apiurl'] = 'URL de API Tutor-IA';
$string['apiurl_desc'] = 'URL base de la API de Tutor-IA (ej: https://plugins-ai-dev.datacurso.com)';
$string['apitoken'] = 'Token de autenticación';
$string['apitoken_desc'] = 'Token de autenticación para la API de Tutor-IA';
$string['enabled'] = 'Habilitar Chat';
$string['enabled_desc'] = 'Habilitar o deshabilitar el chat de Tutor-IA globalmente';

// Error messages.
$string['error_api_not_configured'] = 'Falta la configuración de la API. Por favor, verifica tus ajustes.';
$string['sessionnotready'] = 'La sesión de Tutor-IA no está lista. Por favor intenta nuevamente.';
$string['unauthorized'] = 'Acceso no autorizado';
$string['error_api_request_failed'] = 'Error en la solicitud a la API: {$a}';
$string['error_http_code'] = 'Error HTTP {$a}';
$string['error_invalid_api_response'] = 'Respuesta inválida de la API';

// Cache.
$string['cachedef_sessions'] = 'Caché para sesiones de chat del Tutor-IA';

// Capabilities.
$string['dttutor:use'] = 'Usar el Tutor-IA';

// AI Mode.
$string['aimode_button'] = 'Modo IA';
$string['aimode_settings'] = 'Configuración del Modo IA';
$string['aimode_settings_desc'] = 'Configurar el modal de pantalla completa del Modo IA que aparece en la barra de navegación';
$string['aimode_enabled'] = 'Habilitar Modo IA';
$string['aimode_enabled_desc'] = 'Mostrar el botón de Modo IA en la barra de navegación del sitio. Los usuarios pueden hacer clic para abrir una interfaz de chat IA de pantalla completa.';
$string['aimode_welcome'] = 'Mensaje de Bienvenida';
$string['aimode_welcome_desc'] = 'Mensaje de saludo mostrado cuando se abre el modal del Modo IA. Marcadores disponibles: {username} (nombre completo), {firstname}, {lastname}, {email}, {sitename}, {coursename}';
$string['aimode_quick_options'] = 'Opciones de Inicio Rápido';
$string['aimode_quick_options_desc'] = 'Array JSON de botones de inicio rápido. Formato: [{"icon":"emoji", "label":"Texto del Botón", "prompt":"Mensaje prellenado"}]. Los usuarios pueden hacer clic en estos para iniciar rápidamente una conversación.';
$string['aimode_placeholder'] = 'Pregúntale a la IA cualquier cosa...';
$string['aimode_close'] = 'Cerrar Modo IA';

// Placeholders.
$string['placeholder_username'] = 'Nombre completo del usuario';
$string['placeholder_firstname'] = 'Nombre del usuario';
$string['placeholder_lastname'] = 'Apellido del usuario';
$string['placeholder_email'] = 'Correo electrónico del usuario';
$string['placeholder_sitename'] = 'Nombre del sitio';
$string['placeholder_coursename'] = 'Nombre del curso actual';

// Quick Options Manager.
$string['quickoption_icon'] = 'Ícono';
$string['quickoption_label'] = 'Etiqueta';
$string['quickoption_prompt'] = 'Mensaje';
$string['quickoption_icon_help'] = 'Emoji o símbolo';
$string['quickoption_add_new'] = 'Agregar Nueva Opción Rápida';
$string['quickoption_help_title'] = 'Cómo usar:';
$string['quickoption_help_text'] = 'Use la tabla de arriba para gestionar sus opciones de inicio rápido. Puede agregar, eliminar y reordenar opciones. Cada opción requiere un ícono (emoji), una etiqueta (texto del botón) y un mensaje (mensaje prellenado). Los usuarios verán estos como botones clicables cuando abran el Modo IA.';
$string['quickoption_validation_error'] = 'Por favor complete todos los campos (ícono, etiqueta y mensaje)';
$string['quickoption_delete_confirm'] = '¿Está seguro de que desea eliminar esta opción?';
$string['error_invalid_json'] = 'Formato JSON inválido';
