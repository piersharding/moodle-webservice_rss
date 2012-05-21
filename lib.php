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
 * parameter definition for output of any Atom generator
 *
 * Returns description of method result value
 * @return external_description
 */
function webservice_rss_atom_returns() {
    return new external_single_structure(
                    array(
                                    'id'          => new external_value(PARAM_RAW, 'Atom document Id'),
                                    'title'       => new external_value(PARAM_RAW, 'Atom document Title'),
                                    'link'        => new external_value(PARAM_RAW, 'Atom document Link'),
                                    'description' => new external_value(PARAM_RAW, 'RSS document description', VALUE_OPTIONAL),
                                    'language'    => new external_value(PARAM_RAW, 'RSS document language', VALUE_OPTIONAL),
                                    'email'       => new external_value(PARAM_RAW, 'Atom document Author Email', VALUE_OPTIONAL),
                                    'name'        => new external_value(PARAM_RAW, 'Atom document Author Name', VALUE_OPTIONAL),
                                    'updated'     => new external_value(PARAM_RAW, 'AAtom document Updated date', VALUE_OPTIONAL),
                                    'uri'         => new external_value(PARAM_RAW, 'Atom document URI', VALUE_OPTIONAL),
                                    'entries'     => new external_multiple_structure(
                                                    new external_single_structure(
                                                                    array(
                                                                                    'id'           => new external_value(PARAM_RAW, 'Atom entry Id'),
                                                                                    'link'         => new external_value(PARAM_RAW, 'Atom entry Link'),
                                                                                    'email'        => new external_value(PARAM_RAW, 'Atom entry Author Link'),
                                                                                    'name'         => new external_value(PARAM_RAW, 'Atom entry Author Name'),
                                                                                    'updated'      => new external_value(PARAM_RAW, 'Atom entry updated date'),
                                                                                    'published'    => new external_value(PARAM_RAW, 'Atom entry published date'),
                                                                                    'title'        => new external_value(PARAM_RAW, 'Atom entry Title'),
                                                                                    'description'  => new external_value(PARAM_RAW, 'RSS entry description', VALUE_OPTIONAL),
                                                                                    'summary'      => new external_value(PARAM_RAW, 'Atom entry Summary', VALUE_OPTIONAL),
                                                                                    'content'      => new external_value(PARAM_RAW, 'Atom entry Content', VALUE_OPTIONAL),
                                                                    ), 'Atom entry', VALUE_OPTIONAL)
                                                    , 'Entries', VALUE_OPTIONAL),
                    )
    );
}



/**
 * format a date to the w3 datetime format
 *
 * @param integer unix timestamp to format
 * @return string W3 Date format
 */
function webservice_rss_format_rfc3339_date($date) {
    $d = strftime('%Y-%m-%dT%H:%M:%S%z', $date);
    return substr($d, 0, -2) . ':' . substr($d, -2);
}
