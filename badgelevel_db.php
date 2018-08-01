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

class badgelevel_db {

    public static $table = "local_badgelevel";

    public function __construct(int $courseid, int $blockid) {
        $this->courseid = $courseid;
        $this->blockid = $blockid;
    }

    public function get_freebadges() {
        global $DB;

        $sql = "SELECT * FROM {badge} WHERE id NOT IN
                (SELECT badge_id FROM {" . self::$table . "} WHERE block_id = ?) AND courseid = ?";

        $rs = $DB->get_records_sql($sql, array($this->blockid, $this->courseid));

        $badges = array();
        foreach ($rs as $record) {
            $badges[$record->id] = $record->name;
        }

        return $badges;
    }

    public function get_freelevels($maxlevel) {
        $levels = array();
        for ($level = 1; $level <= $maxlevel; $level++) {
            $levels[$level] = 'Level ' . $level;
        }

        return $levels;
    }

    public function get_badgelevels() {
        global $DB;

        $sql = "SELECT * FROM ({" . self::$table . "} lb
                INNER JOIN {badge} badge ON lb.badge_id = badge.id)
                WHERE lb.block_id = ? AND badge.courseid = ? ORDER BY level";

        $rs = $DB->get_records_sql($sql, array($this->blockid, $this->courseid));

        $badgelevels = array();
        foreach ($rs as $record) {
            $badgelevels[$record->level] = [ $record->badge_id => $record->name ];
        }

        return $badgelevels;
    }

    // Update level-badge association
    // TODO: only update level-badge from block called!
    public function update($level, $badge) {
        global $DB;

        $sql = "UPDATE {" . self::$table . "}
                SET badge_id = ?
                WHERE level = ? AND block_id = ?";

        $DB->execute($sql, array($badge, $level, $this->blockid));
    }

    // Delete level-badge association
    // TODO: only delete level from block called!
    public function delete($level) {
        global $DB;

        $DB->delete_records(self::$table, array('level' => $level, 'block_id' => $this->blockid));
    }

    // Add level-badge association.
    public function add($level, $badge) {
        global $DB;

        $record = new StdClass();
        $record->level = $level;
        $record->badge_id = $badge;
        $record->block_id = $this->blockid;

        $this->delete($level);
        $DB->insert_record(self::$table, $record);
    }

}
