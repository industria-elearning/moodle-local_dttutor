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
    'core/pubsub',
    'local_dttutor/error_modal'
], function(
    $,
    Ajax,
    Notification,
    PubSub,
    ErrorModal
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
            this.currentAIMessageContainer = null;

            // History pagination state
            this.historyOffset = 0;
            this.historyLimit = 20;
            this.isLoadingHistory = false;
            this.hasMoreHistory = true;
            this.historyLoaded = false;

            // Get welcome message from data attribute
            this.welcomeMessage = root.getAttribute('data-welcomemessage') || '';

            // Check if webservice is configured
            this.isConfigured = root.getAttribute('data-is-configured') === '1' ||
                                root.getAttribute('data-is-configured') === 'true';

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

            // Adjust drawer top position based on navbar height.
            this.adjustDrawerTopPosition();

            this.init();
        }

        /**
         * Adjusts the drawer top position based on the navbar height.
         * This ensures compatibility with different Moodle themes that may have
         * different navbar heights (e.g., 60px, 80px, etc.).
         */
        adjustDrawerTopPosition() {
            if (!this.drawerElement) {
                return;
            }

            // Try to find the navbar using common Moodle selectors.
            const navbarSelectors = [
                '.navbar.fixed-top',
                '.fixed-top.navbar',
                '#page-header.fixed-top',
                'nav.fixed-top',
                '.navbar-fixed-top'
            ];

            let navbarHeight = 60; // Default fallback height.

            for (const selector of navbarSelectors) {
                const navbar = document.querySelector(selector);
                if (navbar) {
                    // Get the computed height including padding and border.
                    navbarHeight = navbar.offsetHeight;

                    // If the navbar is hidden or has 0 height, skip it.
                    if (navbarHeight > 0) {
                        break;
                    }
                }
            }

            // Set the CSS variable for drawer top position.
            this.drawerElement.style.setProperty('--tutor-ia-drawer-top', navbarHeight + 'px');

            // Also listen for window resize to recalculate if needed.
            window.addEventListener('resize', () => {
                const navbar = document.querySelector(navbarSelectors[0]);
                if (navbar) {
                    const newHeight = navbar.offsetHeight;
                    this.drawerElement.style.setProperty('--tutor-ia-drawer-top', newHeight + 'px');
                }
            });
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

            // Infinity scroll for history
            const messagesContainer = this.root.find(SELECTORS.MESSAGES);
            messagesContainer.on('scroll', () => {
                this.handleHistoryScroll();
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

            // Update toggle button aria-expanded.
            if (this.toggleButton) {
                this.toggleButton.setAttribute('aria-expanded', 'true');
            }

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

            // Load chat history on first open (only if configured)
            if (this.isConfigured) {
                this.loadChatHistory();
            }
        }

        closeDrawer() {
            if (!this.drawerElement) {
                return;
            }

            this.drawerElement.classList.remove('show');
            this.drawerElement.setAttribute('tabindex', '-1');

            // Update toggle button aria-expanded.
            if (this.toggleButton) {
                this.toggleButton.setAttribute('aria-expanded', 'false');
            }

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

        /**
         * Load chat history from API
         */
        loadChatHistory() {
            // Skip if already loading, no more history, or no session yet
            if (this.isLoadingHistory || !this.hasMoreHistory) {
                return;
            }

            this.isLoadingHistory = true;

            // Get current scroll position and height to maintain position after loading
            const messagesContainer = this.root.find(SELECTORS.MESSAGES);
            const scrollHeightBefore = messagesContainer[0].scrollHeight;
            const scrollTopBefore = messagesContainer[0].scrollTop;

            // Show loading indicator at top
            this.showHistoryLoading();

            const requests = Ajax.call([{
                methodname: "local_dttutor_get_chat_history",
                args: {
                    courseid: parseInt(this.courseId, 10),
                    limit: this.historyLimit,
                    offset: this.historyOffset
                },
            }]);

            requests[0]
                .then((data) => {
                    this.hideHistoryLoading();

                    if (data.success && data.messages && data.messages.length > 0) {
                        const isInitialLoad = this.historyOffset === 0;

                        // Display messages
                        this.displayHistoryMessages(data.messages, isInitialLoad);

                        // Update pagination state
                        this.historyOffset += data.messages.length;
                        this.hasMoreHistory = data.pagination.has_more;

                        if (isInitialLoad) {
                            // Scroll to bottom to show newest messages
                            this.scrollToBottom();
                        } else {
                            // Maintain scroll position (prevent jump)
                            const scrollHeightAfter = messagesContainer[0].scrollHeight;
                            const scrollDiff = scrollHeightAfter - scrollHeightBefore;
                            messagesContainer[0].scrollTop = scrollTopBefore + scrollDiff;
                        }
                    } else {
                        this.hasMoreHistory = false;
                    }

                    this.historyLoaded = true;
                    this.isLoadingHistory = false;
                    return data;
                })
                .catch((err) => {
                    this.hideHistoryLoading();
                    this.isLoadingHistory = false;

                    // Show error in modal instead of notification
                    const errorMessage = this.getFriendlyErrorMessage(err);
                    const isConfigError = this.isWebserviceConfigError(err);
                    const configUrl = this.extractConfigUrl(err);

                    if (isConfigError) {
                        ErrorModal.showConfigError(errorMessage, configUrl);
                    } else {
                        ErrorModal.showGeneralError(errorMessage);
                    }
                });
        }

        /**
         * Handle scroll event for infinity scroll
         */
        handleHistoryScroll() {
            const messagesContainer = this.root.find(SELECTORS.MESSAGES);
            const scrollTop = messagesContainer[0].scrollTop;

            // Load more when scrolled near top (within 100px)
            if (scrollTop < 100 && !this.isLoadingHistory && this.hasMoreHistory) {
                this.loadChatHistory();
            }
        }

        /**
         * Display history messages in the chat
         * @param {Array} messages - Array of message objects from API (ordered DESC by timestamp from backend)
         * @param {boolean} isInitialLoad - True if this is the first load (append), false for loading older (prepend)
         */
        displayHistoryMessages(messages, isInitialLoad) {
            const messagesContainer = this.root.find(SELECTORS.MESSAGES);

            // Remove welcome message if it exists on initial load
            if (isInitialLoad && this.welcomeMessage) {
                messagesContainer.find('.tutor-ia-message.ai:contains("' + this.welcomeMessage + '")').remove();
            }

            if (isInitialLoad) {
                // Initial load: backend sends DESC [msg_10, msg_9, msg_8]
                // Iterate in reverse and append to get chronological order: msg_8, msg_9, msg_10
                for (let i = messages.length - 1; i >= 0; i--) {
                    const messageDiv = this.createMessageElement(messages[i]);
                    messagesContainer.append(messageDiv);
                }
            } else {
                // Loading older: backend sends DESC [msg_5, msg_4, msg_3]
                // Iterate normally and prepend to get: msg_3, msg_4, msg_5 (above existing messages)
                messages.forEach(msg => {
                    const messageDiv = this.createMessageElement(msg);
                    messagesContainer.prepend(messageDiv);
                });
            }
        }

        /**
         * Create a message element
         * @param {Object} msg - Message object
         * @returns {jQuery} Message element
         */
        createMessageElement(msg) {
            const messageDiv = $('<div>')
                .addClass('tutor-ia-message')
                .addClass(msg.role === 'user' ? 'user' : 'ai')
                .attr('data-message-id', msg.id);

            const contentDiv = $('<div>')
                .addClass('message-content')
                .text(msg.content);

            const timestampDiv = $('<div>')
                .addClass('message-timestamp')
                .text(this.formatTimestamp(msg.timestamp));

            messageDiv.append(contentDiv);
            messageDiv.append(timestampDiv);

            return messageDiv;
        }

        /**
         * Format Unix timestamp to readable date and time
         * @param {number} timestamp - Unix timestamp
         * @returns {string} Formatted date and time string
         */
        formatTimestamp(timestamp) {
            const date = new Date(timestamp * 1000);
            const today = new Date();

            // Reset hours to compare dates only
            const messageDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const yesterday = new Date(todayDate);
            yesterday.setDate(yesterday.getDate() - 1);

            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            const time = `${hours}:${minutes}`;

            // If today, show only time
            if (messageDate.getTime() === todayDate.getTime()) {
                return time;
            }

            // If yesterday, show "Yesterday HH:MM"
            if (messageDate.getTime() === yesterday.getTime()) {
                return `Yesterday ${time}`;
            }

            // Otherwise show DD/MM/YYYY HH:MM
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return `${day}/${month}/${year} ${time}`;
        }

        /**
         * Show loading indicator at top of messages
         */
        showHistoryLoading() {
            const messagesContainer = this.root.find(SELECTORS.MESSAGES);
            if (!messagesContainer.find('.history-loading').length) {
                const loadingDiv = $('<div>')
                    .addClass('history-loading')
                    .text('Loading...');
                messagesContainer.prepend(loadingDiv);
            }
        }

        /**
         * Hide loading indicator
         */
        hideHistoryLoading() {
            this.root.find('.history-loading').remove();
        }

        sendMessage() {
            // Do not send messages if webservice is not configured
            if (!this.isConfigured) {
                return;
            }

            const input = this.root.find(SELECTORS.INPUT);
            const sendBtn = this.root.find(SELECTORS.SEND_BTN);

            const messageText = input.val().trim();

            // Validation: Empty messages or streaming in progress.
            if (!messageText || this.streaming) {
                return;
            }

            // Validation: Message contains only a single dot.
            if (messageText === '.') {
                this.addMessage('[Error] Please enter a valid message.', 'ai');
                return;
            }

            // Validation: Message exceeds maximum length.
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

                const metaData = {
                    user_role: 'Student',
                    timestamp: Math.floor(Date.now() / 1000)
                };

                if (this.pageContext.pagetype) {
                    metaData.page = this.pageContext.pagetype;
                }

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

                        if (this.isNoCreditsError(err)) {
                            const errorHtml = err.message || 'Insufficient AI credits available.';
                            this.showNoCreditsWarning(errorHtml);

                            const input = this.root.find(SELECTORS.INPUT);
                            input.prop('disabled', true);
                            sendBtn.prop('disabled', true);
                        } else {
                            sendBtn.prop('disabled', false);

                            const errorMessage = this.getFriendlyErrorMessage(err);
                            const isConfigError = this.isWebserviceConfigError(err);
                            const configUrl = this.extractConfigUrl(err);

                            if (isConfigError) {
                                ErrorModal.showConfigError(errorMessage, configUrl);
                            } else {
                                ErrorModal.showGeneralError(errorMessage);
                            }
                        }
                    });
            } catch (error) {
                this.hideTypingIndicator();
                sendBtn.prop('disabled', false);
                ErrorModal.showGeneralError('Internal error: ' + error.message);
            }
        }

        startSSE(streamUrl, sendBtn) {
            try {
                const es = new EventSource(streamUrl);
                this.currentEventSource = es;
                this.streaming = true;
                let firstToken = true;
                let messageCompleted = false;

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
                        // Invalid token data, skip
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

                es.addEventListener('error', () => {
                    // Only show error if message did NOT complete successfully.
                    if (!messageCompleted) {
                        this.appendToAIMessage('\n[Connection interrupted]');
                        this.finalizeStream(sendBtn);
                    }
                    // If messageCompleted=true, error is expected (normal closure after completion).
                });
            } catch (error) {
                this.addMessage('[Error] Could not establish SSE connection', 'ai');
                this.finalizeStream(sendBtn);
            }
        }

        ensureAIMessageEl() {
            if (this.currentAIMessageEl) {
                return this.currentAIMessageEl;
            }

            const messages = this.root.find(SELECTORS.MESSAGES);
            let messageContainer;

            // Check if typing indicator exists
            const typingEl = messages.find('.tutor-ia-typing');
            if (typingEl.length) {
                // Convert typing indicator to message
                messageContainer = typingEl;
                messageContainer.removeClass('tutor-ia-typing');
                messageContainer.addClass('tutor-ia-message ai');
                messageContainer.html('');
            } else {
                // Create new message container
                messageContainer = $('<div class="tutor-ia-message ai"></div>');
                messages.append(messageContainer);
            }

            // Create content div for message text
            const contentDiv = $('<div>')
                .addClass('message-content');
            messageContainer.append(contentDiv);

            // Store reference to content div for appending text
            this.currentAIMessageEl = contentDiv[0];
            this.currentAIMessageContainer = messageContainer[0];
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

            // Create message container
            const messageEl = $('<div></div>')
                .addClass('tutor-ia-message')
                .addClass(type);

            // Add message content
            const contentDiv = $('<div>')
                .addClass('message-content')
                .text(text.substring(0, 10000));

            // Add timestamp (current time)
            const currentTimestamp = Math.floor(Date.now() / 1000);
            const timestampDiv = $('<div>')
                .addClass('message-timestamp')
                .text(this.formatTimestamp(currentTimestamp));

            messageEl.append(contentDiv);
            messageEl.append(timestampDiv);

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

        /**
         * Show no credits warning in chat
         * @param {string} errorHtml - HTML error message from provider
         */
        showNoCreditsWarning(errorHtml) {
            const messages = this.root.find(SELECTORS.MESSAGES);

            messages.find('.tutor-ia-no-credits-warning').remove();

            const warningDiv = $('<div class="tutor-ia-no-credits-warning"></div>');
            const alertDiv = $('<div class="alert alert-danger"></div>');
            alertDiv.html(
                '<i class="fa fa-exclamation-circle"></i> ' +
                '<div class="warning-content">' +
                '<strong>No Credits Available</strong>' +
                '<p>' + errorHtml + '</p>' +
                '</div>'
            );

            warningDiv.append(alertDiv);
            messages.append(warningDiv);
            this.scrollToBottom();
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
                    // Ignore.
                }
            }
            this.currentEventSource = null;
            this.streaming = false;
            this.currentAIMessageEl = null;
            this.currentAIMessageContainer = null;
            this.hideTypingIndicator();
        }

        finalizeStream(sendBtn) {
            if (this.currentAIMessageContainer) {
                const currentTimestamp = Math.floor(Date.now() / 1000);
                const timestampDiv = $('<div>')
                    .addClass('message-timestamp')
                    .text(this.formatTimestamp(currentTimestamp));
                $(this.currentAIMessageContainer).append(timestampDiv);

                this.currentAIMessageContainer = null;
            }

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

        /**
         * Check if error is related to webservice configuration
         * @param {Object} err - Error object
         * @returns {boolean}
         */
        isWebserviceConfigError(err) {
            if (!err || !err.message) {
                return false;
            }
            const message = err.message.toLowerCase();
            return message.includes('webservice_not_configured') ||
                   message.includes('webservice not configured') ||
                   message.includes('error_webservice_not_configured');
        }

        /**
         * Check if error is related to insufficient AI credits
         * @param {Object} err - Error object
         * @returns {boolean}
         */
        isNoCreditsError(err) {
            if (!err || !err.message) {
                return false;
            }
            const message = err.message.toLowerCase();
            return message.includes('notenoughtokens') ||
                   message.includes('insufficient ai credits') ||
                   message.includes('no credits') ||
                   message.includes('out of credits');
        }

        /**
         * Get friendly error message from exception
         * @param {Object} err - Error object
         * @returns {string} Friendly error message
         */
        getFriendlyErrorMessage(err) {
            if (!err) {
                return 'An unknown error occurred. Please try again.';
            }

            // Check if it's a webservice configuration error
            if (this.isWebserviceConfigError(err)) {
                // Return the error message as-is since it's already friendly
                // (comes from our language strings)
                return err.message || err.error || 'Configuration error';
            }

            // For other errors, provide generic friendly message
            if (err.message) {
                return err.message;
            }

            if (err.error) {
                return err.error;
            }

            return 'An error occurred. Please try again later.';
        }

        /**
         * Extract configuration URL from error message (for admin users)
         * @param {Object} err - Error object
         * @returns {string|null} Configuration URL or null
         */
        extractConfigUrl(err) {
            if (!err || !err.message) {
                return null;
            }

            // Try to extract URL from error message
            // Format: <a href="URL" target="_blank">
            const hrefMatch = err.message.match(/href="([^"]+)"/);
            if (hrefMatch && hrefMatch[1]) {
                return hrefMatch[1];
            }

            return null;
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
