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
 * Web service for get_course_info
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_graidy\external\mod\quiz;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_multiple_structure;
use external_value;
use moodle_exception;
use context_module;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/externallib.php');

/**
 * Class for get_course_info
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_graderinfo_by_attempt extends external_api {

    /**
     * Defines parameters for this function.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'attemptid' => new external_value(PARAM_INT, 'Quiz Attempt ID', VALUE_REQUIRED),
        ]);
    }

    /**
     * Fetch graderinfo for questions in a quiz attempt.
     *
     * @param int $attemptid The quiz attempt ID.
     * @return array The structured response containing graderinfo.
     * @throws moodle_exception
     */
    public static function execute($attemptid) {
        global $DB, $USER;
        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), ['attemptid' => $attemptid]);
        // Get the quiz attempt details.
        $quizattempt = $DB->get_record('quiz_attempts', ['id' => $params['attemptid']], 'id, uniqueid, userid, quiz');
        if (!$quizattempt) {
            throw new moodle_exception('invalidquizattempt', 'quiz');
        }
        // Fetch the correct course module ID for this quiz.
        $cm = $DB->get_record_sql("
            SELECT cm.id, cm.course
            FROM {course_modules} cm
            JOIN {modules} m ON cm.module = m.id
            WHERE m.name = 'quiz' AND cm.instance = ?
        ", [$quizattempt->quiz]);
        if (!$cm) {
            throw new moodle_exception('invalidcoursemodule', 'error', '', $quizattempt->quiz);
        }
        // Use the correct course module ID for permission validation.
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        // Ensure the user has permission to view this attempt.
        if ($USER->id !== $quizattempt->userid && !has_capability('mod/quiz:viewreports', $context)) {
            throw new moodle_exception('nopermissions', 'quiz');
        }
        // Moodle tables mdl_quiz_attempts (uniqueid) connects to table mdl_question_usages (id)
        // which connects to the table mdl_question_attempts (questionusageid).
        $questions = $DB->get_records_sql("
        SELECT qa.slot, qa.responsesummary, qa.rightanswer, q.id AS questionid, q.qtype, q.name, q.questiontext,
        CASE
        WHEN q.qtype = 'essay' THEN IFNULL(eo.graderinfo, '')
        ELSE ''
        END AS graderinfo
        FROM {question_attempts} qa
        JOIN {question} q ON qa.questionid = q.id
        LEFT JOIN {qtype_essay_options} eo ON q.id = eo.questionid
        WHERE qa.questionusageid = ?
        ORDER BY qa.slot
    ", [$quizattempt->uniqueid]);
        // Format the response.
        $result = [];
        foreach ($questions as $question) {
            $result[] = [
                'slot'       => $question->slot,
                'questionid' => $question->questionid,
                'qtype'      => $question->qtype, // To confirm the question type.
                'graderinfo' => $question->graderinfo ?? '', // Avoid null values.
                'name' => $question->name,
                'questiontext' => $question->questiontext,
                'responsesummary' => $question->responsesummary,
                'rightanswer' => $question->rightanswer,
            ];
        }
        return $result;
    }
    /**
     * Defines the return structure.
     *
     * @return external_multiple_structure
     */
    public static function execute_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'slot'       => new external_value(PARAM_INT, 'Question slot in the quiz'),
                'questionid' => new external_value(PARAM_INT, 'Question ID'),
                'graderinfo' => new external_value(PARAM_RAW, 'Grader information'),
                'name' => new external_value(PARAM_RAW, 'Name'),
                'questiontext' => new external_value(PARAM_RAW, 'Question Text'),
                'responsesummary' => new external_value(PARAM_RAW, 'Student Response'),
                'rightanswer' => new external_value(PARAM_RAW, 'Right Answer'),
            ])
        );
    }
}
