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
 * Hub external functions and service definitions.
 *
 * @package    local_rsscourse
 * @copyright  2012 Piers Harding
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(

        'local_rsscourse_get_courses' => array(
                'classname'   => 'local_rsscourse_external',
                'methodname'  => 'get_courses_by_id',
                'classpath'   => 'local/rsscourse/externallib.php',
                'description' => 'Get course information for a given user',
                'type'        => 'read',
        ),
);

$services = array(
        'Course List Interface' => array(
                'functions' => array ('local_rsscourse_get_courses'),
                'enabled' => 1,
        ),
);
