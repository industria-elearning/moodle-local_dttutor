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
 * Plugin upgrade steps are defined here.
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute local_dttutor upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_dttutor_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2025120300) {
        // Define table local_dttutor_course_config to be created.
        $table = new xmldb_table('local_dttutor_course_config');

        // Adding fields to table local_dttutor_course_config.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('custom_prompt', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('indexing_enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('last_indexed_at', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('indexing_status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'not_indexed');
        $table->add_field('indexing_task_id', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('indexing_error', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('usermodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_dttutor_course_config.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_dttutor_course_config.
        $table->add_index('courseid', XMLDB_INDEX_UNIQUE, ['courseid']);
        $table->add_index('indexing_status', XMLDB_INDEX_NOTUNIQUE, ['indexing_status']);

        // Conditionally launch create table for local_dttutor_course_config.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Dttutor savepoint reached.
        upgrade_plugin_savepoint(true, 2025120300, 'local', 'dttutor');
    }

    if ($oldversion < 2025120302) {
        // Change default value of indexing_enabled from 1 to 0 (disabled by default).
        $table = new xmldb_table('local_dttutor_course_config');
        $field = new xmldb_field('indexing_enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Launch change of default for field indexing_enabled.
        $dbman->change_field_default($table, $field);

        // Dttutor savepoint reached.
        upgrade_plugin_savepoint(true, 2025120302, 'local', 'dttutor');
    }

    if ($oldversion < 2026012201) {
        // Add local/dttutor:use capability to 'user' archetype roles.
        // This allows all authenticated users to use the tutor, not just enrolled students.
        $capability = 'local/dttutor:use';
        $archetypes = ['user'];

        foreach ($archetypes as $archetype) {
            // Get all roles with this archetype.
            $roles = get_archetype_roles($archetype);

            foreach ($roles as $role) {
                // Assign capability at system context level for course context.
                $systemcontext = context_system::instance();

                // Check if capability already exists for this role.
                $existingcap = $DB->get_record('role_capabilities', [
                    'roleid' => $role->id,
                    'capability' => $capability,
                    'contextid' => $systemcontext->id,
                ]);

                if (!$existingcap) {
                    // Assign capability with CAP_ALLOW permission.
                    assign_capability($capability, CAP_ALLOW, $role->id, $systemcontext->id);
                }
            }
        }

        // Dttutor savepoint reached.
        upgrade_plugin_savepoint(true, 2026012201, 'local', 'dttutor');
    }

    return true;
}
