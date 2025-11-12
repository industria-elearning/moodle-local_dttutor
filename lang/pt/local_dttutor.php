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

$string['avatar'] = 'Avatar do Tutor-IA';
$string['avatar_desc'] = 'Selecione o avatar a ser exibido no botão flutuante do chat Tutor-IA. Se nenhum for selecionado ou o arquivo não existir, o Avatar 1 será usado por padrão.';
$string['avatar_position'] = 'Posição do avatar';
$string['avatar_position_desc'] = 'Configure onde o botão flutuante do avatar Tutor-IA será exibido. Escolha uma posição de canto predefinida ou personalize as coordenadas X,Y exatas. A visualização ao vivo mostra como aparecerá.';
$string['cachedef_sessions'] = 'Cache para sessões de chat do Tutor-IA';
$string['close'] = 'Fechar Tutor IA';
$string['custom_prompt'] = 'Prompt personalizado';
$string['custom_prompt_desc'] = 'Instruções personalizadas para controlar o comportamento do tutor IA. Use este campo para fornecer diretrizes específicas, tom ou limites de conhecimento para o tutor.';
$string['customavatar'] = 'Avatar personalizado';
$string['customavatar_desc'] = 'Faça upload da sua própria imagem de avatar personalizada. Isso substituirá o avatar predefinido selecionado.';
$string['customavatar_dimensions'] = 'Dimensões recomendadas: 200x200 pixels. Formatos suportados: PNG, JPG, JPEG, SVG. Tamanho máximo do arquivo: 512KB.';
$string['drawer_side'] = 'Lado de abertura da gaveta';
$string['drawer_side_help'] = 'Escolha de qual lado a gaveta de chat será aberta. Isso é independente da posição do botão do avatar.';
$string['drawer_side_left'] = 'Abrir pela esquerda';
$string['drawer_side_right'] = 'Abrir pela direita';
$string['dttutor:use'] = 'Usar Tutor-IA';
$string['enabled'] = 'Ativar chat';
$string['enabled_desc'] = 'Ativar ou desativar o chat Tutor-IA globalmente';
$string['error_api_not_configured'] = 'A configuração da API está ausente. Por favor, verifique suas configurações.';
$string['error_api_request_failed'] = 'Erro na solicitação da API: {$a}';
$string['error_cache_unavailable'] = 'O serviço de chat está temporariamente indisponível. Por favor, tente atualizar a página.';
$string['error_http_code'] = 'Erro HTTP {$a}';
$string['error_invalid_api_response'] = 'Resposta da API inválida';
$string['error_invalid_coordinates'] = 'Coordenadas inválidas. Por favor, use valores CSS válidos (ex: 10px, 2rem, 50%)';
$string['error_invalid_position'] = 'Dados de posição inválidos';
$string['offtopic_detection_enabled'] = 'Ativar detecção de assuntos fora do contexto';
$string['offtopic_detection_enabled_desc'] = 'Quando ativado, o tutor IA detectará e responderá a mensagens fora do contexto de acordo com o nível de rigor configurado abaixo.';
$string['offtopic_strictness'] = 'Rigor de detecção fora do contexto';
$string['offtopic_strictness_desc'] = 'Controle quão rigorosa é a detecção de assuntos fora do contexto. Permissivo permite mais flexibilidade, enquanto rigoroso impõe conversas relacionadas apenas ao curso.';
$string['offtopic_strictness_moderate'] = 'Moderado';
$string['offtopic_strictness_permissive'] = 'Permissivo';
$string['offtopic_strictness_strict'] = 'Rigoroso';
$string['open'] = 'Abrir Tutor IA';
$string['pluginname'] = 'Tutor IA';
$string['position_custom'] = 'Posição personalizada';
$string['position_left'] = 'Canto inferior esquerdo';
$string['position_preset'] = 'Posição predefinida';
$string['position_right'] = 'Canto inferior direito';
$string['position_x'] = 'Posição horizontal (X)';
$string['position_x_help'] = 'Distância da borda esquerda. Exemplos: 2rem, 20px, 5%. Use valores negativos para posicionar a partir da borda direita.';
$string['position_y'] = 'Posição vertical (Y)';
$string['position_y_help'] = 'Distância da borda inferior. Exemplos: 6rem, 80px, 10%. Use valores negativos para posicionar a partir da borda superior.';
$string['preview'] = 'Visualização ao vivo';
$string["ref_bottom"] = "Inferior";
$string["ref_left"] = "Esquerda";
$string["ref_right"] = "Direita";
$string["ref_top"] = "Superior";
$string["reference_edge_x"] = "Borda de referência horizontal";
$string["reference_edge_y"] = "Borda de referência vertical";
$string['sendmessage'] = 'Enviar mensagem';
$string['sessionnotready'] = 'A sessão do Tutor-IA não está pronta. Por favor, tente novamente.';
$string['student'] = 'Estudante';
$string['teacher'] = 'Professor';
$string['tutorcustomization'] = 'Personalização do Tutor';
$string['tutorname_default'] = 'Tutor IA';
$string['tutorname_setting'] = 'Nome do tutor';
$string['tutorname_setting_desc'] = 'Configure o nome a ser exibido no cabeçalho do chat. Você pode usar {teachername} para exibir o nome real do professor do curso, ou inserir um nome personalizado. Exemplos: "{teachername}" exibirá "João Silva", "Assistente IA" exibirá "Assistente IA".';
$string['typemessage'] = 'Digite sua mensagem...';
$string['unauthorized'] = 'Acesso não autorizado';
$string['welcomemessage'] = 'Olá! Eu sou seu assistente de IA. Como posso ajudá-lo hoje?';
$string['welcomemessage_default'] = 'Olá! Eu sou {teachername}, seu assistente de IA. Como posso ajudá-lo hoje?';
$string['welcomemessage_setting'] = 'Mensagem de boas-vindas';
$string['welcomemessage_setting_desc'] = 'Personalize a mensagem de boas-vindas exibida ao abrir o chat. Você pode usar placeholders: {teachername}, {coursename}, {username}, {firstname}';
