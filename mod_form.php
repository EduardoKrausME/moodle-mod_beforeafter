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
 * mod_form.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/course/moodleform_mod.php");

/**
 * Class mod_beforeafter_mod_form.
 */
class mod_beforeafter_mod_form extends moodleform_mod {
    /**
     * Defines the activity form.
     */
    public function definition(): void {
        $mform = $this->_form;

        $mform->addElement("header", "general", get_string("general", "form"));
        $mform->addElement("text", "name", get_string("name", "mod_beforeafter"), ["size" => 64]);
        $mform->setType("name", PARAM_TEXT);
        $mform->addRule("name", null, "required", null, "client");
        $mform->addRule("name", get_string("maximumchars", "", 255), "maxlength", 255, "client");

        $this->standard_intro_elements();

        $mform->addElement("header", "prompts", get_string("prompts", "mod_beforeafter"));

        $mform->addElement("textarea", "beforeprompt", get_string("beforeprompt", "mod_beforeafter"), [
            "rows" => 3,
            "cols" => 70,
        ]);
        $mform->setType("beforeprompt", PARAM_TEXT);
        $mform->setDefault("beforeprompt", get_string("defaultbeforeprompt", "mod_beforeafter"));
        $mform->addRule("beforeprompt", null, "required", null, "client");

        $mform->addElement("textarea", "afterprompt", get_string("afterprompt", "mod_beforeafter"), [
            "rows" => 3,
            "cols" => 70,
        ]);
        $mform->setType("afterprompt", PARAM_TEXT);
        $mform->setDefault("afterprompt", get_string("defaultafterprompt", "mod_beforeafter"));
        $mform->addRule("afterprompt", null, "required", null, "client");

        $mform->addElement("static", "releaseinfo", get_string("secondmoment", "mod_beforeafter"),
            get_string("releaseinfodesc", "mod_beforeafter"));

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }
}
