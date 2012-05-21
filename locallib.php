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
 * RSS web service implementation classes and methods.
 *
 * @package   webservice
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->dirroot/webservice/lib.php");
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/rsslib.php');

// get the additinal Zend Feed libs
$path = $CFG->dirroot . '/webservice/rss/externallib';
set_include_path($path . PATH_SEPARATOR . get_include_path());

/**
 * RSS service server implementation.
 * @author Piers Harding
 */
class webservice_rss_server extends webservice_base_server {

    /** @property string $alt return method (rss91 / rss / atom) */
    protected $rssformat;

    /**
     * Contructor
     */
    public function __construct($authmethod, $rssformat = 'xml') {
        parent::__construct($authmethod);
        $this->wsname = 'rss';
        $this->rssformat = ($rssformat != 'rss91' && $rssformat != 'rss' && $rssformat != 'atom') ? 'atom' : $rssformat; //sanity check
    }

    /**
     * This method parses the $_POST and $_GET superglobals and looks for
     * the following information:
     *  1/ user authentication - username+password or token (wsusername, wspassword and wstoken parameters)
     *  2/ function name (wsfunction parameter)
     *  3/ function parameters (all other parameters except those above)
     *
     * @return void
     */
    protected function parse_request() {

        //Get GET and POST paramters
        $methodvariables = array_merge($_GET,$_POST);

        if ($this->authmethod == WEBSERVICE_AUTHMETHOD_USERNAME) {
            $this->username = isset($methodvariables['wsusername']) ? $methodvariables['wsusername'] : null;
            unset($methodvariables['wsusername']);

            $this->password = isset($methodvariables['wspassword']) ? $methodvariables['wspassword'] : null;
            unset($methodvariables['wspassword']);

            $this->functionname = isset($methodvariables['wsfunction']) ? $methodvariables['wsfunction'] : null;
            unset($methodvariables['wsfunction']);

            $this->parameters = $methodvariables;

        } else {
            $this->token = isset($methodvariables['wstoken']) ? $methodvariables['wstoken'] : null;
            unset($methodvariables['wstoken']);

            $this->functionname = isset($methodvariables['wsfunction']) ? $methodvariables['wsfunction'] : null;
            unset($methodvariables['wsfunction']);

            $this->parameters = $methodvariables;
        }
    }

    /**
     * Send the result of function call to the WS client
     * formatted as XML document.
     * @return void
     */
    protected function send_response() {

        error_log('send response: '.$this->rssformat);
        //Check that the returned values are valid
        $validatedvalues = null;
        try {
            // they must always conform to the RSS return specification
            $validatedvalues = external_api::clean_returnvalue(webservice_rss_atom_returns(), $this->returns);
        } catch (Exception $ex) {
            $exception = $ex;
            error_log('WS RSS: return values validation failure - '.var_export($exception, true));
        }
        if (!empty($exception)) {
            $response =  $this->generate_error($exception);
        } else {
            //We can now convert the response to the requested RSS format
            include 'Zend/Loader/Autoloader.php';
            Zend_Loader_Autoloader::autoload('Zend_Loader');
            if ($this->rssformat == 'atom') {
                error_log('generating ATOM');
                $feed = new Zend_Feed_Writer_Feed();
                $feed->setTitle($validatedvalues['title']);
                $feed->setLink($validatedvalues['link']);
                $feed->setFeedLink($validatedvalues['link'], 'atom');
                $feed->addAuthor(array(
                                'name'  => $validatedvalues['name'],
                                'email' => $validatedvalues['email'],
                                'uri'   => $validatedvalues['uri'],
                ));
                $feed->setDateModified($validatedvalues['updated']);
                foreach ($validatedvalues['entries'] as $e) {
                    $entry = $feed->createEntry();
                    $entry->setTitle($e['title']);
                    $entry->setLink($e['link']);
                    $entry->addAuthor(array(
                                    'name'  => $e['name'],
                                    'email' => $e['email'],
                                    'uri'   => $validatedvalues['uri'],
                    ));
                    $entry->setDateModified($e['updated']);
                    $entry->setDateCreated($e['published']);
                    $summary = (empty($e['summary']) ? $e['title'] : $e['summary']);
                    $entry->setDescription($summary);
                    $content = (empty($e['content']) ? $summary : $e['content']);
                    $entry->setContent($content);
                    $feed->addEntry($entry);
                }
                $response = $feed->export('atom');
            }
            else if ($this->rssformat == 'rss91') {
                error_log('generating RSS 0.91');
                $feed = new Zend_Feed_Writer_Feed();
                $feed->setEncoding('ISO-8859-1');
                $feed->setTitle($validatedvalues['title']);
                $feed->setDescription($validatedvalues['title']);
                $feed->setLink($validatedvalues['link']);
                if (isset($validatedvalues['language'])) {
                    $feed->setLanguage($validatedvalues['language']);
                }
                $feed->addAuthor(array(
                                'name'  => $validatedvalues['name'],
                                'email' => $validatedvalues['email'],
                                'uri'   => $validatedvalues['uri'],
                ));
                $feed->setDateModified($validatedvalues['updated']);
                foreach ($validatedvalues['entries'] as $e) {
                    $entry = $feed->createEntry();
                    $entry->setTitle($e['title']);
                    $entry->setLink($e['link']);
                    $summary = (empty($e['summary']) ? $e['title'] : $e['summary']);
                    $entry->setDescription($summary);
                    $feed->addEntry($entry);
                }
                $response = $feed->export('rss91');
            }
            else if ($this->rssformat == 'rss') {
                error_log('generating RSS 2.0');
                $feed = new Zend_Feed_Writer_Feed();
                $feed->setTitle($validatedvalues['title']);
                $feed->setDescription($validatedvalues['title']);
                $feed->setLink($validatedvalues['link']);
                $feed->setFeedLink($validatedvalues['link'], 'atom');
                $feed->addAuthor(array(
                                'name'  => $validatedvalues['name'],
                                'email' => $validatedvalues['email'],
                                'uri'   => $validatedvalues['uri'],
                ));
                $feed->setDateModified($validatedvalues['updated']);
                foreach ($validatedvalues['entries'] as $e) {
                    $entry = $feed->createEntry();
                    $entry->setTitle($e['title']);
                    $entry->setLink($e['link']);
                    $entry->addAuthor(array(
                                    'name'  => $e['name'],
                                    'email' => $e['email'],
                                    'uri'   => $validatedvalues['uri'],
                    ));
                    $entry->setDateModified($e['updated']);
                    $entry->setDateCreated($e['published']);
                    $summary = (empty($e['summary']) ? $e['title'] : $e['summary']);
                    $entry->setDescription($summary);
                    $content = (empty($e['content']) ? $summary : $e['content']);
                    $entry->setContent($content);
                    $feed->addEntry($entry);
                }
                $response = $feed->export('rss');
            }
            else {
                $response = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
                $response .= '<RESPONSE>'."\n";
                $response .= self::xmlize_result($this->returns, $this->function->returns_desc);
                $response .= '</RESPONSE>'."\n";
            }
        }
        $this->send_headers();
        echo $response;
    }

    /**
     * Send the error information to the WS client
     * formatted as XML document.
     * Note: the exception is never passed as null,
     *       it only matches the abstract function declaration.
     * @param exception $ex
     * @return void
     */
    protected function send_error($ex=null) {
        $this->send_headers();
        echo $this->generate_error($ex);
    }

    /**
     * Build the error information matching the RSS returned value format (JSON or XML)
     * @param exception $ex
     * @return string the error in the requested RSS format
     */
    protected function generate_error($ex) {
        if ($this->rssformat == 'json') {
            $errorobject = new stdClass;
            $errorobject->exception = get_class($ex);
            $errorobject->message = $ex->getMessage();
            if (debugging() and isset($ex->debuginfo)) {
                $errorobject->debuginfo = $ex->debuginfo;
            }
            $error = json_encode($errorobject);
        } else {
            $error = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
            $error .= '<EXCEPTION class="'.get_class($ex).'">'."\n";
            $error .= '<MESSAGE>'.htmlspecialchars($ex->getMessage(), ENT_COMPAT, 'UTF-8').'</MESSAGE>'."\n";
            if (debugging() and isset($ex->debuginfo)) {
                $error .= '<DEBUGINFO>'.htmlspecialchars($ex->debuginfo, ENT_COMPAT, 'UTF-8').'</DEBUGINFO>'."\n";
            }
            $error .= '</EXCEPTION>'."\n";
        }
        return $error;
    }

    /**
     * Internal implementation - sending of page headers.
     * @return void
     */
    protected function send_headers() {
        if ($this->rssformat == 'json') {
            header('Content-type: application/json');
        } else {
            header('Content-Type: application/xml; charset=utf-8');
            header('Content-Disposition: inline; filename="response.xml"');
        }
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Expires: '. gmdate('D, d M Y H:i:s', 0) .' GMT');
        header('Pragma: no-cache');
        header('Accept-Ranges: none');
    }

    /**
     * Internal implementation - recursive function producing XML markup.
     * @param mixed $returns
     * @param $desc
     * @return unknown_type
     */
    protected static function xmlize_result($returns, $desc) {
        if ($desc === null) {
            return '';

        } else if ($desc instanceof external_value) {
            if (is_bool($returns)) {
                // we want 1/0 instead of true/false here
                $returns = (int)$returns;
            }
            if (is_null($returns)) {
                return '<VALUE null="null"/>'."\n";
            } else {
                return '<VALUE>'.htmlspecialchars($returns, ENT_COMPAT, 'UTF-8').'</VALUE>'."\n";
            }

        } else if ($desc instanceof external_multiple_structure) {
            $mult = '<MULTIPLE>'."\n";
            if (!empty($returns)) {
                foreach ($returns as $val) {
                    $mult .= self::xmlize_result($val, $desc->content);
                }
            }
            $mult .= '</MULTIPLE>'."\n";
            return $mult;

        } else if ($desc instanceof external_single_structure) {
            $single = '<SINGLE>'."\n";
            foreach ($desc->keys as $key=>$subdesc) {
                $single .= '<KEY name="'.$key.'">'.self::xmlize_result($returns[$key], $subdesc).'</KEY>'."\n";
            }
            $single .= '</SINGLE>'."\n";
            return $single;
        }
    }
}


/**
 * RSS test client class
 */
class webservice_rss_test_client implements webservice_test_client_interface {
    /**
     * Execute test client WS request
     * @param string $serverurl
     * @param string $function
     * @param array $params
     * @return mixed
     */
    public function simpletest($serverurl, $function, $params) {
        return download_file_content($serverurl.'&wsfunction='.$function, null, $params);
    }
}