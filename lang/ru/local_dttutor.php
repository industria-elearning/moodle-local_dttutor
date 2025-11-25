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
$string['avatar_position_desc'] = 'Настройте, где будет отображаться плавающая кнопка аватара Tutor-IA. Выберите предустановленное угловое положение или настройте точные координаты X,Y. Предпросмотр в реальном времени показывает, как это будет выглядеть.';
$string['cachedef_sessions'] = 'Кэш для сеансов чата Tutor-IA';
$string['char'] = 'символ';
$string['chars'] = 'символов';
$string['clear_selection'] = 'Очистить выделение';
$string['close'] = 'Закрыть Tutor IA';
$string['configuration_error'] = 'Ошибка конфигурации';
$string['configure_now'] = 'Настроить сейчас';
$string['connection_interrupted'] = '[Соединение прервано]';
$string['custom_prompt'] = 'Пользовательский промпт';
$string['custom_prompt_desc'] = 'Пользовательские инструкции для управления поведением IA-репетитора. Используйте это поле для предоставления конкретных рекомендаций, тона или границ знаний для репетитора.';
$string['customavatar'] = 'Пользовательский аватар';
$string['customavatar_desc'] = 'Загрузите свое собственное изображение аватара. Это переопределит выбранный предустановленный аватар.';
$string['customavatar_dimensions'] = 'Рекомендуемые размеры: 200x200 пикселей. Поддерживаемые форматы: PNG, JPG, JPEG, SVG. Максимальный размер файла: 512KB.';
$string['debug_force_reindex'] = 'Принудительная переиндексация контекста';
$string['debug_mode'] = 'Режим отладки';
$string['debug_mode_desc'] = 'Включить параметры отладки в интерфейсе чата. При включении пользователи увидят дополнительные элементы управления отладкой, такие как флажок принудительной переиндексации.';
$string['drawer_side'] = 'Сторона открытия панели';
$string['drawer_side_help'] = 'Выберите, с какой стороны будет открываться панель чата. Это не зависит от положения кнопки аватара.';
$string['drawer_side_left'] = 'Открыть слева';
$string['drawer_side_right'] = 'Открыть справа';
$string['dttutor:use'] = 'Использовать Tutor-IA';
$string['enabled'] = 'Включить чат';
$string['enabled_desc'] = 'Включить или отключить чат Tutor-IA глобально';
$string['error_api_not_configured'] = 'Конфигурация API отсутствует. Пожалуйста, проверьте настройки.';
$string['error_api_request_failed'] = 'Ошибка запроса API: {$a}';
$string['error_attempt_later'] = 'Произошла ошибка. Пожалуйста, попробуйте позже.';
$string['error_cache_unavailable'] = 'Служба чата временно недоступна. Пожалуйста, попробуйте обновить страницу.';
$string['error_empty_message'] = 'Сообщение не может быть пустым';
$string['error_establish_sse_connection'] = '[Ошибка] Не удалось установить SSE-соединение';
$string['error_http_code'] = 'Ошибка HTTP {$a}';
$string['error_internal'] = 'Внутренняя ошибка: {$a}';
$string['error_insufficient_tokens'] = 'Недостаточно кредитов ИИ для обработки вашего запроса. Пожалуйста, свяжитесь с администратором, чтобы добавить больше кредитов и продолжить использование Tutor ИИ.';
$string['error_insufficient_tokens_short'] = 'Недостаточно Кредитов';
$string['error_license_not_allowed'] = 'Лицензия вашего сайта не разрешает доступ к сервису Tutor ИИ. Пожалуйста, свяжитесь с администратором для проверки статуса лицензии или обновления вашего плана.';
$string['error_license_not_allowed_short'] = 'Ошибка Лицензии';
$string['error_invalid_api_response'] = 'Недействительный ответ API';
$string['error_invalid_coordinates'] = 'Недействительные координаты. Пожалуйста, используйте допустимые значения CSS (например: 10px, 2rem, 50%)';
$string['error_invalid_message'] = 'Пожалуйста, введите действительное сообщение';
$string['error_invalid_position'] = 'Недействительные данные о положении';
$string['error_license_fallback'] = 'Ошибка лицензии: {$a}';
$string['error_license_fallback_short'] = 'Ошибка Лицензии';
$string['error_message_too_long'] = '[Ошибка] Сообщение слишком длинное. Максимум 4000 символов.';
$string['error_metadata_too_large'] = 'Метаданные, отправленные с вашим сообщением, слишком большие. Пожалуйста, попробуйте снова.';
$string['error_no_credits'] = 'Недостаточно кредитов ИИ.';
$string['error_no_credits_fallback'] = 'Недостаточно кредитов: {$a}';
$string['error_no_credits_short'] = 'Нет Доступных Кредитов';
$string['error_selected_text_too_large'] = 'Выбранный текст слишком большой. Пожалуйста, выберите меньшую часть.';
$string['error_webservice_not_configured'] = 'Чат ИИ-репетитора не настроен правильно и в настоящее время недоступен.';
$string['error_webservice_not_configured_action'] = 'Пожалуйста, свяжитесь с администратором сайта или сообщите об этой проблеме для активации службы чата.';
$string['error_webservice_not_configured_admin'] = 'Веб-сервис провайдера ИИ Datacurso должен быть настроен перед использованием ИИ-репетитора. <a href="{$a}" target="_blank">Нажмите здесь, чтобы настроить его сейчас</a>.';
$string['error_webservice_not_configured_admin_inline'] = 'Веб-сервис провайдера ИИ Datacurso должен быть настроен перед использованием ИИ-репетитора.';
$string['error_webservice_not_configured_short'] = 'Служба чата недоступна';
$string['error_unexpected'] = 'Произошла непредвиденная ошибка. Пожалуйста, попробуйте снова.';
$string['error_unknown'] = 'Произошла неизвестная ошибка. Пожалуйста, попробуйте снова.';
$string['line'] = 'строка';
$string['lines'] = 'строк';
$string['loading'] = 'Загрузка...';
$string['off_topic_detection_enabled'] = 'Включить обнаружение сообщений вне темы';
$string['off_topic_detection_enabled_desc'] = 'При включении AI-репетитор будет обнаруживать и реагировать на сообщения вне темы в соответствии с уровнем строгости, настроенным ниже.';
$string['off_topic_strictness'] = 'Строгость обнаружения вне темы';
$string['off_topic_strictness_desc'] = 'Управляйте строгостью обнаружения тем вне контекста. Разрешительный позволяет больше гибкости, в то время как строгий обеспечивает разговоры, связанные только с курсом.';
$string['off_topic_strictness_moderate'] = 'Умеренный';
$string['off_topic_strictness_permissive'] = 'Разрешительный';
$string['off_topic_strictness_strict'] = 'Строгий';
$string['open'] = 'Открыть Tutor IA';
$string['pluginname'] = 'Tutor IA';
$string['position_custom'] = 'Пользовательское положение';
$string['position_left'] = 'Левый нижний угол';
$string['position_preset'] = 'Предустановленное положение';
$string['position_right'] = 'Правый нижний угол';
$string['position_x'] = 'Горизонтальное положение (X)';
$string['position_x_help'] = 'Расстояние от левого края. Примеры: 2rem, 20px, 5%. Используйте отрицательные значения для позиционирования от правого края.';
$string['position_x_label'] = 'X: {$a->value} (от {$a->ref})';
$string['position_y'] = 'Вертикальное положение (Y)';
$string['position_y_help'] = 'Расстояние от нижнего края. Примеры: 6rem, 80px, 10%. Используйте отрицательные значения для позиционирования от верхнего края.';
$string['position_y_label'] = 'Y: {$a->value} (от {$a->ref})';
$string['positiondisplay_corner'] = 'Позиция: угол {$a->preset} | Панель: {$a->drawer}';
$string['positiondisplay_custom'] = 'Позиция: X: {$a->x}, Y: {$a->y} | Панель: {$a->drawer}';
$string['preview'] = 'Предпросмотр в реальном времени';
$string["ref_bottom"] = "Снизу";
$string["ref_left"] = "Слева";
$string["ref_right"] = "Справа";
$string["ref_top"] = "Сверху";
$string["reference_edge_x"] = "Горизонтальный опорный край";
$string["reference_edge_y"] = "Вертикальный опорный край";
$string['selected'] = 'выбрано';
$string['selection_indicator'] = '{$a} строк выбрано';
$string['selectionformat'] = '{$a->lines} {$a->linetext}, {$a->chars} {$a->chartext} выбрано';
$string['sendmessage'] = 'Отправить сообщение';
$string['sessionnotready'] = 'Сеанс Tutor-IA не готов. Пожалуйста, попробуйте снова.';
$string['student'] = 'Студент';
$string['teacher'] = 'Преподаватель';
$string['tutorcustomization'] = 'Настройка репетитора';
$string['tutorname_default'] = 'AI Репетитор';
$string['tutorname_setting'] = 'Имя репетитора';
$string['tutorname_setting_desc'] = 'Настройте имя для отображения в заголовке чата. Вы можете использовать {teachername} для отображения реального имени преподавателя курса или ввести пользовательское имя. Примеры: "{teachername}" отобразит "Иван Петров", "AI Помощник" отобразит "AI Помощник".';
$string['typemessage'] = 'Введите ваше сообщение...';
$string['unauthorized'] = 'Несанкционированный доступ';
$string['welcomemessage'] = 'Здравствуйте! Я ваш AI-помощник. Как я могу помочь вам сегодня?';
$string['welcomemessage_default'] = 'Здравствуйте! Я {teachername}, ваш AI-помощник. Как я могу помочь вам сегодня?';
$string['welcomemessage_setting'] = 'Приветственное сообщение';
$string['welcomemessage_setting_desc'] = 'Настройте приветственное сообщение, отображаемое при открытии чата. Вы можете использовать заполнители: {teachername}, {coursename}, {username}, {firstname}';
$string['yesterday'] = 'Вчера';
