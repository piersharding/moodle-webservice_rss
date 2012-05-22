moodle-webservice_rss
=====================

Is a Moodle Web Service Protocol for generating RSS feeds.
Support for ATOM, RSS91, and RSS2.0 is included.


General installation proceedures are here:
http://docs.moodle.org/20/en/Installing_contributed_modules_or_plugins

The basic process is:
Download https://gitorious.org/moodle-webservice_rss/moodle-webservice_rss/archive-tarball/master
unpack the file (probably called master) with tar -xzvf master
This will give you a directory called moodle-webservice_rss-moodle-webservice_rss
Move this directory and rename it into it's final position:
mv moodle-webservice_rss-moodle-webservice_rss <Moodle dirroot>/webserivce/rss

Alternatively you can use git:
cd <Moodle dirroot>/webservice
git clone git@gitorious.org:moodle-webservice_rss/moodle-weservice_rss.git rss

Be careful about leaving the .git directory in your live environment.


Usage
=====

To make use of this delivery protocol, you need to create web service
functions that generate output that conform to the output structure 
defined in webservice/rss/lib.php - function webservice_rss_atom_returns()

This defines a generic structure like:
    return new external_single_structure(
                    array(
                            'expires'     => new external_value(PARAM_INT, 'a unix timestamp to set the expires header with'),
                            'id'          => new external_value(PARAM_RAW, 'Atom document Id'),
                            'title'       => new external_value(PARAM_RAW, 'Atom document Title'),
                            'link'        => new external_value(PARAM_RAW, 'Atom document Link'),
                            'description' => new external_value(PARAM_RAW, 'RSS document description', VALUE_OPTIONAL),
                            'language'    => new external_value(PARAM_RAW, 'RSS document language', VALUE_OPTIONAL),
                            'email'       => new external_value(PARAM_RAW, 'Atom document Author Email', VALUE_OPTIONAL),
                            'name'        => new external_value(PARAM_RAW, 'Atom document Author Name', VALUE_OPTIONAL),
                            'updated'     => new external_value(PARAM_INT, 'AAtom document Updated date', VALUE_OPTIONAL),
                            'uri'         => new external_value(PARAM_RAW, 'Atom document URI', VALUE_OPTIONAL),
                            'entries'     => new external_multiple_structure(
                                            new external_single_structure(
                                                            array(
                                                                    'id'           => new external_value(PARAM_RAW, 'Atom entry Id', VALUE_OPTIONAL),
                                                                    'link'         => new external_value(PARAM_RAW, 'Atom entry Link'),
                                                                    'email'        => new external_value(PARAM_RAW, 'Atom entry Author Link', VALUE_OPTIONAL),
                                                                    'name'         => new external_value(PARAM_RAW, 'Atom entry Author Name', VALUE_OPTIONAL),
                                                                    'updated'      => new external_value(PARAM_INT, 'Atom entry updated date', VALUE_OPTIONAL),
                                                                    'published'    => new external_value(PARAM_INT, 'Atom entry published date', VALUE_OPTIONAL),
                                                                    'title'        => new external_value(PARAM_RAW, 'Atom entry Title'),
                                                                    'description'  => new external_value(PARAM_RAW, 'RSS entry description', VALUE_OPTIONAL),
                                                                    'summary'      => new external_value(PARAM_RAW, 'Atom entry Summary', VALUE_OPTIONAL),
                                                                    'content'      => new external_value(PARAM_RAW, 'Atom entry Content', VALUE_OPTIONAL),
                                                            ), 'Atom entry', VALUE_OPTIONAL)
                                            , 'Entries', VALUE_OPTIONAL),
                    )
    );

An example implementation is found in the example/local/rsscourse directory.  Deploy this in <moodle root>/local/rsscourse/ .

Copyright (C) Piers Harding 2012 and beyond, All rights reserved

moodle-webservice_rss free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

