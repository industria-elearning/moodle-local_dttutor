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
 * Error modal for Tutor-IA
 *
 * @module     local_dttutor/error_modal
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/modal_factory', 'core/modal_events', 'core/str'], function($, ModalFactory, ModalEvents, Str) {
    'use strict';

    /**
     * Show error modal with friendly message
     *
     * @param {string} message Error message to display
     * @param {boolean} isConfigError Whether this is a configuration error
     * @param {string} configUrl Optional configuration URL for admins
     * @returns {Promise} Promise that resolves when modal is created
     */
    var showError = function(message, isConfigError, configUrl) {
        return Str.get_strings([
            {key: 'error_webservice_not_configured_short', component: 'local_dttutor'},
            {key: 'pluginname', component: 'local_dttutor'}
        ]).then(function(strings) {
            var title = isConfigError ? strings[0] : strings[1];
            var body = '<div class="tutor-ia-error-modal">';
            body += '<div class="alert alert-warning">';
            body += '<i class="fa fa-exclamation-triangle"></i> ';
            body += '<span>' + message + '</span>';
            body += '</div>';

            if (configUrl) {
                body += '<div class="mt-3">';
                body += '<a href="' + configUrl + '" class="btn btn-primary" target="_blank">';
                body += '<i class="fa fa-cog"></i> ';
                body += 'Configure Now';
                body += '</a>';
                body += '</div>';
            }

            body += '</div>';

            return ModalFactory.create({
                type: ModalFactory.types.DEFAULT,
                title: title,
                body: body
            });
        }).then(function(modal) {
            modal.show();

            // Auto-hide after 10 seconds if not a config error
            if (!isConfigError) {
                setTimeout(function() {
                    modal.hide();
                }, 10000);
            }

            return modal;
        }).catch(function(error) {
            // Fallback to alert if modal creation fails
            window.alert(message);
            throw error;
        });
    };

    /**
     * Show configuration error modal
     *
     * @param {string} message Error message
     * @param {string} configUrl Optional configuration URL
     * @returns {Promise} Promise that resolves when modal is created
     */
    var showConfigError = function(message, configUrl) {
        return showError(message, true, configUrl);
    };

    /**
     * Show general error modal
     *
     * @param {string} message Error message
     * @returns {Promise} Promise that resolves when modal is created
     */
    var showGeneralError = function(message) {
        return showError(message, false, null);
    };

    return {
        showError: showError,
        showConfigError: showConfigError,
        showGeneralError: showGeneralError
    };
});
