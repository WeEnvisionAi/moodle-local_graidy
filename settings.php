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
 * Settings file.
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_graidy\settings\admin_setting_custom_webservicesoverview;

defined('MOODLE_INTERNAL') || die();

// Only show this section to site administrators.
if ($hassiteconfig) {
    require_once($CFG->dirroot . '/local/graidy/lib.php');
    // Add a new settings page.
    $settings = new admin_settingpage('local_graidy_settings', get_string('pluginname', 'local_graidy'));

    $ADMIN->add('localplugins', new admin_category('local_graidy', get_string('plugin_heading', 'local_graidy')));

    $siteurl = preg_replace('/\/admin.*/', '', get_config('moodle', 'wwwroot')); // Removes /admin or any trailing path.

    $settings->add(new admin_setting_heading(
        'local_graidy/registration_info',
        get_string('registration_get_started', 'local_graidy'),
        get_string('registration_info', 'local_graidy', $siteurl)
    ));

     // Add a heading for the Base URL section.
    $settings->add(new admin_setting_heading(
        'local_graidy/baseurl_heading',
        get_string('baseurl_heading', 'local_graidy'),
        get_string('baseurl_heading_desc', 'local_graidy')
    ));

    // Add a field for the GRAiDY Base URL.
    $settings->add(new admin_setting_configtext(
        'local_graidy/baseurl',
        get_string('baseurl', 'local_graidy'),
        get_string('baseurl_desc', 'local_graidy'),
        'https://portal.graidy.tech',
        PARAM_URL
    ));

        // Add a field for the Organization API Key.
        $settings->add(new admin_setting_configtext(
            'local_graidy/organizationtoken',
            get_string('organizationtoken', 'local_graidy'),
            get_string('organizationtoken_desc', 'local_graidy'),
            '',
            PARAM_TEXT
        ));
    // Include the custom settings class.
    require_once(dirname(__FILE__) . '/classes/settings/admin_setting_custom_webservicesoverview.php');
    $settings->add(new admin_setting_custom_webservicesoverview());
    // Register settings page.
    $ADMIN->add('localplugins', $settings);
}
