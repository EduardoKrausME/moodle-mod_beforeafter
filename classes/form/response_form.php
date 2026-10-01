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
 * response_form.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_beforeafter\form;

use moodleform;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/formslib.php");

/**
 * Class response_form.
 */
class response_form extends moodleform {
    /**
     * Defines the response editor.
     */
    public function definition(): void {
        $mform = $this->_form;
        $stage = (string)$this->_customdata["stage"];
        $prompt = (string)$this->_customdata["prompt"];
        $initialtext = (string)($this->_customdata["initialtext"] ?? "");
        $initialformat = (int)($this->_customdata["initialformat"] ?? FORMAT_HTML);

        $mform->addElement("html", "<div class=\"beforeafter-prompt\">" . format_string($prompt) . "</div>");
        $mform->addElement("editor", "response_editor", get_string("yourreflection", "mod_beforeafter"), ["rows" => 10]);
        $mform->setType("response_editor", PARAM_RAW);
        $mform->addRule("response_editor", null, "required", null, "client");
        $mform->setDefault("response_editor", [
            "text" => $initialtext,
            "format" => $initialformat,
        ]);

        $mform->addElement("hidden", "stage", $stage);
        $mform->setType("stage", PARAM_ALPHA);

        $label = $stage === "before"
            ? get_string("savebefore", "mod_beforeafter")
            : get_string("saveafter", "mod_beforeafter");

        $this->add_action_buttons(false, $label);
    }
}
