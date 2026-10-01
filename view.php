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
 * view.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

use core\output\notification;
use mod_beforeafter\form\response_form;
use mod_beforeafter\manager;

$id = required_param("id", PARAM_INT);

$cm = get_coursemodule_from_id("beforeafter", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record("beforeafter", ["id" => $cm->instance], "*", MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/beforeafter:view", $context);

$PAGE->set_url("/mod/beforeafter/view.php", ["id" => $cm->id]);
$PAGE->set_title(format_string($activity->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$entry = manager::get_entry((int)$activity->id, (int)$USER->id);
$canrespond = has_capability("mod/beforeafter:submit", $context);
$canreport = has_capability("mod/beforeafter:viewreport", $context);

$mform = null;
if ($canrespond) {
    $stage = empty($activity->afterreleased) ? "before" : "after";
    $prompt = $stage === "before" ? $activity->beforeprompt : $activity->afterprompt;
    $initialtext = $stage === "before" ? $entry->beforetext : $entry->aftertext;
    $initialformat = $stage === "before" ? $entry->beforeformat : $entry->afterformat;

    $mform = new response_form(null, [
        "stage" => $stage,
        "prompt" => $prompt,
        "initialtext" => $initialtext,
        "initialformat" => $initialformat,
    ]);

    if ($data = $mform->get_data()) {
        $editor = $data->response_editor;
        manager::save_response(
            $activity,
            (int)$USER->id,
            (string)$data->stage,
            (string)$editor["text"],
            (int)$editor["format"]
        );
        redirect($PAGE->url, get_string("responsesaved", "mod_beforeafter"), null, notification::NOTIFY_SUCCESS);
    }
}

$beforehtml = $entry->beforetext !== ""
    ? format_text($entry->beforetext, $entry->beforeformat, ["context" => $context])
    : "";
$afterhtml = $entry->aftertext !== ""
    ? format_text($entry->aftertext, $entry->afterformat, ["context" => $context])
    : "";

$templatecontext = [
    "intro" => format_module_intro("beforeafter", $activity, $cm->id, false),
    "beforeprompt" => format_string($activity->beforeprompt),
    "afterprompt" => format_string($activity->afterprompt),
    "beforehtml" => $beforehtml,
    "afterhtml" => $afterhtml,
    "hasbefore" => $entry->beforetext !== "",
    "hasafter" => $entry->aftertext !== "",
    "afterreleased" => !empty($activity->afterreleased),
    "beforetime" => $entry->timebefore ? userdate($entry->timebefore) : "",
    "aftertime" => $entry->timeafter ? userdate($entry->timeafter) : "",
    "canreport" => $canreport,
    "reporturl" => (new moodle_url("/mod/beforeafter/report.php", ["id" => $cm->id]))->out(false),
];

// Refresh after a save handled by redirect so the template always shows persisted data.
echo $OUTPUT->header();
echo $OUTPUT->render_from_template("mod_beforeafter/student_view", $templatecontext);

if ($mform) {
    $mform->display();
}

echo $OUTPUT->footer();
