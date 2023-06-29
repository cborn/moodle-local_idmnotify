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
 * The idm_notified event.
 *
 * @package    local_idmnotify
 * @copyright  2012 Carleton College
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_idmnotify\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The idm_notified event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - PUT INFO HERE
 * }
 *
 * @since     Moodle 3.6.3
 * @copyright 2012 Carleton College
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
class idm_role_added extends \core\event\base
{
    protected function init()
    {
        $this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    public static function get_name()
    {
        return get_string('eventidm_role_added', 'local_idmnotify');
    }

    public function get_description()
    {
        return "IDM received add command for user '{$this->relateduserid}' in course '{$this->courseid}'.";
    }

    public function get_url()
    {
        return '';
    }

    public function get_legacy_logdata()
    {
        // Override if you are migrating an add_to_log() call.
        // add_to_log($courseid, $module = "idmnotify", $action = "add", $url = '', $info = $logmsg, $cm = 0, $user = $ra->userid);
        return array($this->courseid, 'idmnotify', 'add',
            '', '',
            $this->objectid, $this->contextinstanceid);
    }
}
