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
 * Displays Graidy portal in an iframe
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('assign', $id);
if (!$cm) {
    $cm = get_coursemodule_from_id('quiz', $id);
    if (!$cm) {
        throw new moodle_exception('invalidcoursemodule', 'error');
    }
}

$context = context_module::instance($cm->id);
require_login($cm->course, false, $cm);

$PAGE->set_url(new moodle_url('/local/graidy/index.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('indexheading', 'local_graidy'));
$PAGE->set_heading(get_string('indexheading', 'local_graidy'));

echo $OUTPUT->header();
echo get_string('indexwelcome', 'local_graidy');
echo $OUTPUT->footer();
