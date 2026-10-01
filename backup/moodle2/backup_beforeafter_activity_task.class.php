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
 * backup_beforeafter_activity_task.class.php
 *
 * @package   mod_beforeafter
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . "/mod/beforeafter/backup/moodle2/backup_beforeafter_stepslib.php");

/**
 * Class backup_beforeafter_activity_task.
 */
class backup_beforeafter_activity_task extends backup_activity_task {
    /**
     * No plugin-specific backup settings.
     */
    protected function define_my_settings(): void {
    }

    /**
     * Adds the activity structure step.
     */
    protected function define_my_steps(): void {
        $this->add_step(new backup_beforeafter_activity_structure_step("beforeafter_structure", "beforeafter.xml"));
    }

    /**
     * Encodes links to this activity.
     *
     * @param string $content
     * @return string
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, "/");
        $content = preg_replace(
            "/(" . $base . "\\/mod\\/beforeafter\\/view.php\\?id\\=)([0-9]+)/",
            "$@BEFOREAFTERVIEWBYID*$2@$",
            $content
        );
        $content = preg_replace(
            "/(" . $base . "\\/mod\\/beforeafter\\/index.php\\?id\\=)([0-9]+)/",
            "$@BEFOREAFTERINDEX*$2@$",
            $content
        );

        return $content;
    }
}
