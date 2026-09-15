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
 * lib.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Returns the features supported by mod_beforeafter.
 *
 * @param string $feature
 * @return bool|string|null
 */
function beforeafter_supports(string $feature): bool|string|null {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_SHOW_DESCRIPTION:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_ASSESSMENT;
        default:
            return null;
    }
}

/**
 * Creates an activity instance.
 *
 * @param stdClass $data
 * @param mod_beforeafter_mod_form|null $mform
 * @return int
 */
function beforeafter_add_instance(stdClass $data, ?mod_beforeafter_mod_form $mform = null): int {
    global $DB;

    $data->afterreleased = 0;
    $data->timereleased = 0;
    $data->timecreated = time();
    $data->timemodified = $data->timecreated;

    return $DB->insert_record("beforeafter", $data);
}

/**
 * Updates an activity instance.
 *
 * @param stdClass $data
 * @param mod_beforeafter_mod_form|null $mform
 * @return bool
 */
function beforeafter_update_instance(stdClass $data, ?mod_beforeafter_mod_form $mform = null): bool {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();

    return $DB->update_record("beforeafter", $data);
}

/**
 * Deletes an activity instance and its responses.
 *
 * @param int $id
 * @return bool
 */
function beforeafter_delete_instance(int $id): bool {
    global $DB;

    if (!$DB->record_exists("beforeafter", ["id" => $id])) {
        return false;
    }

    $DB->delete_records("beforeafter_entries", ["beforeafterid" => $id]);
    $DB->delete_records("beforeafter", ["id" => $id]);

    return true;
}
