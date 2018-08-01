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


defined('MOODLE_INTERNAL') || die;

function local_badgelevel_extend_navigation_course($navigation, $course, $context) {   

    if  (has_capability('moodle/badges:awardbadge', $context)) {
        $url = new moodle_url('/local/badgelevel/index.php', array('courseid' => $course->id));
        $name = 'Award badges when leveling up';
        $navigation->get('coursebadges')->add($name, $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/block', ''));
    }
}
