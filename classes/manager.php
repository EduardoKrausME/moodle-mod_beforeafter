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
 * manager.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_beforeafter;

use context_module;
use moodle_exception;
use stdClass;

/**
 * Class manager.
 */
class manager {
    /**
     * Returns a student's entry or an empty entry object.
     *
     * @param int $beforeafterid
     * @param int $userid
     * @return stdClass
     */
    public static function get_entry(int $beforeafterid, int $userid): stdClass {
        global $DB;

        $entry = $DB->get_record("beforeafter_entries", [
            "beforeafterid" => $beforeafterid,
            "userid" => $userid,
        ]);

        if ($entry) {
            return $entry;
        }

        return (object) [
            "id" => 0,
            "beforeafterid" => $beforeafterid,
            "userid" => $userid,
            "beforetext" => "",
            "beforeformat" => FORMAT_HTML,
            "aftertext" => "",
            "afterformat" => FORMAT_HTML,
            "timebefore" => 0,
            "timeafter" => 0,
            "timemodified" => 0,
        ];
    }

    /**
     * Saves one of the two reflection moments.
     *
     * @param stdClass $activity
     * @param int $userid
     * @param string $stage
     * @param string $text
     * @param int $format
     * @return stdClass
     */
    public static function save_response(stdClass $activity, int $userid, string $stage, string $text, int $format): stdClass {
        global $DB;

        if (!in_array($stage, ["before", "after"], true)) {
            throw new moodle_exception("invalidstage", "mod_beforeafter");
        }

        if ($stage === "before" && !empty($activity->afterreleased)) {
            throw new moodle_exception("beforelocked", "mod_beforeafter");
        }

        if ($stage === "after" && empty($activity->afterreleased)) {
            throw new moodle_exception("afternotreleased", "mod_beforeafter");
        }

        $entry = self::get_entry((int) $activity->id, $userid);
        $now = time();

        if ($stage === "before") {
            $entry->beforetext = $text;
            $entry->beforeformat = $format;
            $entry->timebefore = $now;
        } else {
            $entry->aftertext = $text;
            $entry->afterformat = $format;
            $entry->timeafter = $now;
        }

        $entry->timemodified = $now;

        if (empty($entry->id)) {
            $entry->id = $DB->insert_record("beforeafter_entries", $entry);
        } else {
            $DB->update_record("beforeafter_entries", $entry);
        }

        return $entry;
    }

    /**
     * Releases the second reflection moment.
     *
     * @param int $beforeafterid
     */
    public static function release_after(int $beforeafterid): void {
        global $DB;

        $activity = $DB->get_record("beforeafter", ["id" => $beforeafterid], "*", MUST_EXIST);
        if (!empty($activity->afterreleased)) {
            return;
        }

        $activity->afterreleased = 1;
        $activity->timereleased = time();
        $activity->timemodified = $activity->timereleased;
        $DB->update_record("beforeafter", $activity);
    }

    /**
     * Builds report data for all enrolled participants who can submit.
     *
     * @param context_module $context
     * @param int $beforeafterid
     * @return array
     */
    public static function get_report_rows(context_module $context, int $beforeafterid): array {
        global $DB;

        $users = get_enrolled_users(
            $context,
            "mod/beforeafter:submit",
            0,
            "u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename, u.email",
            "u.lastname ASC, u.firstname ASC"
        );

        if (!$users) {
            return [];
        }

        [$insql, $params] = $DB->get_in_or_equal(array_keys($users), SQL_PARAMS_NAMED, "user");
        $params["beforeafterid"] = $beforeafterid;
        $entries = $DB->get_records_select(
            "beforeafter_entries",
            "beforeafterid = :beforeafterid AND userid {$insql}",
            $params,
            "",
            "*"
        );

        $byuser = [];
        foreach ($entries as $entry) {
            $byuser[(int) $entry->userid] = $entry;
        }

        $rows = [];
        foreach ($users as $user) {
            $entry = $byuser[(int) $user->id] ?? null;
            $rows[] = (object) [
                "user" => $user,
                "entry" => $entry,
            ];
        }

        return $rows;
    }
}
