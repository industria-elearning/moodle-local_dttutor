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
 * AI Mode - Fullscreen modal chat functionality
 *
 * @module     local_dttutor/ai_modal
 * @copyright  2025 Industria Elearning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
    'core/notification'
], function(
    $,
    Ajax,
    Notification
) {
    'use strict';

    const SELECTORS = {
        MODAL_TRIGGER: '[data-action="open-ai-modal"]',
        MODAL: '[data-region="ai-modal"]',
        CLOSE_BTN: '[data-action="close-ai-modal"]',
        INPUT: '[data-region="ai-modal-input"]',
        SEND_BTN: '[data-action="send-ai-message"]',
        MESSAGES_CONTAINER: '[data-region="ai-modal-messages"]',
        MESSAGES_SCROLL: '.ai-modal-messages-scroll',
        QUICK_OPTIONS: '[data-action="quick-option"]',
        QUICK_OPTIONS_CONTAINER: '[data-region="quick-options"]',
        WELCOME: '.ai-modal-welcome'
    };

    class AIModal {
        constructor() {
            this.modal = null;
            this.streaming = false;
            this.currentEventSource = null;
            this.currentSessionId = null;
            this.conversationStarted = false;
            this.courseId = null;
            this.cmId = null;
            this.userId = null;

            this.init();
        }

        init() {
            this.registerEventListeners();
        }

        registerEventListeners() {
            // Open modal button.
            $(document).on('click', SELECTORS.MODAL_TRIGGER, (e) => {
                e.preventDefault();
                this.openModal();
            });

            // Close modal button.
            $(document).on('click', SELECTORS.CLOSE_BTN, (e) => {
                e.preventDefault();
                this.closeModal();
            });

            // Send button.
            $(document).on('click', SELECTORS.SEND_BTN, () => {
                this.sendMessage();
            });

            // Input - Enter to send.
            $(document).on('keydown', SELECTORS.INPUT, (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            // Auto-resize textarea.
            $(document).on('input', SELECTORS.INPUT, function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });

            // Quick options.
            $(document).on('click', SELECTORS.QUICK_OPTIONS, (e) => {
                e.preventDefault();
                const prompt = $(e.currentTarget).data('prompt');
                this.handleQuickOption(prompt);
            });

            // Prevent closing on backdrop click.
            $(document).on('click', SELECTORS.MODAL, (e) => {
                if ($(e.target).is(SELECTORS.MODAL)) {
                    e.stopPropagation();
                    // Do not close - modal only closes via X button.
                }
            });

            // Prevent ESC key from closing.
            $(document).on('keydown', (e) => {
                if (e.key === 'Escape' && this.isModalOpen()) {
                    e.preventDefault();
                    e.stopPropagation();
                    // Do not close - modal only closes via X button.
                }
            });

            // Cleanup on page unload.
            $(window).on('beforeunload', () => this.cleanup());
        }

        isModalOpen() {
            return this.modal && this.modal.is(':visible');
        }

        openModal() {
            if (!this.modal) {
                this.modal = $(SELECTORS.MODAL);
            }

            if (!this.modal.length) {
                window.console.error('AI Modal not found in DOM');
                return;
            }

            // Get context data from modal attributes.
            this.courseId = this.modal.data('courseid');
            this.cmId = this.modal.data('cmid') || 0;
            this.userId = this.modal.data('userid');

            // Show modal.
            this.modal.fadeIn(300);
            $('body').addClass('ai-modal-open');

            // Focus input.
            setTimeout(() => {
                this.modal.find(SELECTORS.INPUT).focus();
            }, 350);
        }

        closeModal() {
            if (!this.modal) {
                return;
            }

            // Close any active stream.
            this.closeCurrentStream();

            // Hide modal.
            this.modal.fadeOut(300);
            $('body').removeClass('ai-modal-open');

            // Reset state.
            this.conversationStarted = false;
            this.currentSessionId = null;

            // Clear messages.
            this.modal.find(SELECTORS.MESSAGES_SCROLL).empty();

            // Hide messages container, show welcome.
            this.modal.find(SELECTORS.MESSAGES_CONTAINER).hide();
            this.modal.find(SELECTORS.WELCOME).show();
            this.modal.find(SELECTORS.QUICK_OPTIONS_CONTAINER).show();

            // Clear input.
            this.modal.find(SELECTORS.INPUT).val('').css('height', 'auto');
        }

        handleQuickOption(prompt) {
            if (!prompt || this.streaming) {
                return;
            }

            // Pre-fill input with prompt.
            this.modal.find(SELECTORS.INPUT).val(prompt).focus();

            // Auto-resize textarea.
            const input = this.modal.find(SELECTORS.INPUT)[0];
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 120) + 'px';
        }

        sendMessage() {
            const input = this.modal.find(SELECTORS.INPUT);
            const sendBtn = this.modal.find(SELECTORS.SEND_BTN);

            const messageText = input.val().trim();
            if (!messageText || this.streaming) {
                return;
            }

            if (messageText.length > 4000) {
                this.addMessage('[Error] Message is too long. Maximum 4000 characters.', 'ai');
                return;
            }

            try {
                this.closeCurrentStream();
                sendBtn.prop('disabled', true);

                // Start conversation mode.
                if (!this.conversationStarted) {
                    this.startConversation();
                }

                this.addMessage(messageText, 'user');
                input.val('');
                input.css('height', 'auto');
                this.scrollToBottom();
                this.showTypingIndicator();

                // Build metadata.
                const metaData = {
                    user_role: 'User',
                    timestamp: Math.floor(Date.now() / 1000),
                    source: 'ai_modal'
                };

                if (this.cmId && this.cmId > 0) {
                    metaData.cmid = parseInt(this.cmId, 10);
                }

                // Detect page context.
                const pageContext = this.detectPageContext();
                if (pageContext.pagetype) {
                    metaData.page = pageContext.pagetype;
                }

                window.console.log('AI Modal sending metadata:', metaData);

                const requests = Ajax.call([{
                    methodname: "local_dttutor_create_chat_message",
                    args: {
                        courseid: parseInt(this.courseId, 10),
                        message: this.sanitizeString(messageText.substring(0, 4000)),
                        meta: JSON.stringify(metaData)
                    },
                }]);

                requests[0]
                    .then((data) => {
                        if (!data || !data.stream_url) {
                            throw new Error('Stream URL missing in response');
                        }
                        this.currentSessionId = data.session_id;
                        this.startSSE(data.stream_url, sendBtn);
                        return data;
                    })
                    .catch((err) => {
                        this.hideTypingIndicator();
                        this.addMessage('[Error] ' + (err.message || 'Unknown error'), 'ai');
                        sendBtn.prop('disabled', false);
                        Notification.exception(err);
                    });
            } catch (error) {
                this.hideTypingIndicator();
                this.addMessage('[Error] Internal error: ' + error.message, 'ai');
                sendBtn.prop('disabled', false);
            }
        }

        startConversation() {
            // Hide welcome and quick options.
            this.modal.find(SELECTORS.WELCOME).hide();
            this.modal.find(SELECTORS.QUICK_OPTIONS_CONTAINER).hide();

            // Show messages container.
            this.modal.find(SELECTORS.MESSAGES_CONTAINER).show();

            this.conversationStarted = true;
        }

        startSSE(streamUrl, sendBtn) {
            try {
                const es = new EventSource(streamUrl);
                this.currentEventSource = es;
                this.streaming = true;
                let firstToken = true;
                let messageCompleted = false;
                let currentMessageEl = null;

                es.addEventListener('token', (ev) => {
                    try {
                        const payload = JSON.parse(ev.data);
                        const text = payload.t || payload.content || '';

                        if (firstToken) {
                            firstToken = false;
                            currentMessageEl = this.createAIMessage();
                            this.hideTypingIndicator();
                        }
                        this.appendToMessage(currentMessageEl, text);
                    } catch (e) {
                        window.console.warn('Invalid token data:', ev.data);
                    }
                });

                es.addEventListener('done', () => {
                    messageCompleted = true;
                    this.finalizeStream(sendBtn);
                });

                es.addEventListener('message_completed', () => {
                    messageCompleted = true;
                    this.finalizeStream(sendBtn);
                });

                es.addEventListener('error', () => {
                    if (!messageCompleted) {
                        window.console.error('SSE error');
                        if (currentMessageEl) {
                            this.appendToMessage(currentMessageEl, '\n[Connection interrupted]');
                        }
                        this.finalizeStream(sendBtn);
                    }
                });
            } catch (error) {
                window.console.error('Error starting SSE:', error);
                this.addMessage('[Error] Could not establish SSE connection', 'ai');
                this.finalizeStream(sendBtn);
            }
        }

        createAIMessage() {
            const messagesScroll = this.modal.find(SELECTORS.MESSAGES_SCROLL);
            const messageEl = $('<div class="tutor-ia-message ai"></div>');
            messagesScroll.append(messageEl);
            this.scrollToBottom();
            return messageEl[0];
        }

        appendToMessage(messageEl, text) {
            if (!messageEl || typeof text !== 'string') {
                return;
            }

            const currentText = messageEl.textContent || '';
            const maxLength = 10000;

            if (currentText.length + text.length > maxLength) {
                const remaining = maxLength - currentText.length;
                if (remaining > 0) {
                    messageEl.textContent += text.substring(0, remaining) + '...';
                }
                return;
            }

            messageEl.textContent += text;
            this.scrollToBottom();
        }

        addMessage(text, type) {
            if (!text || typeof text !== 'string') {
                return;
            }

            const messagesScroll = this.modal.find(SELECTORS.MESSAGES_SCROLL);
            const messageEl = $('<div></div>')
                .addClass('tutor-ia-message')
                .addClass(type)
                .text(text.substring(0, 10000));

            messagesScroll.append(messageEl);
            this.scrollToBottom();
        }

        showTypingIndicator() {
            const messagesScroll = this.modal.find(SELECTORS.MESSAGES_SCROLL);
            if (messagesScroll.find('.tutor-ia-typing').length) {
                return;
            }

            const typing = $('<div class="tutor-ia-message ai tutor-ia-typing"></div>')
                .html('<span class="dot"></span><span class="dot"></span><span class="dot"></span>');
            messagesScroll.append(typing);
            this.scrollToBottom();
        }

        hideTypingIndicator() {
            this.modal.find('.tutor-ia-typing').remove();
        }

        scrollToBottom() {
            const messagesScroll = this.modal.find(SELECTORS.MESSAGES_SCROLL);
            if (messagesScroll.length) {
                messagesScroll.scrollTop(messagesScroll[0].scrollHeight);
            }
        }

        closeCurrentStream() {
            if (this.currentEventSource) {
                try {
                    this.currentEventSource.close();
                } catch (e) {
                    window.console.warn('Error closing EventSource:', e);
                }
            }
            this.currentEventSource = null;
            this.streaming = false;
            this.hideTypingIndicator();
        }

        finalizeStream(sendBtn) {
            this.closeCurrentStream();
            if (sendBtn) {
                sendBtn.prop('disabled', false);
            }
        }

        sanitizeString(str) {
            if (typeof str !== 'string') {
                return '';
            }
            return str.replace(/[<>]/g, '');
        }

        detectPageContext() {
            const context = {};

            if (typeof M !== 'undefined' && M.cfg && M.cfg.pagetype) {
                context.pagetype = M.cfg.pagetype;
            }

            if (!context.pagetype) {
                const bodyId = document.body.id;
                if (bodyId) {
                    context.pagetype = bodyId.replace('page-', '');
                }
            }

            return context;
        }

        cleanup() {
            this.closeCurrentStream();
        }
    }

    return {
        init: function() {
            return new AIModal();
        }
    };
});
