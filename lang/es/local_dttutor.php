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

$string['avatar'] = 'Avatar del Tutor-IA';
$string['avatar_desc'] = 'Selecciona el avatar que se mostrará en el botón flotante del chat Tutor-IA. Si no se selecciona ninguno o el archivo no existe, se usará el Avatar 1 por defecto.';
$string['avatar_position'] = 'Posición del avatar';
$string['avatar_position_desc'] = 'Configura dónde se mostrará el botón flotante del avatar Tutor-IA. Elige una posición predefinida en las esquinas o personaliza las coordenadas X,Y exactas. La vista previa en vivo muestra cómo aparecerá.';
$string['cachedef_sessions'] = 'Caché para sesiones de chat del Tutor-IA';
$string['customavatar'] = 'Avatar personalizado';
$string['customavatar_desc'] = 'Carga tu propia imagen de avatar personalizado. Esto anulará el avatar predefinido seleccionado.';
$string['customavatar_dimensions'] = 'Dimensiones recomendadas: 200x200 píxeles. Formatos soportados: PNG, JPG, JPEG, SVG. Tamaño máximo de archivo: 512KB.';
$string['close'] = 'Cerrar Tutor IA';
$string['dttutor:use'] = 'Usar el Tutor-IA';
$string['enabled'] = 'Habilitar Chat';
$string['enabled_desc'] = 'Habilitar o deshabilitar el chat de Tutor-IA globalmente';
$string['error_api_not_configured'] = 'Falta la configuración de la API. Por favor, verifica tus ajustes.';
$string['error_api_request_failed'] = 'Error en la solicitud a la API: {$a}';
$string['error_http_code'] = 'Error HTTP {$a}';
$string['error_invalid_api_response'] = 'Respuesta inválida de la API';
$string['error_invalid_coordinates'] = 'Coordenadas inválidas. Por favor usa valores CSS válidos (ej: 10px, 2rem, 50%)';
$string['error_invalid_position'] = 'Datos de posición inválidos';
$string['open'] = 'Abrir Tutor IA';
$string['pluginname'] = 'Tutor IA';
$string['position_custom'] = 'Posición personalizada';
$string['position_left'] = 'Esquina inferior izquierda';
$string['position_preset'] = 'Posición predefinida';
$string['position_right'] = 'Esquina inferior derecha';
$string['position_x'] = 'Posición horizontal (X)';
$string['position_x_help'] = 'Distancia desde el borde izquierdo. Ejemplos: 2rem, 20px, 5%. Usa valores negativos para posicionar desde el borde derecho.';
$string['position_y'] = 'Posición vertical (Y)';
$string['position_y_help'] = 'Distancia desde el borde inferior. Ejemplos: 6rem, 80px, 10%. Usa valores negativos para posicionar desde el borde superior.';
$string['preview'] = 'Vista Previa en Vivo';
$string['sendmessage'] = 'Enviar mensaje';
$string['sessionnotready'] = 'La sesión de Tutor-IA no está lista. Por favor intenta nuevamente.';
$string['student'] = 'Estudiante';
$string['teacher'] = 'Profesor';
$string['tutorname_default'] = 'Tutor IA';
$string['tutorname_setting'] = 'Nombre del tutor';
$string['tutorname_setting_desc'] = 'Configura el nombre a mostrar en el encabezado del chat. Puedes usar {teachername} para mostrar el nombre real del profesor del curso, o ingresar un nombre personalizado. Ejemplos: "{teachername}" mostrará "Juan Pérez", "Asistente IA" mostrará "Asistente IA".';
$string['typemessage'] = 'Escribe tu mensaje...';
$string['unauthorized'] = 'Acceso no autorizado';
$string['welcomemessage'] = '¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?';
$string['welcomemessage_default'] = '¡Hola! Soy {teachername}, tu asistente de IA. ¿En qué puedo ayudarte hoy?';
$string['welcomemessage_setting'] = 'Mensaje de bienvenida';
$string['welcomemessage_setting_desc'] = 'Personaliza el mensaje de bienvenida que se muestra al abrir el chat. Puedes usar marcadores: {teachername}, {coursename}, {username}, {firstname}';
$string['welcomesettings'] = 'Configuración del mensaje de bienvenida';
