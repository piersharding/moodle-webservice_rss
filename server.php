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
 * RSS web service entry point. The authentication is done via tokens.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// disable moodle specific debug messages and any errors in output
define('NO_DEBUG_DISPLAY', true);
define('NO_MOODLE_COOKIES', true);

require('../../config.php');
require_once("$CFG->dirroot/webservice/rss/locallib.php");

if (!webservice_protocol_is_enabled('rss')) {
    debugging('WS RSS DISABLED (admin variables)');
    rss_error();
}

$rssformat = optional_param('wsrssformat', 'atom', PARAM_ALPHANUM);
//remove the alt from the request
if(isset($_GET['wsrssformat'])) {
    unset($_GET['wsrssformat']);
}
if(isset($_POST['wsrssformat'])) {
    unset($_POST['wsrssformat']);
}

$server = new webservice_rss_server(WEBSERVICE_AUTHMETHOD_PERMANENT_TOKEN, $rssformat);
$server->run();
die;

