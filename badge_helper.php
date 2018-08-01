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

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/badgeslib.php');

class badge_helper {

    // TODO: redundant in badgelevel_db.
    private static $table = "block_showgrade_level_badge";

    public static function check_and_issue_badge($user, $level, $course, $block) {
        global $DB;

        $sql = "SELECT id FROM
                (SELECT b.id FROM {badge} b
                INNER JOIN {" . self::$table . "} AS lb
                ON b.id = lb.badge_id WHERE lb.level <= ? AND lb.block_id = ? AND b.courseid = ?) AS b
                WHERE id NOT IN
                (SELECT badgeid FROM {badge_issued} WHERE userid = ?)";

        $rs = $DB->get_records_sql($sql, array($level, $block, $course, $user));

        foreach ($rs as $record) {
            $badge = new badge($record->id);
            $badge->issue($user);
        }
    }
}
