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
 * Russian language strings for Tutor-IA plugin.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['avatar'] = 'Аватар Tutor-IA';
$string['avatar_desc'] = 'Выберите аватар для отображения на плавающей кнопке чата Tutor-IA. Если ничего не выбрано или файл не существует, по умолчанию будет использоваться Аватар 1.';
$string['avatar_position'] = 'Положение аватара';
$string['avatar_position_desc'] = 'Выберите угол, где будет отображаться плавающая кнопка чата Tutor-IA. По умолчанию она появляется в правом нижнем углу.';
$string['cachedef_sessions'] = 'Кэш для сеансов чата Tutor-IA';
$string['close'] = 'Закрыть Tutor IA';
$string['customavatar'] = 'Пользовательский аватар';
$string['customavatar_desc'] = 'Загрузите свое собственное изображение аватара. Это переопределит выбранный предустановленный аватар.';
$string['customavatar_dimensions'] = 'Рекомендуемые размеры: 200x200 пикселей. Поддерживаемые форматы: PNG, JPG, JPEG, SVG. Максимальный размер файла: 512KB.';
$string['dttutor:use'] = 'Использовать Tutor-IA';
$string['enabled'] = 'Включить чат';
$string['enabled_desc'] = 'Включить или отключить чат Tutor-IA глобально';
$string['error_api_not_configured'] = 'Конфигурация API отсутствует. Пожалуйста, проверьте настройки.';
$string['error_api_request_failed'] = 'Ошибка запроса API: {$a}';
$string['error_http_code'] = 'Ошибка HTTP {$a}';
$string['error_invalid_api_response'] = 'Недействительный ответ API';
$string['open'] = 'Открыть Tutor IA';
$string['pluginname'] = 'Tutor IA';
$string['position_left'] = 'Левый нижний угол';
$string['position_right'] = 'Правый нижний угол';
$string['sendmessage'] = 'Отправить сообщение';
$string['sessionnotready'] = 'Сеанс Tutor-IA не готов. Пожалуйста, попробуйте снова.';
$string['student'] = 'Студент';
$string['teacher'] = 'Преподаватель';
$string['tutorname_default'] = 'AI Репетитор';
$string['tutorname_setting'] = 'Имя репетитора';
$string['tutorname_setting_desc'] = 'Настройте имя для отображения в заголовке чата. Вы можете использовать {teachername} для отображения реального имени преподавателя курса или ввести пользовательское имя. Примеры: "{teachername}" отобразит "Иван Петров", "AI Помощник" отобразит "AI Помощник".';
$string['typemessage'] = 'Введите ваше сообщение...';
$string['unauthorized'] = 'Несанкционированный доступ';
$string['welcomemessage'] = 'Здравствуйте! Я ваш AI-помощник. Как я могу помочь вам сегодня?';
$string['welcomemessage_default'] = 'Здравствуйте! Я {teachername}, ваш AI-помощник. Как я могу помочь вам сегодня?';
$string['welcomemessage_setting'] = 'Приветственное сообщение';
$string['welcomemessage_setting_desc'] = 'Настройте приветственное сообщение, отображаемое при открытии чата. Вы можете использовать заполнители: {teachername}, {coursename}, {username}, {firstname}';
$string['welcomesettings'] = 'Настройки приветственного сообщения';
