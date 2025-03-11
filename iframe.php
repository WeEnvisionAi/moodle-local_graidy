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

$id = required_param('id', PARAM_INT); // Get the module ID.
$context = context_module::instance($id);
require_login();

$PAGE->set_url('/local/graidy/iframe.php', ['id' => $id]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');

$baseurl = get_config('local_graidy', 'baseurl'); // Get GRAiDY Base URL.
if (!$baseurl || empty($baseurl)) {
    echo $OUTPUT->header();
    echo "<div class='alert alert-danger'>" . get_string('iframebaseurlerror', 'local_graidy') ."</div>";
    echo $OUTPUT->footer();
    exit;
}

$iframeurl = $baseurl . "/grading?id=" . $id;

echo $OUTPUT->header();
echo "<h2>" . get_string('iframeheading', 'local_graidy') . "</h2>";
echo "<iframe src='$iframeurl' width='100%' height='800px' style='border: none;'></iframe>";
echo $OUTPUT->footer();
