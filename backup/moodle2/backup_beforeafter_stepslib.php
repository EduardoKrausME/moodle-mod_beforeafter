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
 * backup_beforeafter_stepslib.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class backup_beforeafter_activity_structure_step.
 */
class backup_beforeafter_activity_structure_step extends backup_activity_structure_step {
    /**
     * Defines the backup structure.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        $userinfo = $this->get_setting_value("userinfo");

        $activity = new backup_nested_element("beforeafter", ["id"], [
            "name",
            "intro",
            "introformat",
            "beforeprompt",
            "afterprompt",
            "afterreleased",
            "timereleased",
            "timecreated",
            "timemodified",
        ]);

        $entries = new backup_nested_element("entries");
        $entry = new backup_nested_element("entry", ["id"], [
            "userid",
            "beforetext",
            "beforeformat",
            "aftertext",
            "afterformat",
            "timebefore",
            "timeafter",
            "timemodified",
        ]);

        $activity->add_child($entries);
        $entries->add_child($entry);

        $activity->set_source_table("beforeafter", ["id" => backup::VAR_ACTIVITYID]);

        if ($userinfo) {
            $entry->set_source_table("beforeafter_entries", ["beforeafterid" => "../../id"]);
        }

        $entry->annotate_ids("user", "userid");
        $activity->annotate_files("mod_beforeafter", "intro", null);

        return $this->prepare_activity_structure($activity);
    }
}
