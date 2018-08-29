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
        $freelevels = $this->db->get_freelevels(20);
        $badgelevels = $this->db->get_badgelevels();

        // TODO: action and level should be passed in customdata
        $this->action = empty($_GET['action']) ? "show" : $_GET['action'];
        $this->level = empty($_GET['level']) ? null : $_GET['level'];

        $mform =& $this->_form;

        // Save courseid and blockid params.
        $mform->addElement('hidden', 'courseid', $courseid);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'blockid', $blockid);
        $mform->setType('blockid', PARAM_INT);

        // TODO: Show block name selected
        $mform->addElement('header', 'blockname', "Block " . $blockid);

        // Show current level associations with badges.
        //$mform->addElement('header', 'currentlevels', 'Current badges');
        foreach ($badgelevels as $level => $badge) {
                // Add badge to first in array.
                $currentbadges = $badge + $freebadges;
                $group = array();
                $group[0] = $mform->createElement('select', 'badge' . $level, 'Level ' . $level, $currentbadges, null);
                $group[1] = $mform->createElement('submit', 'updatebutton' . $level,
                    'Update', ['formaction' => '/local/badgelevel/index.php?action=update&level=' . $level]);
                $group[2] = $mform->createElement('submit', 'deletebutton' . $level,
                    'Delete', ['formaction' => '/local/badgelevel/index.php?action=delete&level=' . $level]);
                $mform->addElement('group', 'level' . $level, 'Level ' . $level, $group, false);
        }

        // Add new level association only if available badges and levels.
	// TODO: refactor urls
        if ($freebadges && $freelevels) {
            //$mform->addElement('header', 'newlevelheader', 'Link badge to level');
            $group = array();
            $group[0] = $mform->createElement('select', 'newlevel', 'Level', $freelevels, null);
            $group[1] = $mform->createElement('select', 'newbadge', 'Badge', $freebadges, null);
            $group[2] = $mform->createElement('submit', 'newbutton', 'Add',
                ['formaction' => '/local/badgelevel/index.php?action=add']);
            $group[3] = $mform->createElement('submit', 'cancelbutton', 'Cancel',
                ['formaction' => '/local/badgelevel/index.php?action=cancel']);
            $mform->addElement('group', 'newlevelgroup', null, $group, false);
            $mform->setType('level', PARAM_INT);
        }

        //$mform->addElement('header', 'badgeheader', 'Badges');
        $mform->addElement('static', 'badgelink', '', '<a href="/badges/index.php?type=2&id=' . $courseid . '">Add badge</a>');
    }

    public function update() {
        $level = $this->level;
        $formdata = get_object_vars($this->get_data());
        $badge = $formdata['level' . $level]['badge' . $level];
        $this->db->update($level, $badge);
    }

    public function delete() {
        $level = $this->level;
        $this->db->delete($level);
    }

    public function add() {
        $level = $this->level;
        $formdata = get_object_vars($this->get_data());
        $level = $formdata['newlevelgroup']['newlevel'];
        $badge = $formdata['newlevelgroup']['newbadge'];
        $this->db->add($level, $badge);
    }
}
