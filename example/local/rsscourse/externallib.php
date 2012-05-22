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
 * External local_rsscourse API
 *
 * @subpackage local_rsscourse
 * @copyright  2012 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->dirroot/webservice/rss/lib.php");
require_once("$CFG->dirroot/enrol/externallib.php");

/**
 * Course List functions
 */
class local_rsscourse_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_courses_by_id_parameters() {
        return new external_function_parameters(
                            array('id' => new external_value(PARAM_INT, 'User Id of User to generate course list for', VALUE_REQUIRED))
        );
    }

    /**
     * Get Syllabi information
     * @param int $userid  User Id of enroled person
     * @return array An array of arrays describing enroled courses
     */
    public static function get_courses_by_id($id) {
        global $CFG, $DB, $USER, $FULLME;
        require_once($CFG->dirroot . "/course/lib.php");
        $user = $DB->get_record('user', array('id' => $id), '*', MUST_EXIST);
        
        // now format ready for atom
        $results = array(
                        'expires'     => time() + 60 * 60, // let it live for an hour
                        'id'          => sha1($FULLME . 'get_courses_by_id/' . $id . time()),
                        'link'        => $FULLME,
                        'email'       => $user->email,
                        'uri'         => $CFG->wwwroot . '/user/profile.php?id=' . $id,
                        'title'       => get_string('pluginname', 'local_rsscourse'),
                        'name'        => 'local_rsscourse_get_courses',
                        'description' => get_string('pluginname', 'local_rsscourse'),
                        'updated' => time(),
                        'entries' => array(),
        );
        
        $courses = enrol_get_users_courses($id, true, 'id, shortname, fullname, idnumber, visible, summary, timemodified, timecreated');
        foreach ($courses as $course) {
            $course = (object) $course;
            if (!$course->visible) {
                continue;
            }
            $results['entries'][] = array(
                            'id'        => $course->shortname,
                            'link'      => $CFG->wwwroot . '/course/view.php?id=' . $course->id,
                            'email'     => $user->email,
                            'name'      => $course->fullname,
                            'updated'   => $course->timemodified,
                            'published' => $course->timecreated,
                            'title'     => $course->fullname,
                            'summary'   => $course->summary,
                            'content'   => $course->summary,
            );
        }
        return $results;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    // this must always conform to the webservice_rss_atom_returns() specification
    public static function get_courses_by_id_returns() {
        return webservice_rss_atom_returns();
    }
}
