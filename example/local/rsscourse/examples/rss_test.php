<?php

// define your token - you need to change this
// $token = '953fdd66778eb09097b39391767490ce';

// must be run from the command line
if (isset($_SERVER['REMOTE_ADDR']) || isset($_SERVER['GATEWAY_INTERFACE'])){
    die('Direct access to this script is forbidden.');
}

define('CLI_SCRIPT', 1);
require('../../../config.php');
include 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::autoload('Zend_Loader');

include ("Console/Getopt.php");
/**
 *   command line help text
 */
function help_text() {
    return "
    Options are:
        --token      - Web Services token - mandatory
        --id         - ID for user
    ";
}

//fetch arguments
$args = Console_Getopt::readPHPArgv();
//checking errors for argument fetching
if (PEAR::isError($args)) {
    error_log('Invalid arguments (1): ' . help_text());
    exit(1);
}
// remove stderr/stdout redirection args
$args = preg_grep('/2>&1/', $args, PREG_GREP_INVERT);
$console_opt = Console_Getopt::getOpt($args, 't:i:e', array('token=', 'id='));

if (PEAR::isError($console_opt)) {
    error_log('Invalid arguments (2): ' . help_text());
    exit(1);
}

// must supply at least one arg for the action to perform
if (count($args) < 2) {
    error_log('Invalid arguments: you must atleast specify --token' . help_text());
    exit(1);
}

// parse back the options
$token = '';
$id = '';
$opts = $console_opt[0];
if (sizeof($opts) > 0) {
    // if at least one option is present
    foreach ($opts as $o) {
        switch ($o[0]) {
            // handle the size option
            case 't':
            case '--token':
                $token = $o[1];
                break;
            case 'i':
            case '--id':
                $id = $o[1];
                break;
        }
    }
}

//set web service url server
$serverurl = $CFG->wwwroot.'/webservice/rss/server.php';

// specify the call parameters
$params = array(
    'id' => $id, // the params to passed to the function
    'wsrssformat' => 'rss91',
    'wsfunction' => 'local_rsscourse_get_courses',   // the function to be called
    'wstoken' => $token, //token need to be passed in the url
);

// do the REST call
error_log('going to call: '.$serverurl);
$client = new Zend_Http_Client($serverurl);
try {
    $client->setParameterPost($params);
    $response = $client->request('POST');
    var_dump ($response->getBody());
    //var_dump (json_decode($response->getBody()));
} catch (exception $exception) {
    var_dump ($exception);
}
var_dump($client->getLastRequest());
exit(0);
