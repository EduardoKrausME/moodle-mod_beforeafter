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
 * release.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "/../../config.php");

use mod_beforeafter\manager;

$id = required_param("id", PARAM_INT);
require_sesskey();

$cm = get_coursemodule_from_id("beforeafter", $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);
$activity = $DB->get_record("beforeafter", ["id" => $cm->instance], "*", MUST_EXIST);

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability("mod/beforeafter:releaseafter", $context);

manager::release_after((int) $activity->id);

redirect(
    new moodle_url("/mod/beforeafter/report.php", ["id" => $cm->id]),
    get_string("afterreleasedsuccess", "mod_beforeafter"),
    null,
    \core\output\notification::NOTIFY_SUCCESS
);
