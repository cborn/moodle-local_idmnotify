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

defined('MOODLE_INTERNAL') || die();

use local_idmnotify\event\idm_role_added;
use local_idmnotify\event\idm_role_removed;

/**
 * Event observer for role changes.
 */
class local_idmnotify_observer
{

    /**
     * Triggered via role_assigned event.
     *
     * @param \core\event\role_assigned $event
     * @return bool true on success.
     */
    public static function role_assigned(\core\event\role_assigned $event)
    {
        global $CFG, $DB;

        // only course level roles are interesting
        if (!$parentcontext = context::instance_by_id($event->contextid, IGNORE_MISSING)) {
            return true;
        }
        if ($parentcontext->contextlevel != CONTEXT_COURSE) {
            return true;
        }

        $courseid = $event->courseid;
        $user = $DB->get_record("user", array('id' => $event->relateduserid));
        $role = $DB->get_record("role", array('id' => $event->objectid));
        $thiscourse = $DB->get_record("course", array('id' => $courseid));
        $component = $event->other['component'];
        $msg = 'User: ' . $user->username . " Role: " . $role->name . ' Course: ' . $thiscourse->idnumber . ' Component : ' . $component;

        // Automatic enrollments via LDAP will have component set to 'enroll_ldap'.
        // We only care about the manual enrollments.  IdM already knows about the
        // things that are in LDAP.
        if ($component == '') {
            $idm_dsn = 'IDM';
            $idm_user = $CFG->carleton_databases['IDM']['dbuser'];
            $idm_pass = $CFG->carleton_databases['IDM']['dbpass'];
            $idm_connection = odbc_connect($idm_dsn, $idm_user, $idm_pass);
            $idm_sp_text = '{CALL X.Moodle_CoursePersonRoleAdd(\'' . $thiscourse->idnumber . '\', \'' . $user->username . '\', \'manual' . $role->name . '\')}';
            $idm_sp = odbc_prepare($idm_connection, "$idm_sp_text");
            odbc_execute($idm_sp, array());
            $odbc_error = odbc_errormsg();
            odbc_close($idm_connection);

            $logmsg = $msg;
            //Assuming students are role id 5, only warns students.
            if ($role->id == 5 && preg_match('{-[0-9][0-9]-[f|w|s][0-9][0-9]$}', $thiscourse->shortname) && $component == '') {
                $subject = 'Carleton Moodle Enrollment Warning';
                $messagetext = 'You were added to the Moodle site for ' . $thiscourse->shortname . ' by the instructor. If ​the course doesn\'t appear within "My Course Schedule" on your Hub account​ , you are not registered for the course. If you are not registered for this course, you must follow these directions http://apps.carleton.edu/handbook/academics/?policy_id=21446 to get registered.  Call X4288 if you have questions.';
                $from = $DB->get_record("user", array('id' => 2));
                email_to_user($user, $from, $subject, $messagetext, $messagehtml = '', $attachment = '', $attachname = '', $usetrueaddress = true, $replyto = '', $replytoname = '', $wordwrapwidth = 79);
                $logmsg .= ' Email sent';
            };

            $idm_event = idm_role_added::create(array(
                'context' => context_course::instance($courseid),
                'courseid' => $courseid,
                'relateduserid' => $event->relateduserid,
                'other' => array('msg' => $msg, 'odbc_error' => $odbc_error)
            ));
            $idm_event->trigger();
        }

        return true;
    }

    /**
     * Triggered via role_unassigned event.
     *
     * @param \core\event\role_unassigned $event
     * @return bool true on success.
     */
    public static function role_unassigned(\core\event\role_unassigned $event)
    {
        global $CFG, $DB;

        // only course level roles are interesting
        if (!$parentcontext = context::instance_by_id($event->contextid, IGNORE_MISSING)) {
            return true;
        }
        if ($parentcontext->contextlevel != CONTEXT_COURSE) {
            return true;
        }

        $courseid = $event->courseid;
        $user = $DB->get_record("user", array('id' => $event->relateduserid));
        $role = $DB->get_record("role", array('id' => $event->objectid));
        $thiscourse = $DB->get_record("course", array('id' => $courseid));
        $component = $event->other['component'];
        $msg = $user->username . " : " . $role->name . $thiscourse->idnumber;

        // Automatic enrollments via LDAP will have component set to 'enroll_ldap'.
        // We only care about the manual enrollments.  IdM already knows about the
        // things that are in LDAP.
        if ($component == '') {
            $idm_dsn = 'IDM';
            $idm_user = $CFG->carleton_databases['IDM']['dbuser'];
            $idm_pass = $CFG->carleton_databases['IDM']['dbpass'];
            $idm_connection = odbc_connect($idm_dsn, $idm_user, $idm_pass);
            $idm_sp_text = '{CALL X.Moodle_CoursePersonRoleDrop(\'' . $thiscourse->idnumber . '\', \'' . $user->username . '\', \'manual' . $role->name . '\')}';
            $idm_sp = odbc_prepare($idm_connection, "$idm_sp_text");
            odbc_execute($idm_sp, array());
            $odbc_error = odbc_errormsg();
            odbc_close($idm_connection);

            $idm_event = idm_role_removed::create(array(
                'context' => context_course::instance($courseid),
                'courseid' => $courseid,
                'relateduserid' => $event->relateduserid,
                'other' => array('msg' => $msg, 'odbc_error' => $odbc_error)
            ));
            $idm_event->trigger();
        }


        return true;
    }

}
