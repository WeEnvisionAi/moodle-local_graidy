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
 * Web service for get_course_content
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_graidy\external\course;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use context_course;

defined('MOODLE_INTERNAL') || die;

require_once("{$CFG->libdir}/externallib.php");

/**
 * Class for get_course_content
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_course_content extends external_api {

    /**
     * Defines the expected parameters for the execute function.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            [
                'courseid' => new external_value(PARAM_INT, 'Course ID', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Execute the function logic.
     */
    public static function execute($courseid) {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), ['courseid' => $courseid]);

        // Get the course context and validate access.
        $context = context_course::instance($courseid);
        self::validate_context($context);

        // Capability check: Ensure the user has the capability to view the course.
        require_capability('moodle/course:view', $context);

        // Fetch course sections.
        $sections = $DB->get_records('course_sections', ['course' => $courseid], 'section ASC');

        // Get modinfo for this course.
        $modinfo = get_fast_modinfo($courseid, $USER->id);

        // Initialize the response.
        $response = [];

        foreach ($sections as $section) {
            // Check if the section is visible to the user.
            $sectionvisible = $section->visible || has_capability('moodle/course:viewhiddensections', $context);

            // Get section modules.
            $sectioninfo = $modinfo->get_section_info_all();
            $sectionmodules = isset($sectioninfo[$section->id]) ? $sectioninfo[$section->id]->sequence : '';
            $moduleids = explode(',', $sectionmodules);

            $modules = [];
            foreach ($moduleids as $cmid) {
                if (!isset($modinfo->cms[$cmid])) {
                    continue; // Skip if the module is not available.
                }
                $cm = $modinfo->cms[$cmid];
                if (!$cm->uservisible) {
                    continue; // Skip modules not visible to the user.
                }

                // Check if the module has associated content.
                $fs = get_file_storage();
                $files = $fs->get_area_files($cm->context->id, $cm->modname, 'content', false, 'sortorder', false);
                $hascontent = !empty($files);

                // Module details.
                $modules[] = [
                    'id' => $cm->id,
                    'url' => $cm->url->out(false),
                    'name' => $cm->name,
                    'instance' => $cm->instance,
                    'contextid' => $cm->context->id,
                    'visible' => $cm->visible,
                    'uservisible' => $cm->uservisible,
                    'visibleoncoursepage' => $cm->visibleoncoursepage,
                    'modicon' => '',
                    'modname' => $cm->modname,
                    'modplural' => get_string('modulenameplural', $cm->modname),
                    'availability' => $cm->availability,
                    'indent' => $cm->indent,
                    'onclick' => $cm->onclick,
                    'afterlink' => null,
                    'customdata' => '""',
                    'noviewlink' => !$cm->has_view(),
                    'completion' => $cm->completion,
                    'completiondata' => [
                        'state' => $cm->completionstate,
                        'timecompleted' => $cm->timecompleted,
                        'overrideby' => null,
                        'valueused' => false,
                        'hascompletion' => $cm->completion,
                        'isautomatic' => $cm->completionexpected,
                        'istrackeduser' => $cm->completiontracked,
                        'uservisible' => $cm->uservisible,
                        'details' => [],
                    ],
                    'downloadcontent' => $hascontent == true ? 1 : 0,
                    'dates' => [], // Add date-related data if available.
                    'contents' => [], // Add module-specific content here if needed.
                    'contentsinfo' => [
                        'filescount' => count($files),
                        'filessize' => array_sum(array_map(fn($file) => $file->get_filesize(), $files)),
                        'lastmodified' => empty($files) ? 0 : max(array_map(fn($file) => $file->get_timemodified(), $files)),
                        'mimetypes' => array_unique(array_map(fn($file) => $file->get_mimetype(), $files)),
                        'repositorytype' => '',
                    ],
                ];
            }

            // Add section data.
            $response[] = [
                'id' => $section->id,
                'name' => $section->name ?: get_section_name($courseid, $section),
                'visible' => $section->visible,
                'summary' => $section->summary,
                'summaryformat' => $section->summaryformat,
                'section' => $section->section,
                'hiddenbynumsections' => $section->section > count($sectioninfo) ? 1 : 0,
                'uservisible' => $sectionvisible,
                'modules' => $modules,
            ];
        }

        return $response;
    }

    /**
     * Execute the function logic return.
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                [
                    'id' => new external_value(PARAM_INT, 'Section ID'),
                    'name' => new external_value(PARAM_TEXT, 'Section name'),
                    'visible' => new external_value(PARAM_INT, 'Visibility status'),
                    'summary' => new external_value(PARAM_RAW, 'Summary'),
                    'summaryformat' => new external_value(PARAM_INT, 'Summary format'),
                    'section' => new external_value(PARAM_INT, 'Section number'),
                    'hiddenbynumsections' => new external_value(PARAM_INT, 'Hidden by number of sections'),
                    'uservisible' => new external_value(PARAM_BOOL, 'Is the section visible to the user'),
                    'modules' => new external_multiple_structure(
                        new external_single_structure(
                            [
                                'id' => new external_value(PARAM_INT, 'Module ID'),
                                'url' => new external_value(PARAM_URL, 'Module URL'),
                                'name' => new external_value(PARAM_TEXT, 'Module name'),
                                'instance' => new external_value(PARAM_INT, 'Module instance'),
                                'contextid' => new external_value(PARAM_INT, 'Context ID'),
                                'visible' => new external_value(PARAM_INT, 'Visibility status'),
                                'uservisible' => new external_value(PARAM_BOOL, 'Is the module visible to the user'),
                                'visibleoncoursepage' => new external_value(PARAM_INT, 'Visibility on course page'),
                                'modicon' => new external_value(PARAM_URL, 'Module icon URL'),
                                'modname' => new external_value(PARAM_TEXT, 'Module name type'),
                                'modplural' => new external_value(PARAM_TEXT, 'Module plural name'),
                                'availability' => new external_value(PARAM_RAW, 'Module availability'),
                                'indent' => new external_value(PARAM_INT, 'Indentation level'),
                                'onclick' => new external_value(PARAM_TEXT, 'Onclick action'),
                                'afterlink' => new external_value(PARAM_RAW, 'Afterlink data'),
                                'customdata' => new external_value(PARAM_RAW, 'Custom data'),
                                'noviewlink' => new external_value(PARAM_BOOL, 'No view link flag'),
                                'completion' => new external_value(PARAM_INT, 'Completion status'),
                                'completiondata' => new external_single_structure(
                                    [
                                        'state' => new external_value(PARAM_INT, 'Completion state'),
                                        'timecompleted' => new external_value(PARAM_INT, 'Time completed'),
                                        'overrideby' => new external_value(PARAM_RAW, 'Override by user'),
                                        'valueused' => new external_value(PARAM_BOOL, 'Is value used'),
                                        'hascompletion' => new external_value(PARAM_BOOL, 'Has completion tracking'),
                                        'isautomatic' => new external_value(PARAM_BOOL, 'Is automatic completion'),
                                        'istrackeduser' => new external_value(PARAM_BOOL, 'Is the user tracked'),
                                        'uservisible' => new external_value(PARAM_BOOL, 'Is visible to the user'),
                                        'details' => new external_multiple_structure(
                                            new external_single_structure([], 'Additional completion details', VALUE_OPTIONAL)
                                        ),
                                    ],
                                    'Completion data',
                                    VALUE_OPTIONAL
                                ),
                                'downloadcontent' => new external_value(PARAM_INT, 'Allows download content'),
                                'dates' => new external_multiple_structure(
                                    new external_single_structure(
                                        [
                                            'label' => new external_value(PARAM_TEXT, 'Date label'),
                                            'timestamp' => new external_value(PARAM_INT, 'Date timestamp'),
                                            'dataid' => new external_value(PARAM_TEXT, 'Data identifier'),
                                        ],
                                        'Date details'
                                    ),
                                    'List of dates',
                                    VALUE_OPTIONAL
                                ),
                                'contents' => new external_multiple_structure(
                                    new external_single_structure(
                                        [
                                            'type' => new external_value(PARAM_TEXT, 'Content type'),
                                            'filename' => new external_value(PARAM_TEXT, 'Filename'),
                                            'filepath' => new external_value(PARAM_TEXT, 'Filepath'),
                                            'filesize' => new external_value(PARAM_INT, 'File size'),
                                            'fileurl' => new external_value(PARAM_RAW, 'File URL', VALUE_OPTIONAL),
                                            'content' => new external_value(PARAM_RAW, 'Content data'),
                                            'timecreated' => new external_value(PARAM_INT, 'Time created'),
                                            'timemodified' => new external_value(PARAM_INT, 'Time modified'),
                                            'sortorder' => new external_value(PARAM_INT, 'Sort order'),
                                            'userid' => new external_value(PARAM_INT, 'User ID', VALUE_OPTIONAL),
                                            'author' => new external_value(PARAM_RAW, 'Author', VALUE_OPTIONAL),
                                            'license' => new external_value(PARAM_RAW, 'License', VALUE_OPTIONAL),
                                        ],
                                        'Content details'
                                    ),
                                    'Contents data',
                                    VALUE_OPTIONAL
                                ),
                                'contentsinfo' => new external_single_structure(
                                    [
                                        'filescount' => new external_value(PARAM_INT, 'Number of files'),
                                        'filessize' => new external_value(PARAM_INT, 'Total file size'),
                                        'lastmodified' => new external_value(PARAM_INT, 'Last modified timestamp'),
                                        'mimetypes' => new external_multiple_structure(
                                            new external_value(PARAM_TEXT, 'MIME type'),
                                            'MIME types'
                                        ),
                                        'repositorytype' => new external_value(PARAM_RAW, 'Repository type'),
                                    ],
                                    'Contents info',
                                    VALUE_OPTIONAL
                                ),
                            ],
                            'Module details'
                        )
                    ),
                ]
            )
        );
    }
}
