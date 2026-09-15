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
 * restore_beforeafter_stepslib.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class restore_beforeafter_activity_structure_step.
 */
class restore_beforeafter_activity_structure_step extends restore_activity_structure_step {
    /**
     * Defines the restore paths.
     *
     * @return array
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element("beforeafter", "/activity/beforeafter");
        if ($this->get_setting_value("userinfo")) {
            $paths[] = new restore_path_element("beforeafter_entry", "/activity/beforeafter/entries/entry");
        }

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Restores the activity instance.
     *
     * @param array $data
     */
    protected function process_beforeafter($data): void {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();
        $newitemid = $DB->insert_record("beforeafter", $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Restores a student entry.
     *
     * @param array $data
     */
    protected function process_beforeafter_entry($data): void {
        global $DB;

        $data = (object) $data;
        $data->beforeafterid = $this->get_new_parentid("beforeafter");
        $data->userid = $this->get_mappingid("user", $data->userid);

        if (!$data->userid) {
            return;
        }

        $DB->insert_record("beforeafter_entries", $data);
    }

    /**
     * Restores related files.
     */
    protected function after_execute(): void {
        $this->add_related_files("mod_beforeafter", "intro", null);
    }
}
