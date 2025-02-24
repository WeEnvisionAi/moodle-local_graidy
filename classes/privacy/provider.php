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
 * Privacy Provider file.
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_graidy\privacy;

use core_privacy\local\metadata\collection;

defined('MOODLE_INTERNAL') || die();
/**
 * Privacy Subsystem implementing null_provider.
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\null_provider {

    /**
     * Get the language string identifier with the component's language
     * file to explain why this plugin stores no data.
     *
     * @return string
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }

    /**
     * Returns metadata about data sent to external systems.
     *
     * @param collection $collection The metadata collection object.
     * @return collection The updated metadata collection.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location_link(
            'https://portal.graidy.tech/',
            get_string('graidy_portal', 'local_graidy'),
            get_string('graidy_portal_privacy_description', 'local_graidy'),
            [
                'userid' => get_string('privacy_userid', 'local_graidy'),
                'email' => get_string('privacy_email', 'local_graidy'),
                'firstname' => get_string('privacy_firstname', 'local_graidy'),
                'username' => get_string('privacy_username', 'local_graidy'),
                'lastname' => get_string('privacy_lastname', 'local_graidy'),
                'token' => get_string('privacy_token', 'local_graidy'),
            ]
        );

        return $collection;
    }
}
