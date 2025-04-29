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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/webservice/lib.php');
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/externallib.php');

/**
 * Execute the function logic to return a user token.
 *
 * @param int $userid The ID of the user.
 * @return string The generated or existing token.
 */
function local_graidy_get_or_create_token($userid) {
    global $DB;

    // 1. Get the external service record by shortname.
    $service = $DB->get_record('external_services', [
        'shortname' => 'local_graidy',  // Change to your actual service shortname.
        'enabled'   => 1,
    ], '*', MUST_EXIST);

    // 2. Check if the user already has a token for this service.
    $existingtoken = $DB->get_record('external_tokens', [
        'userid'            => $userid,
        'externalserviceid' => $service->id,
    ]);

    if ($existingtoken) {
        // Reuse existing token.
        return $existingtoken->token;
    }

    // 3. Generate a new token if none exists.
    $token = external_generate_token(
        EXTERNAL_TOKEN_PERMANENT, // Info constant from webservice/lib.php.
        $service->id,
        $userid,
        null,
    );
    return $token;
}

/**
 * Execute the function logic to extend the settings navigation.
 *
 * @param settings_navigation $settingsnav The settings navigation object.
 * @param context $context The context of the current page.
 * @return void
 */
function local_graidy_extend_settings_navigation(settings_navigation $settingsnav, context $context) {
    global $PAGE;
    global $DB;
    global $USER;

    // Check if the user has access to the external service.
    $service = $DB->get_record('external_services', [
        'shortname' => 'local_graidy',  // Change to your actual service shortname.
        'enabled'   => 1,
    ], '*', MUST_EXIST);

    if (!$service) {
        return; // Service does not exist.
    }

    $userhasaccess = $DB->record_exists('external_services_users', [
        'externalserviceid' => $service->id,
        'userid' => $USER->id,
    ]);

    if (!$userhasaccess) {
        return; // User does not have access to the external service.
    }

    // Check if the context is module-level.
    if ($context->contextlevel === CONTEXT_MODULE) {
        if (!$PAGE->cm) {
            // Use $PAGE->set_cm() to set the course module context.
            $cm = get_coursemodule_from_id(null, $context->instanceid, 0, false, MUST_EXIST);
            $PAGE->set_cm($cm);
        }
    } else {
        return; // Exit if not module-level context.
    }

    if ($PAGE->cm->modname === 'assign' || $PAGE->cm->modname === 'quiz') {
        // Get the settings navigation node (More menu).
        $modulenode = $settingsnav->get('modulesettings');
        $cm = $DB->get_record('course_modules', ['id' => $PAGE->cm->id], 'id, section');
        $quizcmid = $PAGE->cm->id; // Current Quiz Module ID.
        $courseid = $PAGE->course->id; // Course ID.
        // Fetch course sections and modules directly from the database.
        $sections = $DB->get_records_sql("
        SELECT cm.id AS moduleid, cs.id AS sectionid, cs.name AS sectionname 
        FROM {course_sections} cs
        LEFT JOIN {course_modules} cm ON cm.section = cs.id
        WHERE cs.course = :courseid
        ", ['courseid' => $courseid]);

        // Match the Quiz Module ID with Course Sections.
        $matchingsectionid = null;
        foreach ($sections as $section) {
            if ($section->moduleid == $quizcmid) {
                // Match Module ID.
                $matchingsectionid = $section->sectionid;
                break;
            }
        }

        if ($modulenode !== null) {
            $modulenode->add(
                get_string('tab_' . $PAGE->cm->modname, 'local_graidy'),
                new moodle_url('/local/graidy/' . $PAGE->cm->modname . '.php', [
                    'courseid' => $PAGE->course->id,
                    'sectionid' => $PAGE->cm->section,
                    'moduleid' => $PAGE->cm->id,
                    'contentid' => $matchingsectionid,
                    'type' => $PAGE->cm->modname,
                ]),
                navigation_node::TYPE_CUSTOM,
                null,
                'local_graidy_tab_' . $PAGE->cm->modname
            );
        } else {
            debugging('local_graidy: modulesettings node not found for ' . $PAGE->cm->modname);
        }
    }
}

/**
 * Execute the function logic to extend the navigation on the course page.
 *
 * @param navigation_node $parentnode The parent navigation node.
 * @param stdClass $course The course object.
 * @param context_course $context The course context.
 * @return void
 */
function local_graidy_extend_navigation_course(navigation_node $parentnode, stdClass $course, context_course $context) {
    global $DB;
    global $USER;

    // Check if the user has access to the external service.
    $service = $DB->get_record('external_services', [
        'shortname' => 'local_graidy',  // Change to your actual service shortname.
        'enabled'   => 1,
    ], '*', MUST_EXIST);

    if (!$service) {
        return; // Service does not exist.
    }
    $userhasaccess = $DB->record_exists('external_services_users', [
        'externalserviceid' => $service->id,
        'userid' => $USER->id,
    ]);

    if (!$userhasaccess) {
        return; // User does not have access to the external service.
    }
    // Create the URL with the "id" param set to the course's ID.
    $url = new moodle_url('/local/graidy/course.php', ['courseid' => $course->id]);
    // Add a navigation node.
    $parentnode->add(
        get_string('tab_course', 'local_graidy'),
        $url,
        navigation_node::TYPE_CUSTOM
    );
}
