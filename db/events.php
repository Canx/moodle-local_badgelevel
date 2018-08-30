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

// List of observers.
$observers = array(
    // Support for block_xp plugin.
    array(
        'eventname'   => '\block_xp\event\user_leveledup',
        'callback'    => 'local_badgelevel_observer::xp_user_leveledup',
    ),
    // Support for block_showgrade plugin.
    array(
        'eventname'   => '\block_showgrade\event\user_leveledup',
        'callback'    => 'local_badgelevel_observer::showgrade_user_leveledup')
);

