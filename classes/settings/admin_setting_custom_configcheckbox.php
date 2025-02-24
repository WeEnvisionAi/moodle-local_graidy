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
 * Special setting for admin_setting_configcheckbox
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_graidy;

/**
 * Class for overriding admin_setting_configcheckbox.
 *
 * @package    local_graidy
 * @copyright  2025 We Envision AI <info@weenvisionai.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_custom_configcheckbox extends \admin_setting_configcheckbox {

    /**
     * @var bool $disabled
     */
    private bool $disabled = false;
    /**
     * @var bool $checked
     */
    private bool $checked = false;

    /**
     * Allows the checkbox to be disabled.
     * @param bool $disabled
     */
    public function set_disabled($disabled = false) {
        $this->disabled = $disabled;
    }

    /**
     * Allows the checkbox to be checked.
     * @param bool $checked
     */
    public function set_checked($checked = false) {
        $this->checked = $checked;
    }

    /**
     * Returns an XHTML checkbox field.
     *
     * @param string $data If $data matches yes then checkbox is checked
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $readonly = $this->disabled;
        $checked = $this->checked;

        if ($this->is_readonly()) {
            $readonly = $this->is_readonly();
        } else if ($this->disabled) {
            $readonly = true;
        }

        if ((string) $data === $this->yes) {
            $checked = (string) $data === $this->yes;
        } else if ($this->checked) {
            $checked = true;
        }

        $context = (object) [
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'no' => $this->no,
            'value' => $this->yes,
            'checked' => $checked,
            'readonly' => $readonly,
        ];

        $default = $this->get_defaultsetting();
        if (!is_null($default)) {
            if ((string)$default === $this->yes) {
                $defaultinfo = get_string('checkboxyes', 'admin');
            } else {
                $defaultinfo = get_string('checkboxno', 'admin');
            }
        } else {
            $defaultinfo = null;
        }

        $element = $OUTPUT->render_from_template('core_admin/setting_configcheckbox', $context);

        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }
}
