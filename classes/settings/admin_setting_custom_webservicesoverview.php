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
 * Settings file for overriding admin_setting_webservicesoverview.
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_graidy\settings;

use html_writer;
use html_table;
use moodle_url;

/**
 * Class for overriding admin_setting_webservicesoverview.
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_custom_webservicesoverview extends \admin_setting_webservicesoverview {

    /**
     * Builds the XHTML to display the control
     *
     * @param string $data Unused
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $OUTPUT;

        $return = "";
        $brtag = html_writer::empty_tag('br');

        // GRAiDY accessing Moodle with Token.
        $return .= $OUTPUT->heading(get_string('graidyaccesstomoodle', 'local_graidy'), 3, 'main');
        $table = new html_table();
        $table->head = [get_string('step', 'local_graidy'), get_string('webservicestatus', 'local_graidy'),
            get_string('webservicedesc', 'local_graidy')];
        $table->colclasses = ['leftalign step', 'leftalign status', 'leftalign description'];
        $table->id = 'graidyaccesstomoodle';
        $table->attributes['class'] = 'admintable wsoverview generaltable';
        $table->data = [];

        $return .= $brtag . get_string('graidyaccesstomoodledesc', 'local_graidy')
                . $brtag . $brtag;

        // 1. Enable Web Services.
        $row = [];
        $url = new moodle_url("/admin/search.php?query=enablewebservices");
        $row[0] = "1. " . html_writer::tag('a', get_string('enablews', 'local_graidy'),
                        ['href' => $url]);
        $status = html_writer::tag('span', get_string('no'), ['class' => 'badge badge-danger']);
        if ($CFG->enablewebservices) {
            $status = get_string('yes');
        }
        $row[1] = $status;
        $row[2] = get_string('enablewsdesc', 'local_graidy');
        $table->data[] = $row;

        // 2. Enable protocols.
        $row = [];
        $url = new moodle_url("/admin/settings.php?section=webserviceprotocols");
        $row[0] = "2. " . html_writer::tag('a', get_string('enableprotocols', 'local_graidy'),
                        ['href' => $url]);
        $status = html_writer::tag('span', get_string('none'), ['class' => 'badge badge-danger']);
        // Retrieve activated protocol.
        $activeprotocols = empty($CFG->webserviceprotocols) ?
                [] : explode(',', $CFG->webserviceprotocols);
        if (!empty($activeprotocols)) {
            $status = "";
            foreach ($activeprotocols as $protocol) {
                $status .= $protocol . $brtag;
            }
        }
        $row[1] = $status;
        $row[2] = get_string('enableprotocolsdesc', 'local_graidy');
        $table->data[] = $row;

        // 3. Create user account.
        $row = [];
        $url = new moodle_url("/user/editadvanced.php?id=-1");
        $row[0] = "3. " . html_writer::tag('a', get_string('createuser', 'local_graidy'),
                        ['href' => $url]);
        $row[1] = "";
        $row[2] = get_string('createuserdesc', 'local_graidy');
        $table->data[] = $row;

        // 4. Add capability to users.
        $row = [];
        // An export of a role of the permissions the web service user will need.
        $roledownloadurl = new moodle_url('/local/graidy/graidy_webservice.xml');
        $roledownloadlink = html_writer::tag('a', 'graidy_webservice.xml',
                        ['href' => $roledownloadurl, 'download' => '']);

        $url = new moodle_url("/admin/roles/define.php?action=add");
        $row[0] = "4. " . html_writer::tag('a', get_string('assignroletouser', 'local_graidy'),
                        ['href' => $url]);
        $row[1] = "";
        $row[2] = get_string('assignroletouserdesc', 'local_graidy') . ' ' . $roledownloadlink;
        $table->data[] = $row;

        // 5. Select a web service.
        $row = [];
        $url = new moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "5. " . html_writer::tag('a', get_string('selectservice', 'local_graidy'),
                        ['href' => $url]);
        $row[1] = "";
        $row[2] = get_string('selectservicedesc', 'local_graidy');
        $table->data[] = $row;

        // 6. Add the specific user.
        $row = [];
        $url = new moodle_url("/admin/settings.php?section=externalservices");
        $row[0] = "6. " . html_writer::tag('a', get_string('selectspecificuser', 'local_graidy'),
                        ['href' => $url]);
        $row[1] = "";
        $row[2] = get_string('selectspecificuserdesc', 'local_graidy');
        $table->data[] = $row;

        // 7. Create token for the specific user.
        $row = [];
        $url = new moodle_url('/admin/webservice/tokens.php', ['action' => 'create']);
        $row[0] = "7. " . html_writer::tag('a', get_string('createtokenforuser', 'local_graidy'),
                        ['href' => $url]);
        $row[1] = "";
        $row[2] = get_string('createtokenforuserdesc', 'local_graidy');
        $table->data[] = $row;

        // 8. Enable the documentation.
        $row = [];
        $url = new moodle_url("/admin/search.php?query=enablewsdocumentation");
        $row[0] = "8. " . html_writer::tag('a', get_string('enabledocumentation', 'local_graidy'),
                        ['href' => $url]);
        $status = '<span class="warning">' . get_string('no') . '</span>';
        if ($CFG->enablewsdocumentation) {
            $status = get_string('yes');
        }
        $row[1] = $status;
        $row[2] = get_string('enabledocumentationdesc', 'local_graidy');
        $table->data[] = $row;

        $return .= html_writer::table($table);

        return highlight($query, $return);
    }
}
