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
 * Quick Start Options Manager - Visual interface for managing options
 *
 * @module     local_dttutor/quick_options_manager
 * @copyright  2025 Industria Elearning
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/str'], function($, Str) {
    'use strict';

    class QuickOptionsManager {
        constructor(fieldname, options) {
            this.fieldname = fieldname;
            this.options = options || [];
            this.container = null;
            this.hiddenField = null;
            this.tbody = null;

            this.init();
        }

        init() {
            // Find elements.
            this.container = $('[data-fieldname="' + this.fieldname + '"]');
            if (!this.container.length) {
                return;
            }

            this.hiddenField = this.container.find('input[type="hidden"]');
            this.tbody = this.container.find('[data-region="options-tbody"]');

            // Render current options.
            this.renderOptions();

            // Register event listeners.
            this.registerEvents();
        }

        registerEvents() {
            // Add button.
            this.container.on('click', '[data-action="add-option"]', () => {
                this.addOption();
            });

            // Delete button (delegated).
            this.container.on('click', '[data-action="delete-option"]', (e) => {
                const index = $(e.currentTarget).data('index');
                this.deleteOption(index);
            });

            // Move up button.
            this.container.on('click', '[data-action="move-up"]', (e) => {
                const index = $(e.currentTarget).data('index');
                this.moveOption(index, -1);
            });

            // Move down button.
            this.container.on('click', '[data-action="move-down"]', (e) => {
                const index = $(e.currentTarget).data('index');
                this.moveOption(index, 1);
            });

            // Enter key in new fields.
            this.container.find('[data-field]').on('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.addOption();
                }
            });
        }

        renderOptions() {
            this.tbody.empty();

            if (this.options.length === 0) {
                const emptyText = 'No options configured. Add your first option below.';
                const emptyRow = $('<tr><td colspan="4" class="text-center text-muted">' + emptyText + '</td></tr>');
                this.tbody.append(emptyRow);
                return;
            }

            this.options.forEach((option, index) => {
                const row = this.createOptionRow(option, index);
                this.tbody.append(row);
            });
        }

        createOptionRow(option, index) {
            const row = $('<tr></tr>');

            // Icon cell.
            const iconCell = $('<td></td>').addClass('text-center').text(option.icon || '');

            // Label cell.
            const labelCell = $('<td></td>').text(option.label || '');

            // Prompt cell.
            const promptCell = $('<td></td>').text(option.prompt || '');

            // Actions cell.
            const actionsCell = $('<td></td>').addClass('text-center');

            const btnGroup = $('<div></div>').addClass('btn-group btn-group-sm');

            // Move up button.
            if (index > 0) {
                const upBtn = $('<button></button>')
                    .attr('type', 'button')
                    .addClass('btn btn-secondary')
                    .attr('data-action', 'move-up')
                    .attr('data-index', index)
                    .attr('title', 'Move up')
                    .html('<i class="icon fa fa-arrow-up fa-fw"></i>');
                btnGroup.append(upBtn);
            }

            // Move down button.
            if (index < this.options.length - 1) {
                const downBtn = $('<button></button>')
                    .attr('type', 'button')
                    .addClass('btn btn-secondary')
                    .attr('data-action', 'move-down')
                    .attr('data-index', index)
                    .attr('title', 'Move down')
                    .html('<i class="icon fa fa-arrow-down fa-fw"></i>');
                btnGroup.append(downBtn);
            }

            // Delete button.
            const deleteBtn = $('<button></button>')
                .attr('type', 'button')
                .addClass('btn btn-danger')
                .attr('data-action', 'delete-option')
                .attr('data-index', index)
                .attr('title', 'Delete')
                .html('<i class="icon fa fa-trash fa-fw"></i>');
            btnGroup.append(deleteBtn);

            actionsCell.append(btnGroup);

            row.append(iconCell, labelCell, promptCell, actionsCell);

            return row;
        }

        addOption() {
            const iconField = this.container.find('[data-field="new-icon"]');
            const labelField = this.container.find('[data-field="new-label"]');
            const promptField = this.container.find('[data-field="new-prompt"]');

            const icon = iconField.val().trim();
            const label = labelField.val().trim();
            const prompt = promptField.val().trim();

            // Validate.
            if (!icon || !label || !prompt) {
                Str.get_string('quickoption_validation_error', 'local_dttutor').done(function(message) {
                    alert(message);
                });
                return;
            }

            // Add to array.
            this.options.push({
                icon: icon,
                label: label,
                prompt: prompt
            });

            // Clear fields.
            iconField.val('');
            labelField.val('');
            promptField.val('');

            // Re-render and update hidden field.
            this.renderOptions();
            this.updateHiddenField();
        }

        deleteOption(index) {
            Str.get_string('quickoption_delete_confirm', 'local_dttutor').done((message) => {
                if (confirm(message)) {
                    this.options.splice(index, 1);
                    this.renderOptions();
                    this.updateHiddenField();
                }
            });
        }

        moveOption(index, direction) {
            const newIndex = index + direction;

            if (newIndex < 0 || newIndex >= this.options.length) {
                return;
            }

            // Swap.
            const temp = this.options[index];
            this.options[index] = this.options[newIndex];
            this.options[newIndex] = temp;

            this.renderOptions();
            this.updateHiddenField();
        }

        updateHiddenField() {
            const json = JSON.stringify(this.options, null, 2);
            this.hiddenField.val(json);
        }
    }

    return {
        init: function(fieldname, options) {
            return new QuickOptionsManager(fieldname, options);
        }
    };
});
