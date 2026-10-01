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
 * provider.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_beforeafter\privacy;

use context;
use context_module;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\writer;

/**
 * Class provider.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Describes stored personal data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            "beforeafter_entries",
            [
                "userid" => "privacy:metadata:beforeafter_entries:userid",
                "beforetext" => "privacy:metadata:beforeafter_entries:beforetext",
                "aftertext" => "privacy:metadata:beforeafter_entries:aftertext",
                "timebefore" => "privacy:metadata:beforeafter_entries:timebefore",
                "timeafter" => "privacy:metadata:beforeafter_entries:timeafter",
                "timemodified" => "privacy:metadata:beforeafter_entries:timemodified",
            ],
            "privacy:metadata:beforeafter_entries"
        );

        return $collection;
    }

    /**
     * Returns contexts containing data for a user.
     *
     * @param int $userid
     * @return contextlist
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();
        $sql = "SELECT ctx.id
                  FROM {context} ctx
                  JOIN {course_modules} cm ON cm.id = ctx.instanceid AND ctx.contextlevel = :contextlevel
                  JOIN {modules} m ON m.id = cm.module AND m.name = :modname
                  JOIN {beforeafter} ba ON ba.id = cm.instance
                  JOIN {beforeafter_entries} bae ON bae.beforeafterid = ba.id
                 WHERE bae.userid = :userid";
        $contextlist->add_from_sql($sql, [
            "contextlevel" => CONTEXT_MODULE,
            "modname" => "beforeafter",
            "userid" => $userid,
        ]);

        return $contextlist;
    }

    /**
     * Exports user data.
     *
     * @param approved_contextlist $contextlist
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        if (!$contextlist->count()) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof context_module) {
                continue;
            }

            $cm = get_coursemodule_from_id("beforeafter", $context->instanceid);
            if (!$cm) {
                continue;
            }

            $activity = $DB->get_record("beforeafter", ["id" => $cm->instance]);
            $entry = $DB->get_record("beforeafter_entries", [
                "beforeafterid" => $cm->instance,
                "userid" => $userid,
            ]);
            if (!$activity || !$entry) {
                continue;
            }

            $data = (object)[
                get_string("privacy:export:before", "mod_beforeafter") => $entry->beforetext,
                get_string("privacy:export:after", "mod_beforeafter") => $entry->aftertext,
            ];

            writer::with_context($context)->export_data([format_string($activity->name)], $data);
        }
    }

    /**
     * Deletes all response data in one module context.
     *
     * @param context $context
     */
    public static function delete_data_for_all_users_in_context(context $context): void {
        global $DB;

        if (!$context instanceof context_module) {
            return;
        }

        $cm = get_coursemodule_from_id("beforeafter", $context->instanceid);
        if ($cm) {
            $DB->delete_records("beforeafter_entries", ["beforeafterid" => $cm->instance]);
        }
    }

    /**
     * Deletes data for a user from approved contexts.
     *
     * @param approved_contextlist $contextlist
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        if (!$contextlist->count()) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        foreach ($contextlist->get_contexts() as $context) {
            if (!$context instanceof context_module) {
                continue;
            }

            $cm = get_coursemodule_from_id("beforeafter", $context->instanceid);
            if ($cm) {
                $DB->delete_records("beforeafter_entries", [
                    "beforeafterid" => $cm->instance,
                    "userid" => $userid,
                ]);
            }
        }
    }
}
