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
 * Tutor-IA Chat - Drawer and chat functionality (based on aiplacement_courseassist)
 *
 * @module     local_dttutor/tutor_ia_chat
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/pubsub'
], function(
    $,
    Ajax,
    Notification,
    PubSub
) {
    'use strict';

    const SELECTORS = {
        TOGGLE_BTN: '[data-action="tutor-ia-toggle"]',
        DRAWER: '.tutor-ia-drawer',
        CLOSE_BTN: '.tutor-ia-close-button',
        MESSAGES: '[data-region="tutor-ia-messages"]',
        INPUT: '[data-region="tutor-ia-input"]',
        SEND_BTN: '[data-action="send-message"]',
        PAGE: '#page',
        JUMP_TO: '#jump-to',
        BODY: 'body'
    };

    class TutorIAChat {
        constructor(root, uniqueId, courseId, cmId, userId) {
            this.root = $(root);
            this.uniqueId = uniqueId;
            this.courseId = courseId;
            this.cmId = cmId;
            this.userId = userId;
            this.streaming = false;
            this.currentEventSource = null;
            this.currentSessionId = null;
            this.currentAIMessageEl = null;

            this.drawerElement = document.querySelector(SELECTORS.DRAWER);
            this.pageElement = document.querySelector(SELECTORS.PAGE);
            this.bodyElement = document.querySelector(SELECTORS.BODY);
            this.toggleButton = document.querySelector(SELECTORS.TOGGLE_BTN);
            this.closeButton = document.querySelector(SELECTORS.CLOSE_BTN);
            this.jumpTo = document.querySelector(SELECTORS.JUMP_TO);

            // Detect drawer position (right/left) from data-position.
            this.position = this.drawerElement ? this.drawerElement.getAttribute('data-position') || 'right' : 'right';
            this.pageClass = this.position === 'left' ? 'show-drawer-left' : 'show-drawer-right';
            // Class to identify that Tutor-IA drawer is open (to move footer-popover).
            this.bodyClass = this.position === 'left' ? 'tutor-ia-drawer-open-left' : 'tutor-ia-drawer-open-right';

            // Capture contextual information from the page.
            this.pageContext = this.detectPageContext();

            this.init();
        }

        /**
         * Detects the current page context (page type and relevant parameters).
         *
         * @returns {Object} Object with page contextual information
         */
        detectPageContext() {
            const context = {};

            // Method 1: Try to get pagetype from M.cfg.
            if (typeof M !== 'undefined' && M.cfg && M.cfg.pagetype) {
                context.pagetype = M.cfg.pagetype;
            }

            // Method 2: If not available, use body id/class (more reliable).
            if (!context.pagetype) {
                const bodyId = document.body.id;
                if (bodyId) {
                    // Body id format: "page-mod-forum-discuss" or "page-course-view-topics".
                    // Extract the part after "page-".
                    context.pagetype = bodyId.replace('page-', '');
                }
            }

            // Method 3: Fallback using body classes.
            if (!context.pagetype) {
                const bodyClasses = document.body.className;
                // Look for classes with format "path-mod-forum", "pagelayout-incourse", etc.
                const pathMatch = bodyClasses.match(/path-([\w-]+)/);
                if (pathMatch) {
                    context.pagetype = pathMatch[1];
                }
            }

            // Get URL parameters.
            const urlParams = new URLSearchParams(window.location.search);

            // Extract common parameters based on page type.
            if (context.pagetype && context.pagetype.includes('forum')) {
                // Forum discussion ID.
                if (urlParams.has('d')) {
                    context.discussionid = parseInt(urlParams.get('d'), 10);
                }
                // Forum ID.
                if (urlParams.has('f')) {
                    context.forumid = parseInt(urlParams.get('f'), 10);
                }
            } else if (context.pagetype && context.pagetype.includes('quiz')) {
                // Quiz attempt ID.
                if (urlParams.has('attempt')) {
                    context.attemptid = parseInt(urlParams.get('attempt'), 10);
                }
            } else if (context.pagetype && context.pagetype.includes('assign')) {
                // Assignment ID.
                if (urlParams.has('id')) {
                    context.assignid = parseInt(urlParams.get('id'), 10);
                }
            } else if (context.pagetype && context.pagetype.includes('wiki')) {
                // Wiki page ID.
                if (urlParams.has('pageid')) {
                    context.pageid = parseInt(urlParams.get('pageid'), 10);
                }
            }

            // Debug logging.
            window.console.log('Tutor-IA Page Context:', context);
            window.console.log('Body ID:', document.body.id);
            window.console.log('Body Classes:', document.body.className);
            window.console.log('URL Params:', window.location.search);

            return context;
        }

        init() {
            this.registerEventListeners();
            window.addEventListener('beforeunload', () => this.cleanup());
        }

        registerEventListeners() {
            // Toggle button.
            if (this.toggleButton) {
                this.toggleButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleDrawer();
                });
            }

            // Close button.
            if (this.closeButton) {
                this.closeButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.closeDrawer();
                });
            }

            // Send button.
            this.root.find(SELECTORS.SEND_BTN).on('click', () => {
                this.sendMessage();
            });

            // Input - Enter to send.
            const input = this.root.find(SELECTORS.INPUT);
            input.on('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });

            // Auto-resize textarea.
            input.on('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.min(this.scrollHeight, 120) + 'px';
            });

            // Close on Escape key.
            document.addEventListener('keydown', (e) => {
                if (this.isDrawerOpen() && e.key === 'Escape') {
                    this.closeDrawer();
                }
            });

            // Close drawer if message drawer opens.
            PubSub.subscribe('core_message/drawer_shown', () => {
                if (this.isDrawerOpen()) {
                    this.closeDrawer();
                }
            });

            // Jump to functionality.
            if (this.jumpTo) {
                this.jumpTo.addEventListener('focus', () => {
                    if (this.closeButton) {
                        this.closeButton.focus();
                    }
                });
            }
        }

        isDrawerOpen() {
            return this.drawerElement && this.drawerElement.classList.contains('show');
        }

        openDrawer() {
            if (!this.drawerElement) {
                return;
            }

            // Close message drawer if open.
            PubSub.publish('core_message/hide', {});

            this.drawerElement.classList.add('show');
            this.drawerElement.setAttribute('tabindex', '0');

            // Add padding to page (redistribute space) - uses this.pageClass (show-drawer-left or show-drawer-right).
            if (this.pageElement && !this.pageElement.classList.contains(this.pageClass)) {
                this.pageElement.classList.add(this.pageClass);
            }

            // Add class to body to identify Tutor-IA drawer is open (for footer-popover positioning).
            if (this.bodyElement && !this.bodyElement.classList.contains(this.bodyClass)) {
                this.bodyElement.classList.add(this.bodyClass);
            }

            // Focus management.
            if (this.jumpTo) {
                this.jumpTo.setAttribute('tabindex', 0);
                this.jumpTo.focus();
            }
        }

        closeDrawer() {
            if (!this.drawerElement) {
                return;
            }

            this.drawerElement.classList.remove('show');
            this.drawerElement.setAttribute('tabindex', '-1');

            // Remove padding from page - uses this.pageClass.
            if (this.pageElement && this.pageElement.classList.contains(this.pageClass)) {
                this.pageElement.classList.remove(this.pageClass);
            }

            // Remove class from body.
            if (this.bodyElement && this.bodyElement.classList.contains(this.bodyClass)) {
                this.bodyElement.classList.remove(this.bodyClass);
            }

            // Focus management.
            if (this.jumpTo) {
                this.jumpTo.setAttribute('tabindex', -1);
            }
            if (this.toggleButton) {
                this.toggleButton.focus();
            }
        }

        toggleDrawer() {
            if (this.isDrawerOpen()) {
                this.closeDrawer();
            } else {
                this.openDrawer();
            }
        }

        sendMessage() {
            const input = this.root.find(SELECTORS.INPUT);
            const sendBtn = this.root.find(SELECTORS.SEND_BTN);

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

                this.addMessage(messageText, 'user');
                input.val('');
                input.css('height', 'auto');
                this.scrollToBottom();
                this.showTypingIndicator();

                // Build metadata object with contextual information.
                const metaData = {
                    user_role: 'Student',
                    timestamp: Math.floor(Date.now() / 1000)
                };

                // Add page information if available.
                if (this.pageContext.pagetype) {
                    metaData.page = this.pageContext.pagetype;
                }

                // Add specific contextual parameters.
                if (this.pageContext.discussionid) {
                    metaData.discussionid = this.pageContext.discussionid;
                }
                if (this.pageContext.forumid) {
                    metaData.forumid = this.pageContext.forumid;
                }
                if (this.pageContext.attemptid) {
                    metaData.attemptid = this.pageContext.attemptid;
                }
                if (this.pageContext.assignid) {
                    metaData.assignid = this.pageContext.assignid;
                }
                if (this.pageContext.pageid) {
                    metaData.pageid = this.pageContext.pageid;
                }
                if (this.cmId) {
                    metaData.cmid = parseInt(this.cmId, 10);
                }

                // Debug logging.
                window.console.log('Tutor-IA sending metadata:', metaData);

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

        startSSE(streamUrl, sendBtn) {
            try {
                const es = new EventSource(streamUrl);
                this.currentEventSource = es;
                this.streaming = true;
                let firstToken = true;
                let messageCompleted = false; // Flag to track if message completed successfully.

                es.addEventListener('token', (ev) => {
                    try {
                        const payload = JSON.parse(ev.data);
                        const text = payload.t || payload.content || '';

                        if (firstToken) {
                            firstToken = false;
                            this.ensureAIMessageEl();
                            this.hideTypingIndicator();
                        }
                        this.appendToAIMessage(text);
                    } catch (e) {
                        window.console.warn('Invalid token data:', ev.data);
                    }
                });

                // Listen for 'done' event (server sends 'done', not 'message_completed').
                es.addEventListener('done', () => {
                    messageCompleted = true; // Mark that message completed successfully.
                    this.finalizeStream(sendBtn);
                });

                // Maintain compatibility with 'message_completed' in case server changes.
                es.addEventListener('message_completed', () => {
                    messageCompleted = true;
                    this.finalizeStream(sendBtn);
                });

                es.addEventListener('error', (e) => {
                    // Only show error if message did NOT complete successfully.
                    if (!messageCompleted) {
                        window.console.error('SSE error:', e);
                        this.appendToAIMessage('\n[Connection interrupted]');
                        this.finalizeStream(sendBtn);
                    }
                    // If messageCompleted=true, error is expected (normal closure after completion).
                });
            } catch (error) {
                window.console.error('Error starting SSE:', error);
                this.addMessage('[Error] Could not establish SSE connection', 'ai');
                this.finalizeStream(sendBtn);
            }
        }

        ensureAIMessageEl() {
            if (this.currentAIMessageEl) {
                return this.currentAIMessageEl;
            }

            const messages = this.root.find(SELECTORS.MESSAGES);
            let el = messages.find('.tutor-ia-typing');

            if (el.length) {
                el.removeClass('tutor-ia-typing');
                el.addClass('tutor-ia-message ai');
                el.html('');
            } else {
                el = $('<div class="tutor-ia-message ai"></div>');
                messages.append(el);
            }

            this.currentAIMessageEl = el[0];
            return this.currentAIMessageEl;
        }

        appendToAIMessage(text) {
            if (!this.currentAIMessageEl) {
                this.ensureAIMessageEl();
            }
            if (!this.currentAIMessageEl || typeof text !== 'string') {
                return;
            }

            const currentText = this.currentAIMessageEl.textContent || '';
            const maxLength = 10000;

            if (currentText.length + text.length > maxLength) {
                const remaining = maxLength - currentText.length;
                if (remaining > 0) {
                    this.currentAIMessageEl.textContent += text.substring(0, remaining) + '...';
                }
                return;
            }

            this.currentAIMessageEl.textContent += text;
            this.scrollToBottom();
        }

        addMessage(text, type) {
            if (!text || typeof text !== 'string') {
                return;
            }

            const messages = this.root.find(SELECTORS.MESSAGES);
            const messageEl = $('<div></div>')
                .addClass('tutor-ia-message')
                .addClass(type)
                .text(text.substring(0, 10000));

            messages.append(messageEl);
            this.scrollToBottom();
        }

        showTypingIndicator() {
            const messages = this.root.find(SELECTORS.MESSAGES);
            if (messages.find('.tutor-ia-typing').length) {
                return;
            }

            const typing = $('<div class="tutor-ia-message ai tutor-ia-typing"></div>')
                .html('<span class="dot"></span><span class="dot"></span><span class="dot"></span>');
            messages.append(typing);
            this.scrollToBottom();
        }

        hideTypingIndicator() {
            this.root.find('.tutor-ia-typing').remove();
        }

        scrollToBottom() {
            const messages = this.root.find(SELECTORS.MESSAGES);
            messages.scrollTop(messages[0].scrollHeight);
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
            this.currentAIMessageEl = null;
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

        cleanup() {
            this.closeCurrentStream();
        }

        destroy() {
            this.cleanup();
        }
    }

    return {
        init: function(root, uniqueId, courseId, cmId, userId) {
            return new TutorIAChat(root, uniqueId, courseId, cmId, userId);
        }
    };
});
