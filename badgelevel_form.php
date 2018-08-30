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
require_once('./badgelevel_db.php');

class badgelevel_form extends moodleform {

    public function __construct($db) {
        parent::__construct(null, array('db' => $db));
    }

    public function definition() {
        $this->db = $this->_customdata['db'];
        $courseid = $this->db->courseid;
        $blockid = $this->db->blockid;
        $freebadges = $this->db->get_freebadges();

        // TODO: get max levels from get_maxlevels(), and refactor block_showgrade.php!
        $badgelevels = $this->db->get_badgelevels();

        // TODO: action and level should be passed in customdata.
        $this->action = empty($_GET['action']) ? "show" : $_GET['action'];
        $this->level = empty($_GET['level']) ? null : $_GET['level'];

        $mform =& $this->_form;

        // Save courseid and blockid params.
        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'blockid', $blockid);
        $mform->setType('blockid', PARAM_INT);

        // TODO: Show block name selected.
        $mform->addElement('header', 'blockname', "Block " . $blockid);

        // Show current level associations with badges.
        if ($badgelevels) {
            $mform->addElement('html','<table><tr><th>Level</th><th>Badge</th><th></th></tr>');
            foreach ($badgelevels as $level => $badge) {
                    $mform->addElement('html','<tr><td style="min-width: 100px">' . $level . '</td>');
                    $mform->addElement('html','<td style="min-width: 200px">' . current($badge) . '</td>');
                    $mform->addElement('html','<td><input type="submit" class="btn btn-primary" name="deletebutton' . $level .'" id="id_deletebutton' . $level . '" value="Delete" formaction="/local/badgelevel/index.php?action=delete&amp;level=' .$level . '"></td></tr>');
            }
            $mform->addElement('html','</table>');
        }

        // Add new level association only if available badges and levels.
        // TODO: refactor urls.
        if ($freebadges) {
            $mform->addElement('header', 'newlevelheader', 'Link badge to level');
            $group = array();
            $mform->addElement('text', 'newlevel', 'Level', array("size" => 17)); 
            $mform->setType('newlevel', PARAM_INT);
            $mform->addElement('select', 'newbadge', 'Badge', $freebadges, array("style" => "min-width: 200px"));
            $mform->addElement('submit', 'newbutton', 'Add',
                ['formaction' => '/local/badgelevel/index.php?action=add']);
            $mform->addElement('submit', 'cancelbutton', 'Cancel',
                ['formaction' => '/local/badgelevel/index.php?action=cancel']);
            $mform->setType('level', PARAM_INT);
        }
    }

    public function delete() {
        $level = $this->level;
        $this->db->delete($level);
    }

    public function add() {
        $level = $this->level;
        $formdata = get_object_vars($this->get_data());
        $level = $formdata['newlevel'];
        $badge = $formdata['newbadge'];
        $this->db->add($level, $badge);
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
    
        if (empty($data['newlevel'])) {
            $errors['newlevel'] = get_string('missinglevel', 'local_badgelevel');
        }
        elseif ($data['newlevel'] < 1) {
            $errors['newlevel'] = get_string('missinglevel', 'local_badgelevel');
        }
    
        return $errors;
    }
}
