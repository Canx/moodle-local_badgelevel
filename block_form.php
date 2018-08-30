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

require_once("{$CFG->libdir}/formslib.php");

class block_form extends moodleform {
    public function __construct($courseid) {
        parent::__construct(null, array('courseid' => $courseid));
    }

    public function definition() {
        $this->courseid = $this->_customdata['courseid'];
        $mform =& $this->_form;

        $mform->addElement('hidden', 'courseid', $this->courseid);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('select', 'blockid', 'Select block', $this->get_blocks(), null);
        $mform->addElement('submit', 'blockselected', 'Select',
            ['formaction' => '/local/badgelevel/index.php']);
    }

    private function get_blocks() {
        global $DB;

        $sql = "SELECT bi.id, bi.blockname FROM {block_instances} bi
                INNER JOIN {context} c ON c.id = bi.parentcontextid
                WHERE c.instanceid = :contextid
                AND blockname IN ('xp', 'showgrade')";

        $params = [
                'contextid' => $this->courseid,
                'blocks' => '"xp" , "showgrade"'
        ];

        $blocks = array();

        $records = $DB->get_records_sql($sql, $params);

        foreach ($records as $record) {
            $blocks[$record->id] = $record->blockname . " (" . $record->id . ")";
        }

        return $blocks;
    }
}
