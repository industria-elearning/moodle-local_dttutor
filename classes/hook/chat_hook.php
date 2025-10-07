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

namespace local_dttutor\hook;

use core\hook\output\before_footer_html_generation;

/**
 * Hook para cargar el chat flotante del Tutor-IA
 *
 * @package    local_dttutor
 * @copyright  2025 Datacurso <josue@datacurso.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class chat_hook {

    /**
     * Hook to load the floating chat before the footer.
     *
     * @param before_footer_html_generation $hook The hook event.
     */
    public static function before_footer_html_generation(before_footer_html_generation $hook): void {
        self::add_float_chat($hook);
    }

    /**
     * Checks if we are in a course context.
     * Returns true if the current page or context is related to a course or module.
     *
     * @return bool
     */
    private static function is_course_context(): bool {
        global $PAGE, $COURSE;

        // Check if we are on a course page.
        if ($PAGE->pagelayout === 'course' ||
            $PAGE->pagelayout === 'incourse' ||
            strpos($PAGE->pagetype, 'course-') === 0 ||
            strpos($PAGE->pagetype, 'mod-') === 0) {
            return true;
        }

        // Check if there is a valid course.
        if (isset($COURSE) && $COURSE->id > 1) {
            return true;
        }

        // Check context.
        $context = $PAGE->context;
        if (!$context) {
            return false;
        }
        if ($context->contextlevel == CONTEXT_COURSE ||
            $context->contextlevel == CONTEXT_MODULE) {
            return true;
        }

        return false;
    }

    /**
     * Adds the Tutor-IA drawer to course pages for all users.
     *
     * @param before_footer_html_generation $hook The hook event.
     */
    private static function add_float_chat(before_footer_html_generation $hook): void {
        global $PAGE, $COURSE, $USER, $OUTPUT;

        // Check if chat is enabled globally.
        if (!get_config('local_dttutor', 'enabled')) {
            return;
        }

        if (!self::is_course_context()) {
            return;
        }

        $courseid = $COURSE->id ?? 0;
        if ($courseid <= 1) {
            return; // No mostrar en frontpage.
        }

        // Detectar rol del usuario.
        $userrole = self::get_user_role_in_course();
        $userroledisplay = ($userrole === 'teacher') ? 'Profesor' : 'Estudiante';

        // Obtener avatar configurado con sistema de fallback.
        $avatarurl = self::get_avatar_url();

        // Obtener posición del avatar (derecha/izquierda).
        $position = get_config('local_dttutor', 'avatar_position');
        if (empty($position)) {
            $position = 'right'; // Por defecto: derecha.
        }

        // Calcular bottom position dinámicamente según el footer-popover de Moodle.
        // El footer-popover está en bottom: 2rem, y el communication en 4rem.
        // Colocamos el avatar en 6rem para estar por encima de ambos.
        $bottomposition = '6rem';

        // Generar unique ID.
        $uniqid = uniqid('tia_');

        // Preparar datos para templates.
        $toggledata = [
            'uniqid' => $uniqid,
            'avatarurl' => $avatarurl->out(false),
            'position' => $position,
            'bottomposition' => $bottomposition,
        ];

        $drawerdata = [
            'uniqid' => $uniqid,
            'courseid' => $courseid,
            'userid' => $USER->id,
            'userrole' => $userroledisplay,
            'avatarurl' => $avatarurl->out(false),
            'position' => $position,
        ];

        // Renderizar templates.
        $toggle = $OUTPUT->render_from_template('local_dttutor/tutor_ia_toggle', $toggledata);
        $drawer = $OUTPUT->render_from_template('local_dttutor/tutor_ia_drawer', $drawerdata);

        // Agregar HTML directamente al footer usando el hook.
        $hook->add_html($toggle . $drawer);
    }

    /**
     * Obtiene la URL del avatar configurado con sistema de fallback.
     *
     * @return \moodle_url URL del avatar a usar
     */
    private static function get_avatar_url(): \moodle_url {
        global $CFG;

        // Obtener configuración.
        $avatarnum = get_config('local_dttutor', 'avatar');

        // Primer fallback: Si no hay configuración, usar '01'.
        if (empty($avatarnum)) {
            $avatarnum = '01';
        }

        // Segundo fallback: Si el archivo no existe, usar '01'.
        $avatarpath = $CFG->dirroot . '/local/dttutor/pix/avatars/avatar_profesor_' . $avatarnum . '.png';
        if (!file_exists($avatarpath)) {
            $avatarnum = '01';

            // Último fallback: Si ni siquiera el '01' existe, usar un icono genérico de Moodle.
            $defaultpath = $CFG->dirroot . '/local/dttutor/pix/avatars/avatar_profesor_01.png';
            if (!file_exists($defaultpath)) {
                // Usar icono de usuario genérico de Moodle.
                return new \moodle_url('/pix/u/f1.png');
            }
        }

        return new \moodle_url('/local/dttutor/pix/avatars/avatar_profesor_' . $avatarnum . '.png');
    }

    /**
     * Determines the user's role in the current course context.
     */
    private static function get_user_role_in_course(): string {
        global $COURSE, $USER;

        if (!isset($COURSE) || $COURSE->id <= 1) {
            return 'student';
        }

        $context = \context_course::instance($COURSE->id);

        if (has_capability('moodle/course:update', $context) ||
            has_capability('moodle/course:manageactivities', $context)) {
            return 'teacher';
        }

        // Verificar roles específicos.
        $roles = get_user_roles($context, $USER->id);
        foreach ($roles as $role) {
            if (in_array($role->shortname, ['teacher', 'editingteacher', 'manager', 'coursecreator'])) {
                return 'teacher';
            }
        }

        return 'student';
    }
}
