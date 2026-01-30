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

$string['accepted_files_pdf_only'] = 'Apenas arquivos PDF são aceitos';
$string['avatar'] = 'Avatar do Tutor-IA';
$string['avatar_desc'] = 'Selecione o avatar a ser exibido no botão flutuante do chat Tutor-IA. Se nenhum for selecionado ou o arquivo não existir, o Avatar 1 será usado por padrão.';
$string['avatar_position'] = 'Posição do avatar';
$string['avatar_position_desc'] = 'Configure onde o botão flutuante do avatar Tutor-IA será exibido. Escolha uma posição de canto predefinida ou personalize as coordenadas X,Y exatas. A visualização ao vivo mostra como aparecerá.';
$string['cachedef_sessions'] = 'Cache para sessões de chat do Tutor-IA';
$string['cancel_indexing'] = 'Cancelar';
$string['char'] = 'caractere';
$string['chars'] = 'caracteres';
$string['choose_files'] = 'Escolher arquivos';
$string['clear_selection'] = 'Limpar seleção';
$string['close'] = 'Fechar Tutor IA';
$string['configuration_error'] = 'Erro de configuração';
$string['configure_now'] = 'Configurar agora';
$string['connection_interrupted'] = '[Conexão interrompida]';
$string['course_custom_prompt'] = 'Prompt Personalizado Específico do Curso';
$string['course_custom_prompt_help'] = 'Este prompt personalizado substitui a configuração global apenas para este curso. Deixe vazio para usar o prompt global.';
$string['course_indexing'] = 'Sincronização do Curso';
$string['course_materials'] = 'Materiais do Curso (PDFs)';
$string['course_materials_help'] = 'Carregue arquivos PDF adicionais que o tutor IA deve consultar ao responder perguntas.';
$string['custom_prompt'] = 'Prompt personalizado';
$string['custom_prompt_desc'] = 'Instruções personalizadas para controlar o comportamento do tutor IA. Use este campo para fornecer diretrizes específicas, tom ou limites de conhecimento para o tutor.';
$string['customavatar'] = 'Avatar personalizado';
$string['customavatar_desc'] = 'Faça upload da sua própria imagem de avatar personalizada. Isso substituirá o avatar predefinido selecionado.';
$string['customavatar_dimensions'] = 'Dimensões recomendadas: 200x200 pixels. Formatos suportados: PNG, JPG, JPEG, SVG. Tamanho máximo do arquivo: 512KB.';
$string['drag_drop_upload'] = 'Arraste e solte arquivos PDF aqui';
$string['drag_drop_upload_or_browse'] = 'ou clique para navegar';
$string['drawer_side'] = 'Lado de abertura da gaveta';
$string['drawer_side_help'] = 'Escolha de qual lado a gaveta de chat será aberta. Isso é independente da posição do botão do avatar.';
$string['drawer_side_left'] = 'Abrir pela esquerda';
$string['drawer_side_right'] = 'Abrir pela direita';
$string['dttutor:use'] = 'Usar Tutor-IA';
$string['enable_tutor_for_course'] = 'Ativar o Tutor IA para este curso';
$string['enable_tutor_for_course_help'] = 'Quando ativado, o Tutor IA estará disponível para alunos e professores neste curso. A configuração global do plugin também deve estar ativada.';
$string['enabled'] = 'Ativar chat';
$string['enabled_desc'] = 'Ativar ou desativar o chat Tutor-IA globalmente';
$string['error_api_not_configured'] = 'A configuração da API está ausente. Por favor, verifique suas configurações.';
$string['error_api_request_failed'] = 'Erro na solicitação da API: {$a}';
$string['error_attempt_later'] = 'Ocorreu um erro. Por favor, tente novamente mais tarde.';
$string['error_cache_unavailable'] = 'O serviço de chat está temporariamente indisponível. Por favor, tente atualizar a página.';
$string['error_empty_message'] = 'A mensagem não pode estar vazia';
$string['error_establish_sse_connection'] = '[Erro] Não foi possível estabelecer a conexão SSE';
$string['error_http_code'] = 'Erro HTTP {$a}';
$string['error_insufficient_tokens'] = 'Não há créditos de IA suficientes disponíveis para processar sua solicitação. Entre em contato com o administrador para adicionar mais créditos e continuar usando o Tutor de IA.';
$string['error_insufficient_tokens_short'] = 'Créditos Insuficientes';
$string['error_internal'] = 'Erro interno: {$a}';
$string['error_invalid_api_response'] = 'Resposta da API inválida';
$string['error_invalid_coordinates'] = 'Coordenadas inválidas. Por favor, use valores CSS válidos (ex: 10px, 2rem, 50%)';
$string['error_invalid_message'] = 'Por favor, insira uma mensagem válida';
$string['error_invalid_position'] = 'Dados de posição inválidos';
$string['error_license_fallback'] = 'Erro de licença: {$a}';
$string['error_license_fallback_short'] = 'Erro de Licença';
$string['error_license_not_allowed'] = 'A licença do seu site não permite acesso ao serviço Tutor de IA. Entre em contato com o administrador para verificar o status da licença ou atualizar seu plano.';
$string['error_license_not_allowed_short'] = 'Erro de Licença';
$string['error_message_too_long'] = '[Erro] A mensagem é muito longa. Máximo 4000 caracteres.';
$string['error_metadata_too_large'] = 'Os metadados enviados com sua mensagem são muito grandes. Por favor, tente novamente.';
$string['error_no_credits'] = 'Créditos de IA insuficientes disponíveis.';
$string['error_no_credits_fallback'] = 'Créditos insuficientes: {$a}';
$string['error_no_credits_short'] = 'Sem Créditos Disponíveis';
$string['error_selected_text_too_large'] = 'O texto selecionado é muito grande. Por favor, selecione uma porção menor.';
$string['error_unexpected'] = 'Ocorreu um erro inesperado. Por favor, tente novamente.';
$string['error_unknown'] = 'Ocorreu um erro desconhecido. Por favor, tente novamente.';
$string['error_webservice_not_configured'] = 'O chat do Tutor IA não está configurado corretamente e está atualmente indisponível.';
$string['error_webservice_not_configured_action'] = 'Por favor, entre em contato com o administrador do site ou relate este problema para ativar o serviço de chat.';
$string['error_webservice_not_configured_admin'] = 'O webservice do Provedor de IA Datacurso precisa ser configurado antes de usar o Tutor IA. <a href="{$a}" target="_blank">Clique aqui para configurá-lo agora</a>.';
$string['error_webservice_not_configured_admin_inline'] = 'O webservice do Provedor de IA Datacurso precisa ser configurado antes de usar o Tutor IA.';
$string['error_webservice_not_configured_short'] = 'Serviço de Chat Indisponível';
$string['indexing_cancelled'] = 'Cancelado';
$string['indexing_completed'] = 'Sincronizado';
$string['indexing_failed'] = 'Sincronização falhada';
$string['indexing_interrupted'] = 'Interrompido';
$string['indexing_not_indexed'] = 'Não sincronizado';
$string['indexing_phase_estimating'] = 'Estimando tokens...';
$string['indexing_phase_fetching'] = 'Buscando dados do curso...';
$string['indexing_phase_finalizing'] = 'Finalizando...';
$string['indexing_phase_initializing'] = 'Inicializando...';
$string['indexing_phase_preparing'] = 'Preparando documentos...';
$string['indexing_phase_uploading'] = 'Carregando documentos...';
$string['indexing_progress'] = 'Progresso: {$a}%';
$string['indexing_running'] = 'Sincronização em andamento';
$string['indexing_status'] = 'Estado de Sincronização';
$string['last_indexed'] = 'Última sincronização: {$a}';
$string['line'] = 'linha';
$string['lines'] = 'linhas';
$string['loading'] = 'Carregando...';
$string['manage_tutor'] = 'Gestão do Tutor IA';
$string['material_deleted'] = 'Material excluído com sucesso';
$string['material_uploaded'] = 'Material carregado com sucesso';
$string['off_topic_detection_enabled'] = 'Ativar detecção de assuntos fora do contexto';
$string['off_topic_detection_enabled_desc'] = 'Quando ativado, o tutor IA detectará e responderá a mensagens fora do contexto de acordo com o nível de rigor configurado abaixo.';
$string['off_topic_strictness'] = 'Rigor de detecção fora do contexto';
$string['off_topic_strictness_desc'] = 'Controle quão rigorosa é a detecção de assuntos fora do contexto. Permissivo permite mais flexibilidade, enquanto rigoroso impõe conversas relacionadas apenas ao curso.';
$string['off_topic_strictness_moderate'] = 'Moderado';
$string['off_topic_strictness_permissive'] = 'Permissivo';
$string['off_topic_strictness_strict'] = 'Rigoroso';
$string['open'] = 'Abrir Tutor IA';
$string['or_click_to_browse'] = 'ou clique para navegar';
$string['pluginname'] = 'Tutor IA';
$string['position_custom'] = 'Posição personalizada';
$string['position_left'] = 'Canto inferior esquerdo';
$string['position_preset'] = 'Posição predefinida';
$string['position_right'] = 'Canto inferior direito';
$string['position_x'] = 'Posição horizontal (X)';
$string['position_x_help'] = 'Distância da borda esquerda. Exemplos: 2rem, 20px, 5%. Use valores negativos para posicionar a partir da borda direita.';
$string['position_x_label'] = 'X: {$a->value} (de {$a->ref})';
$string['position_y'] = 'Posição vertical (Y)';
$string['position_y_help'] = 'Distância da borda inferior. Exemplos: 6rem, 80px, 10%. Use valores negativos para posicionar a partir da borda superior.';
$string['position_y_label'] = 'Y: {$a->value} (de {$a->ref})';
$string['positiondisplay_corner'] = 'Posição: canto {$a->preset} | Gaveta: {$a->drawer}';
$string['positiondisplay_custom'] = 'Posição: X: {$a->x}, Y: {$a->y} | Gaveta: {$a->drawer}';
$string['preview'] = 'Visualização ao vivo';
$string['ref_bottom'] = 'Inferior';
$string['ref_left'] = 'Esquerda';
$string['ref_right'] = 'Direita';
$string['ref_top'] = 'Superior';
$string['reference_edge_x'] = 'Borda de referência horizontal';
$string['reference_edge_y'] = 'Borda de referência vertical';
$string['restart_indexing'] = 'Re-sincronizar Curso';
$string['selected'] = 'selecionados';
$string['selection_indicator'] = '{$a} linhas selecionadas';
$string['selectionformat'] = '{$a->lines} {$a->linetext}, {$a->chars} {$a->chartext} selecionados';
$string['sendmessage'] = 'Enviar mensagem';
$string['sessionnotready'] = 'A sessão do Tutor-IA não está pronta. Por favor, tente novamente.';
$string['start_indexing'] = 'Iniciar Sincronização';
$string['student'] = 'Estudante';
$string['teacher'] = 'Professor';
$string['tutor_disabled_notice'] = 'O Tutor IA está atualmente desativado para este curso. Os alunos não verão a interface de chat.';
$string['tutor_status'] = 'Status do Tutor IA';
$string['tutorcustomization'] = 'Personalização do Tutor';
$string['tutorname_default'] = 'Tutor IA';
$string['tutorname_setting'] = 'Nome do tutor';
$string['tutorname_setting_desc'] = 'Configure o nome a ser exibido no cabeçalho do chat. Você pode usar {teachername} para exibir o nome real do professor do curso, ou inserir um nome personalizado. Exemplos: "{teachername}" exibirá "João Silva", "Assistente IA" exibirá "Assistente IA".';
$string['typemessage'] = 'Digite sua mensagem...';
$string['unauthorized'] = 'Acesso não autorizado';
$string['upload_files'] = 'Carregar arquivos';
$string['upload_material'] = 'Carregar material';
$string['welcomemessage'] = 'Olá! Eu sou seu assistente de IA. Como posso ajudá-lo hoje?';
$string['welcomemessage_default'] = 'Olá! Eu sou {teachername}, seu assistente de IA. Como posso ajudá-lo hoje?';
$string['welcomemessage_setting'] = 'Mensagem de boas-vindas';
$string['welcomemessage_setting_desc'] = 'Personalize a mensagem de boas-vindas exibida ao abrir o chat. Você pode usar placeholders: {teachername}, {coursename}, {username}, {firstname}';
$string['yesterday'] = 'Ontem';
