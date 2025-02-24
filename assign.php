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
 * Assignment page for Local GRAiDY plugin.
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_graidy;

require_once(__DIR__ . '/../../config.php');

// Get the course ID from the URL.
$courseid = required_param('courseid', PARAM_INT); // Required course ID.
$sectionid = required_param('sectionid', PARAM_INT);
$moduleid = required_param('moduleid', PARAM_INT); // Required module ID.
$contentid = required_param('contentid', PARAM_INT);

// Load the course object.
$course = get_course($courseid);
require_login($course);

// Setup the context for this course.
$context = context_module::instance($moduleid);
$PAGE->set_context($context);

// Setup the page URL, title, and heading.
$PAGE->set_url('/local/graidy/assign.php', ['courseid' => $courseid]);
// Example: You could use $course->fullname as the page heading/title.
$PAGE->set_title(get_string('pluginname', 'local_graidy'));
$PAGE->set_heading($course->fullname);

// Get base portal URL from plugin settings, or fallback.
$baseurl = get_config('local_graidy', 'baseurl');
if (empty($baseurl)) {
    // Fallback or handle error.
    $baseurl = 'https:/portal.graidy.tech';
}

// Determine which path to load in the iframe (dashboard, etc.).
$path = optional_param('path', 'dashboard', PARAM_ALPHANUMEXT);

// Construct the full iframe URL.
$iframeurl = rtrim($baseurl, '/') . '/' . $path;

// Output the page header.
echo $OUTPUT->header();

// Embed your iframe or content here.
// 1. Get the GRAiDY base URL from plugin settings.
$graidybaseurl = get_config('local_graidy', 'baseurl');
$organizationtoken = get_config('local_graidy', 'organizationtoken');
$token = local_graidy_get_or_create_token($USER->id);

// Set iframe url.
$iframeurl = $graidybaseurl . '/moodle/plugin/assign/' . $courseid . '/' . $contentid . '/' .
$moduleid . '/' . $sectionid . '/' . $USER->id . '/' . $token . '/' . $organizationtoken;

$output = $PAGE->get_renderer('local_graidy');
echo $output->render_iframe($iframeurl);

// Finish the page.
echo $OUTPUT->footer();
