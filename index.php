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

require_once('../../config.php');
require_once('badgelevel_form.php');
require_once('block_form.php');

require_login();

// TODO: restrict access to course teachers!

$courseid = required_param('courseid', PARAM_INT);
$blockid = optional_param('blockid', null, PARAM_INT);

$url = new moodle_url('/local/badgelevel/index.php', array('courseid' => $courseid, 'blockid' => $blockid));
$courseurl = new moodle_url('/course/view.php', array('id' => $courseid));

// TODO: check permissions and that course exists!
$course = $DB->get_record('course', array('id' => $courseid));
$coursecontext = context_course::instance($courseid);
$PAGE->set_context($coursecontext);
$PAGE->set_pagelayout('incourse');
$PAGE->set_url($url);


$hdr = 'Link badges to levels';
$PAGE->set_heading(format_string($course->fullname, true, array('context' => $coursecontext)) . ': ' . $hdr);
$PAGE->set_title($hdr);


if ($blockid) {
    $db = new badgelevel_db($courseid, $blockid);
    
    $form = new badgelevel_form($db);
    
    switch ($form->action) {
        case 'update':
            $form->update();
            redirect($url);
            break;
        case 'delete':
            $form->delete();
            redirect($url);
            break;
        case 'add':
            $form->add();
            redirect($url);
            break;
        case 'cancel':
            redirect($courseurl);
            break;
    }
} else {
    // No blockid passed, we have to choose it first!
    $form = new block_form($courseid);   
}

echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();

