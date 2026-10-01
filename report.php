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
 * report.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

use mod_beforeafter\manager;

$id = required_param("id", PARAM_INT);

$cm = get_coursemodule_from_id("beforeafter", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record("beforeafter", ["id" => $cm->instance], "*", MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/beforeafter:viewreport", $context);

$PAGE->set_url("/mod/beforeafter/report.php", ["id" => $cm->id]);
$PAGE->set_title(get_string("reporttitle", "mod_beforeafter", format_string($activity->name)));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$rawrows = manager::get_report_rows($context, (int)$activity->id);
$rows = [];
$beforecount = 0;
$aftercount = 0;
$completecount = 0;

foreach ($rawrows as $item) {
    $user = $item->user;
    $entry = $item->entry;
    $hasbefore = $entry && $entry->beforetext !== "";
    $hasafter = $entry && $entry->aftertext !== "";

    if ($hasbefore) {
        $beforecount++;
    }
    if ($hasafter) {
        $aftercount++;
    }
    if ($hasbefore && $hasafter) {
        $completecount++;
    }

    $rows[] = [
        "fullname" => fullname($user),
        "profileurl" => (new moodle_url("/user/view.php", ["id" => $user->id, "course" => $course->id]))->out(false),
        "beforehtml" => $hasbefore ? format_text($entry->beforetext, $entry->beforeformat, ["context" => $context]) : "",
        "afterhtml" => $hasafter ? format_text($entry->aftertext, $entry->afterformat, ["context" => $context]) : "",
        "hasbefore" => $hasbefore,
        "hasafter" => $hasafter,
        "beforetime" => $hasbefore && $entry->timebefore ? userdate($entry->timebefore) : "",
        "aftertime" => $hasafter && $entry->timeafter ? userdate($entry->timeafter) : "",
        "complete" => $hasbefore && $hasafter,
    ];
}

$total = count($rawrows);
$templatecontext = [
    "name" => format_string($activity->name),
    "beforeprompt" => format_string($activity->beforeprompt),
    "afterprompt" => format_string($activity->afterprompt),
    "afterreleased" => !empty($activity->afterreleased),
    "timereleased" => $activity->timereleased ? userdate($activity->timereleased) : "",
    "canrelease" => empty($activity->afterreleased) && has_capability("mod/beforeafter:releaseafter", $context),
    "releaseurl" => (new moodle_url("/mod/beforeafter/release.php"))->out(false),
    "cmid" => $cm->id,
    "sesskey" => sesskey(),
    "total" => $total,
    "beforecount" => $beforecount,
    "aftercount" => $aftercount,
    "completecount" => $completecount,
    "rows" => $rows,
    "hasrows" => !empty($rows),
    "backurl" => (new moodle_url("/mod/beforeafter/view.php", ["id" => $cm->id]))->out(false),
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template("mod_beforeafter/report", $templatecontext);
echo $OUTPUT->footer();
