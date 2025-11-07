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
 * Portuguese language strings for Tutor-IA plugin.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apitoken'] = 'Token de autenticação';
$string['apitoken_desc'] = 'Token de autenticação para a API Tutor-IA';
$string['apiurl'] = 'URL da API Tutor-IA';
$string['apiurl_desc'] = 'URL base da API Tutor-IA (ex: https://plugins-ai-dev.datacurso.com)';
$string['avatar'] = 'Avatar do Tutor-IA';
$string['avatar_desc'] = 'Selecione o avatar a ser exibido no botão flutuante do chat Tutor-IA. Se nenhum for selecionado ou o arquivo não existir, o Avatar 1 será usado por padrão.';
$string['avatar_position'] = 'Posição do avatar';
$string['avatar_position_desc'] = 'Selecione o canto onde o botão flutuante do chat Tutor-IA será exibido. Por padrão, aparece no canto inferior direito.';
$string['cachedef_sessions'] = 'Cache para sessões de chat do Tutor-IA';
$string['close'] = 'Fechar Tutor IA';
$string['customavatar'] = 'Avatar personalizado';
$string['customavatar_desc'] = 'Faça upload da sua própria imagem de avatar personalizada. Isso substituirá o avatar predefinido selecionado.';
$string['customavatar_dimensions'] = 'Dimensões recomendadas: 200x200 pixels. Formatos suportados: PNG, JPG, JPEG, SVG. Tamanho máximo do arquivo: 512KB.';
$string['dttutor:use'] = 'Usar Tutor-IA';
$string['enabled'] = 'Ativar chat';
$string['enabled_desc'] = 'Ativar ou desativar o chat Tutor-IA globalmente';
$string['error_api_not_configured'] = 'A configuração da API está ausente. Por favor, verifique suas configurações.';
$string['error_api_request_failed'] = 'Erro na solicitação da API: {$a}';
$string['error_http_code'] = 'Erro HTTP {$a}';
$string['error_invalid_api_response'] = 'Resposta da API inválida';
$string['open'] = 'Abrir Tutor IA';
$string['pluginname'] = 'Tutor IA';
$string['position_left'] = 'Canto inferior esquerdo';
$string['position_right'] = 'Canto inferior direito';
$string['sendmessage'] = 'Enviar mensagem';
$string['sessionnotready'] = 'A sessão do Tutor-IA não está pronta. Por favor, tente novamente.';
$string['student'] = 'Estudante';
$string['teacher'] = 'Professor';
$string['teachername_default'] = 'Tutor IA';
$string['teachername_setting'] = 'Nome do professor padrão';
$string['teachername_setting_desc'] = 'Nome padrão a usar quando nenhum professor é encontrado no curso. Será usado no cabeçalho do chat e pode ser referenciado usando o placeholder {teachername}.';
$string['typemessage'] = 'Digite sua mensagem...';
$string['unauthorized'] = 'Acesso não autorizado';
$string['welcomemessage'] = 'Olá! Eu sou seu assistente de IA. Como posso ajudá-lo hoje?';
$string['welcomemessage_default'] = 'Olá! Eu sou {teachername}, seu assistente de IA. Como posso ajudá-lo hoje?';
$string['welcomemessage_setting'] = 'Mensagem de boas-vindas';
$string['welcomemessage_setting_desc'] = 'Personalize a mensagem de boas-vindas exibida ao abrir o chat. Você pode usar placeholders: {teachername}, {coursename}, {username}, {firstname}';
$string['welcomesettings'] = 'Configurações da mensagem de boas-vindas';
