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
 * Indexing progress management with full backend API integration
 *
 * @module     local_dttutor/indexing_progress
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/str'], function($, Ajax, Notification, Str) {
    'use strict';

    var IndexingProgress = function(courseid, initialStatus, initialTaskId) {
        this.courseid = courseid;
        this.taskId = initialTaskId || null;
        this.initialStatus = initialStatus || 'not_indexed';
        this.pollInterval = null;
        this.retries = 0;
        this.maxRetries = 3;
        this.isPolling = false;
    };

    IndexingProgress.prototype.init = function() {
        this.attachEventHandlers();

        // If page loaded with status 'running' and a task_id, start polling immediately.
        if (this.initialStatus === 'running' && this.taskId) {
            window.console.log('Page loaded with running status, starting polling for task:', this.taskId);
            this.showProgressUI();
            this.startPolling();
        } else {
            // Otherwise, check for stored task from previous session.
            this.checkForStoredTask();
        }

        this.setupBeforeUnload();
    };

    IndexingProgress.prototype.attachEventHandlers = function() {
        var self = this;

        // Start indexing button.
        $('[data-action="start-indexing"]').on('click', function(e) {
            e.preventDefault();
            self.checkStatusBeforeStart();
        });

        // Cancel indexing button.
        $('[data-action="cancel-indexing"]').on('click', function(e) {
            e.preventDefault();
            var taskid = $(this).data('taskid');
            if (!taskid && self.taskId) {
                taskid = self.taskId;
            }
            self.cancelIndexing(taskid);
        });
    };

    /**
     * Check for stored task from previous session
     */
    IndexingProgress.prototype.checkForStoredTask = function() {
        var storedTaskId = this.getStoredTaskId();
        if (storedTaskId) {
            this.taskId = storedTaskId;
            this.checkTaskStatus();
        }
    };

    /**
     * Setup warning when user tries to close browser during indexing
     */
    IndexingProgress.prototype.setupBeforeUnload = function() {
        var self = this;
        window.addEventListener('beforeunload', function(event) {
            if (self.isPolling && self.taskId) {
                var message = 'Indexing will continue in the background. You can safely close this page.';
                event.returnValue = message;
                return message;
            }
        });
    };

    /**
     * Check indexing status before starting to avoid duplicates
     */
    IndexingProgress.prototype.checkStatusBeforeStart = function() {
        var self = this;

        // If initial status was 'completed', force re-indexing.
        var forcereindex = (this.initialStatus === 'completed');

        Ajax.call([{
            methodname: 'local_dttutor_get_indexing_status',
            args: { courseid: this.courseid }
        }])[0].done(function(data) {
            // Update internal status.
            self.initialStatus = data.status;

            if (data.status === 'completed') {
                // If already completed, force re-indexing.
                self.startIndexing(true);
            } else if (data.status === 'running' && data.task_id) {
                // If already running, resume polling.
                self.taskId = data.task_id;
                self.saveTaskId(data.task_id);
                self.showProgressUI();
                self.startPolling();
            } else {
                // For 'not_indexed', 'failed', etc., use the force_reindex flag from initial status.
                self.startIndexing(forcereindex);
            }
        }).fail(function() {
            // If status check fails, use force_reindex based on initial status.
            self.startIndexing(forcereindex);
        });
    };

    /**
     * Check status of a stored task
     */
    IndexingProgress.prototype.checkTaskStatus = function() {
        var self = this;

        Ajax.call([{
            methodname: 'local_dttutor_get_indexing_progress',
            args: {
                task_id: this.taskId,
                courseid: this.courseid
            }
        }])[0].done(function(data) {
            if (data.status === 'running' || data.status === 'pending') {
                self.showProgressUI();
                self.startPolling();
            } else if (data.status === 'completed') {
                self.clearStoredTaskId();
            } else {
                self.clearStoredTaskId();
            }
        }).fail(function() {
            self.clearStoredTaskId();
        });
    };

    IndexingProgress.prototype.startIndexing = function(forcereindex) {
        var self = this;
        forcereindex = forcereindex || false;

        window.console.log('Starting indexing with force_reindex:', forcereindex);

        Ajax.call([{
            methodname: 'local_dttutor_start_indexing',
            args: {
                courseid: this.courseid,
                force_reindex: forcereindex
            }
        }])[0].done(function(response) {
            if (response.success && response.task_id) {
                self.taskId = response.task_id;
                self.saveTaskId(response.task_id);
                self.showProgressUI();
                self.startPolling();
            } else if (response.message) {
                Notification.addNotification({
                    message: response.message,
                    type: 'info'
                });
                if (response.status === 'already_indexed') {
                    setTimeout(function() {
                        window.location.reload();
                    }, 2000);
                }
            }
        }).fail(Notification.exception);
    };

    IndexingProgress.prototype.startPolling = function() {
        var self = this;
        this.isPolling = true;

        var poll = function() {
            self.pollProgress();
        };

        this.pollInterval = setInterval(poll, 2000);
        poll(); // Immediate first poll
    };

    IndexingProgress.prototype.pollProgress = function() {
        var self = this;

        Ajax.call([{
            methodname: 'local_dttutor_get_indexing_progress',
            args: {
                task_id: this.taskId,
                courseid: this.courseid
            }
        }])[0].done(function(data) {
            // Reset retries on success
            self.retries = 0;

            self.updateProgressUI(data);

            switch (data.status) {
                case 'pending':
                    // Keep polling, show "Starting..."
                    $('[data-region="indexing-phase-name"]').text('Starting...');
                    break;

                case 'running':
                    // Continue polling, UI already updated
                    break;

                case 'completed':
                    self.stopPolling();
                    self.clearStoredTaskId();
                    self.showSuccess();
                    break;

                case 'failed':
                    self.stopPolling();
                    self.clearStoredTaskId();
                    self.showError(data.error || 'Indexing failed');
                    break;

                case 'cancelled':
                    self.stopPolling();
                    self.clearStoredTaskId();
                    self.showCancelled();
                    break;

                case 'interrupted':
                    self.stopPolling();
                    self.clearStoredTaskId();
                    self.showInterrupted();
                    break;

                default:
                    // Unknown status, continue polling
                    break;
            }
        }).fail(function(error) {
            self.handlePollError(error);
        });
    };

    /**
     * Handle polling errors with exponential backoff
     *
     * @param {Object} error The error object
     */
    IndexingProgress.prototype.handlePollError = function(error) {
        var self = this;
        window.console.warn('Polling error:', error);

        this.retries++;

        if (this.retries >= this.maxRetries) {
            this.stopPolling();
            Notification.addNotification({
                message: 'Connection error. Please refresh the page.',
                type: 'error'
            });
        } else {
            // Exponential backoff: 2s, 4s, 8s
            var delay = 2000 * Math.pow(2, this.retries - 1);
            this.stopPolling();

            setTimeout(function() {
                if (self.taskId) {
                    self.startPolling();
                }
            }, delay);
        }
    };

    IndexingProgress.prototype.updateProgressUI = function(data) {
        var percent = data.overall_percent || 0;

        $('[data-region="indexing-progress-bar"]').css('width', percent + '%');
        $('[data-region="indexing-progress-bar"]').attr('aria-valuenow', percent);
        $('[data-region="indexing-progress-percent"]').text(Math.round(percent) + '%');

        if (data.current_phase) {
            $('[data-region="indexing-phase-name"]').text(data.current_phase);
        }

        // Show phase details if available
        if (data.phase_details) {
            $('[data-region="indexing-phase-details"]').text(data.phase_details).show();
        }

        // Show items processed if available
        if (data.items_total && data.items_total > 0) {
            var itemsText = data.items_processed + ' / ' + data.items_total + ' items';
            $('[data-region="indexing-items"]').text(itemsText).show();
        }
    };

    IndexingProgress.prototype.stopPolling = function() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
        this.isPolling = false;
    };

    IndexingProgress.prototype.showProgressUI = function() {
        $('[data-region="indexing-progress-container"]').show();
        $('[data-region="indexing-phase-info"]').show();
        $('[data-action="cancel-indexing"]').show();
        $('[data-action="start-indexing"]').hide();
    };

    IndexingProgress.prototype.hideProgressUI = function() {
        $('[data-region="indexing-progress-container"]').hide();
        $('[data-region="indexing-phase-info"]').hide();
        $('[data-action="cancel-indexing"]').hide();
        $('[data-action="start-indexing"]').show();
    };

    IndexingProgress.prototype.showSuccess = function() {
        this.hideProgressUI();

        Str.get_string('indexing_completed', 'local_dttutor').then(function(str) {
            Notification.addNotification({
                message: str,
                type: 'success'
            });
        }).catch(function() {
            Notification.addNotification({
                message: 'Indexing completed successfully',
                type: 'success'
            });
        });

        setTimeout(function() {
            window.location.reload();
        }, 2000);
    };

    IndexingProgress.prototype.showError = function(error) {
        this.hideProgressUI();

        Notification.addNotification({
            message: error || 'Indexing failed',
            type: 'error'
        });

        setTimeout(function() {
            window.location.reload();
        }, 3000);
    };

    IndexingProgress.prototype.showCancelled = function() {
        this.hideProgressUI();

        Str.get_string('indexing_cancelled', 'local_dttutor').then(function(str) {
            Notification.addNotification({
                message: str,
                type: 'info'
            });
        }).catch(function() {
            Notification.addNotification({
                message: 'Indexing was cancelled',
                type: 'info'
            });
        });

        setTimeout(function() {
            window.location.reload();
        }, 2000);
    };

    IndexingProgress.prototype.showInterrupted = function() {
        this.hideProgressUI();

        Str.get_string('indexing_interrupted', 'local_dttutor').then(function(str) {
            Notification.addNotification({
                message: str,
                type: 'warning'
            });
        }).catch(function() {
            Notification.addNotification({
                message: 'Indexing was interrupted. Please try again.',
                type: 'warning'
            });
        });

        setTimeout(function() {
            window.location.reload();
        }, 3000);
    };

    IndexingProgress.prototype.cancelIndexing = function(taskid) {
        var self = this;

        if (!taskid) {
            return;
        }

        Ajax.call([{
            methodname: 'local_dttutor_cancel_indexing',
            args: {
                task_id: taskid,
                courseid: this.courseid
            }
        }])[0].done(function(response) {
            if (response.success) {
                self.stopPolling();
                self.clearStoredTaskId();
                self.showCancelled();
            }
        }).fail(Notification.exception);
    };

    /**
     * Save task ID to localStorage for recovery
     *
     * @param {String} taskId The task ID to save
     */
    IndexingProgress.prototype.saveTaskId = function(taskId) {
        try {
            localStorage.setItem('local_dttutor_indexing_task_' + this.courseid, taskId);
        } catch (e) {
            // LocalStorage not available
        }
    };

    /**
     * Get stored task ID from localStorage
     */
    IndexingProgress.prototype.getStoredTaskId = function() {
        try {
            return localStorage.getItem('local_dttutor_indexing_task_' + this.courseid);
        } catch (e) {
            return null;
        }
    };

    /**
     * Clear stored task ID from localStorage
     */
    IndexingProgress.prototype.clearStoredTaskId = function() {
        try {
            localStorage.removeItem('local_dttutor_indexing_task_' + this.courseid);
        } catch (e) {
            // LocalStorage not available
        }
    };

    return {
        init: function(courseid, initialStatus, initialTaskId) {
            var progress = new IndexingProgress(courseid, initialStatus, initialTaskId);
            progress.init();
        }
    };
});
