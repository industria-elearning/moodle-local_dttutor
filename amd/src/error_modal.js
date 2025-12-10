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

import Modal from 'core/modal';
import * as Str from 'core/str';
import * as Templates from 'core/templates';

    /**
     * Show error modal with friendly message
     *
     * @param {string} message Error message to display
     * @param {boolean} isConfigError Whether this is a configuration error
     * @param {string} configUrl Optional configuration URL for admins
     * @returns {Promise} Promise that resolves when modal is created
     */
    const showError = async(message, isConfigError, configUrl) => {
        try {
            const strings = await Str.get_strings([
                {key: 'error_webservice_not_configured_short', component: 'local_dttutor'},
                {key: 'pluginname', component: 'local_dttutor'},
                {key: 'configure_now', component: 'local_dttutor'}
            ]);

            const modalTitle = isConfigError ? strings[0] : strings[1];
            const configureNowStr = strings[2];

            // Prepare template context.
            const context = {
                message: message,
                has_config_url: !!configUrl,
                config_url: configUrl || '',
                str_configure_now: configureNowStr
            };

            // Render template.
            const html = await Templates.render('local_dttutor/error_modal_body', context);

            // Create modal using new API.
            const modal = await Modal.create({
                title: modalTitle,
                body: html,
                show: true,
                removeOnClose: true
            });

            // Auto-hide after 10 seconds if not a config error.
            if (!isConfigError) {
                setTimeout(() => {
                    modal.hide();
                }, 10000);
            }

            return modal;
        } catch (error) {
            // Fallback to alert if modal/template fails.
            window.alert(message);
            throw error;
        }
    };

    /**
     * Show configuration error modal
     *
     * @param {string} message Error message
     * @param {string} configUrl Optional configuration URL
     * @returns {Promise} Promise that resolves when modal is created
     */
    export const showConfigError = (message, configUrl) => {
        return showError(message, true, configUrl);
    };

    /**
     * Show general error modal
     *
     * @param {string} message Error message
     * @returns {Promise} Promise that resolves when modal is created
     */
    export const showGeneralError = (message) => {
        return showError(message, false, null);
    };

    export default {
        showError,
        showConfigError,
        showGeneralError
    };
