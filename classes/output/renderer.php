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
 * Output renderer file.
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class for rendering html
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/local/graidy/lib.php');

/**
 * Execute the function logic.
 */
class local_graidy_renderer extends plugin_renderer_base {
    /**
     * Render the "GRAiDY Grading" button inside assignments.
     */
    public function render_graidy_button($courseid) {
        global $DB, $USER;
        // 1. Get the GRAiDY base URL from plugin settings.
        $graidybaseurl = get_config('local_graidy', 'baseurl');
        // 2. Retrieve the user’s token from the external service.
        // (You must have an external service with shortname 'my_service_shortname'.)
        $service = $DB->get_record('external_services', ['shortname' => 'local_graidy'], '*', MUST_EXIST);
        $tokenrecord = $DB->get_record('external_tokens', [
            'userid' => $USER->id,
            'externalserviceid' => $service->id,
        ], '*', IGNORE_MISSING);

        if ($tokenrecord) {
            $token = $tokenrecord->token;
        } else {
            // If no token is found, handle this gracefully.
            // For now, let's just define it as an empty string or show a notice.
            $token = local_graidy_get_or_create_token($USER->id);
        }
        // 3. Build the final URL string:
        // https://baseurl/moodle/course/[courseid]/[token]
        $finalpath = '/moodle/course/' . $courseid . '/' . $token;
        $fullurl   = $graidybaseurl . $finalpath;
        // 4. Convert to a moodle_url if you like (or use string).
        $targeturl = new moodle_url($fullurl);
        // 5. Render the HTML button that opens in a new tab.
        return html_writer::tag('a',
            get_string('graidy_grading_button', 'local_graidy'),
            [
                'href'   => $targeturl,
                'class'  => 'btn btn-primary',
                'target' => '_blank',
                'style'  => 'margin-left: 10px;',
            ]
        );
    }
    /**
     * Render the "GRAiDY Grading" iframe.
     */
    public function render_iframe($url, $width = '100%', $height = '800px', ) {
        return html_writer::tag('iframe', '', [
            'src' => $url,
            'width' => $width,
            'height' => $height,
            'frameborder' => '0',
            'allowfullscreen' => 'allowfullscreen',
        ]);
    }
}
