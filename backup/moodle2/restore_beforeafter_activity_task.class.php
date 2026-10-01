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
 * restore_beforeafter_activity_task.class.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/mod/beforeafter/backup/moodle2/restore_beforeafter_stepslib.php");

/**
 * Class restore_beforeafter_activity_task.
 */
class restore_beforeafter_activity_task extends restore_activity_task {
    /**
     * No plugin-specific restore settings.
     */
    protected function define_my_settings(): void {
    }

    /**
     * Adds the activity restore step.
     */
    protected function define_my_steps(): void {
        $this->add_step(new restore_beforeafter_activity_structure_step("beforeafter_structure", "beforeafter.xml"));
    }

    /**
     * Defines content fields to decode.
     *
     * @return array
     */
    public static function define_decode_contents(): array {
        return [new restore_decode_content("beforeafter", ["intro"], "beforeafter")];
    }

    /**
     * Defines link decoding rules.
     *
     * @return array
     */
    public static function define_decode_rules(): array {
        return [
            new restore_decode_rule("BEFOREAFTERVIEWBYID", "/mod/beforeafter/view.php?id=$1", "course_module"),
            new restore_decode_rule("BEFOREAFTERINDEX", "/mod/beforeafter/index.php?id=$1", "course"),
        ];
    }

    /**
     * Defines activity log restore rules.
     *
     * @return array
     */
    public static function define_restore_log_rules(): array {
        return [];
    }
}
