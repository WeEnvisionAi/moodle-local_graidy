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
 * Web service for local_graidy
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    // This is a custom external function.
    'local_graidy_get_course_info' => [
        'classname'   => 'local_graidy\external\course\get_course_info',
        'description' => 'Get course information.',
        'type'        => 'read',
        'capabilities' => 'moodle/course:view, moodle/course:update, moodle/course:viewhiddencourses',
        'ajax' => true,
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    // This provides similar functionality but using the CORE external function.
    'local_graidy_core_course_get_courses' => [
        'classname' => 'core_course_external',
        'methodname' => 'get_courses',
        'classpath' => 'course/externallib.php',
        'description' => 'Return course details',
        'type' => 'read',
        'capabilities' => 'moodle/course:view, moodle/course:update, moodle/course:viewhiddencourses',
        'ajax' => true,
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_core_user_get_users' => [
        'classname'   => 'core_user_external',
        'methodname'  => 'get_users',
        'classpath'   => 'user/externallib.php',
        'description' => 'Retrieve user information based on filters.',
        'type'        => 'read',
        'capabilities' => 'moodle/user:viewdetails, moodle/site:viewparticipants',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_get_course_content' => [
        'classname'   => 'local_graidy\external\course\get_course_content',
        'description' => 'Get course content.',
        'type'        => 'read',
        'capabilities' => 'moodle/course:view, moodle/course:update, moodle/course:viewhiddencourses',
        'ajax' => true,
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_assign_get_assignments' => [
        'classname'   => 'mod_assign_external',
        'methodname'  => 'get_assignments',
        'classpath'   => 'mod/assign/externallib.php',
        'description' => 'Retrieve course assignments.',
        'type'        => 'read',
        'capabilities' => 'moodle/course:view, moodle/course:update, moodle/course:viewhiddencourses',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_gradereport_user_get_grades_table' => [
        'classname' => 'gradereport_user\\external\\user',
        'methodname' => 'get_grades_table',
        'classpath'   => 'grade/report/user/classes/external/user.php',
        'description' => 'Retrieve a table of grades for a specific user.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_page_get_pages_by_courses' => [
        'classname'   => 'mod_page_external',
        'methodname'  => 'get_pages_by_courses',
        'classpath'   => 'mod/page/externallib.php',
        'description' => 'Retrieve a list of pages in specified courses.',
        'type'        => 'read',
    ],
    'local_graidy_mod_url_get_urls_by_courses' => [
        'classname'   => 'mod_url_external',
        'methodname'  => 'get_urls_by_courses',
        'classpath'   => 'mod/url/externallib.php',
        'description' => 'Retrieve a list of URLs in specified courses.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_book_get_books_by_courses' => [
        'classname'   => 'mod_book_external',
        'methodname'  => 'get_books_by_courses',
        'classpath'   => 'mod/book/externallib.php',
        'description' => 'Retrieve a list of books in specified courses.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_folder_get_folders_by_courses' => [
        'classname'   => 'mod_folder_external',
        'methodname'  => 'get_folders_by_courses',
        'classpath'   => 'mod/folder/externallib.php',
        'description' => 'Retrieve a list of folders in specified courses.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_assign_get_grades' => [
        'classname'   => 'mod_assign_external',
        'methodname'  => 'get_grades',
        'classpath'   => 'mod/assign/externallib.php',
        'description' => 'Retrieve the grades for assignments in a course.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_assign_get_submissions' => [
        'classname'   => 'mod_assign_external',
        'methodname'  => 'get_submissions',
        'classpath'   => 'mod/assign/externallib.php',
        'description' => 'Retrieve submissions for assignments.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_core_enrol_get_enrolled_users' => [
        'classname'   => 'core_enrol_external',
        'methodname'  => 'get_enrolled_users',
        'classpath'   => 'enrol/externallib.php',
        'description' => 'Retrieve a list of users enrolled in a specific course.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_assign_save_grade' => [
        'classname'   => 'mod_assign_external',
        'methodname'  => 'save_grade',
        'classpath'   => 'mod/assign/externallib.php',
        'description' => 'Save a grade for a student in an assignment.',
        'type'        => 'write',
        'capabilities' => 'mod/assign:grade, moodle/grade:edit',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_quiz_get_quizzes_by_courses' => [
        'classname'   => 'mod_quiz_external',
        'methodname'  => 'get_quizzes_by_courses',
        'classpath'   => 'mod/quiz/externallib.php',
        'description' => 'Retrieve a list of quizzes from specified courses.',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'mod/quiz:view, moodle/course:view',
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_quiz_get_user_attempts' => [
        'classname'   => 'mod_quiz_external',
        'methodname'  => 'get_user_attempts',
        'classpath'   => 'mod/quiz/externallib.php',
        'description' => 'Retrieve a list of quiz attempts by a specific user.',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'mod/quiz:view, moodle/course:view, mod/quiz:attempt, mod/quiz:viewreports, moodle/user:viewdetails',
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_quiz_get_attempt_review' => [
        'classname'   => 'mod_quiz_external',
        'methodname'  => 'get_attempt_review',
        'classpath'   => 'mod/quiz/externallib.php',
        'description' => 'Retrieve the review information for a specific quiz attempt.',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities' => 'mod/quiz:reviewmyattempts, mod/quiz:viewreports',
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
    'local_graidy_mod_quiz_get_graderinfo_by_attempt' => [
        'classname'   => 'local_graidy\external\mod\quiz\get_graderinfo_by_attempt',
        'methodname'  => 'execute',
        'description' => 'Retrieve graderinfo for questions in a quiz attempt.',
        'type'        => 'read',
        'ajax'        => true,
        'services'    => [MOODLE_OFFICIAL_MOBILE_SERVICE],
    ],
];

// Defining a pre-built service with the following web services available.
// Pre-built services are installed by default and cannot be edited by admins.
$services = [
    // The name of the service.
    // This does not need to include the component name.
    'GRAiDY service' => [

        // A list of external functions available in this service.
        'functions' => [
            'local_graidy_get_course_info',
            'local_graidy_core_course_get_courses',
            'local_graidy_core_user_get_users',
            'local_graidy_get_course_content',
            'local_graidy_mod_assign_get_assignments',
            'local_graidy_gradereport_user_get_grades_table',
            'local_graidy_mod_page_get_pages_by_courses',
            'local_graidy_mod_url_get_urls_by_courses',
            'local_graidy_mod_book_get_books_by_courses',
            'local_graidy_mod_folder_get_folders_by_courses',
            'local_graidy_mod_assign_get_grades',
            'local_graidy_mod_assign_get_submissions',
            'local_graidy_core_enrol_get_enrolled_users',
            'local_graidy_mod_assign_save_grade',
            'local_graidy_mod_quiz_get_quizzes_by_courses',
            'local_graidy_mod_quiz_get_user_attempts',
            'local_graidy_mod_quiz_get_attempt_review',
            'local_graidy_mod_quiz_get_graderinfo_by_attempt',
        ],

        // If set, the external service user will need this capability to access
        // any function of this service.
        'requiredcapability' => '',

        // If enabled, the Moodle administrator must link a user to this service from the Web UI.
        'restrictedusers' => 1,

        // Whether the service is enabled by default or not.
        'enabled' => 1,

        // This field os optional, but requried if the `restrictedusers` value is
        // set, so as to allow configuration via the Web UI.
        'shortname' => 'local_graidy',
    ],
];
