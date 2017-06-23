<?php
# Copyright (c) 2003-2005, Jannis Hermanns (on behalf the Serendipity Developer Team)
# All rights reserved.  See LICENSE file for licensing details

if (IN_serendipity !== true) {
    die ("Don't hack!");
}

if (defined('S9Y_FRAMEWORK_FUNCTIONS')) {
    return;
}
@define('S9Y_FRAMEWORK_FUNCTIONS', true);

$serendipity['imageList'] = array();

include_once(S9Y_INCLUDE_PATH . 'include/db/db.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/compat.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/functions_config.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/plugin_api.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/functions_images.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/functions_installer.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/functions_entries.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/functions_comments.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/functions_permalinks.inc.php');
include_once(S9Y_INCLUDE_PATH . 'include/functions_smarty.inc.php');

/**
 * Retrieve the raw request entity (body)
 *
 * @since 2.1
 * @return string
 */
function get_raw_data() {
    // $HTTP_RAW_POST_DATA is deprecated on PHP 5.6
    if (version_compare(PHP_VERSION, '5.6.0', '>=' ) ) {
        return file_get_contents( 'php://input' );
    }
    global $HTTP_RAW_POST_DATA;
}

/**
 * Set a new PEAR Request object
 * Includes the required PHP5 PEAR Request2 class and
 * fixes failing CERT validation check for PHP versions below 5.6
 * Make new Request Object
 *
 * @since   2.1
 * @param   $url        string
 * @param   $method     string  Request method for send() (get,head,post,put,delete,trace,conn)
 *                      one of the methods defined in RFC 2616 (https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html)
 * @param   $options    array   Request parameter
 *
 * @return  object
 */
function serendipity_request_object($url = '', $method = 'get', $options = array()) {
    require_once S9Y_PEAR_PATH . 'HTTP/Request2.php';
    // The OpenSSL extension of PHP below version 5.6 does not try to use the distribution-default values for CA file / CA path
    // when explicit ones are not provided. Thus failing, we need to reset using ssl_verify_pear.
    if (version_compare(PHP_VERSION, '5.6.0', '<')) {
        $options['ssl_verify_peer'] = false;
    }
    switch($method) {
        case 'get':
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_GET, $options);
            break;

        case 'head':
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_HEAD, $options);
            break;

        case 'post':
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_POST, $options);
            break;

        case 'put':
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_PUT, $options);
            break;

        case 'delete':
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_DELETE, $options);
            break;

        case 'trace':
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_TRACE, $options);
            break;

        case 'conn':
        case 'connnect':
            $req = new HTTP_Request2($url, HTTP_Request2::METHOD_CONNECT, $options);
            break;

        default:
            return false;
    }

    return $req;
}

/**
 * Request the contents of an URL, API wrapper [A Serendipity successor of the Styx serendipity_request_object() wrapper]
 *
 * @param $uri string               The URL to fetch
 * @param $method string            HTTP method (GET/POST/PUT/OPTIONS...)
 * @param $contenttype string       optional HTTP content type
 * @param $contenttype mixed        optional extra data (i.e. POST body), can be an array
 * @param $extra_options array      Extra options for HTTP_Request $options array (can override defaults)
 * @param $addData string           possible extra event addData declaration for 'backend_http_request' hook
 * @param $auth array               Array with 'user' and 'pass' for HTTP Auth
 *
 * @return $content string          The URL contents
 */
function serendipity_request_url($uri, $method = 'GET', $contenttype = null, $data = null, $extra_options = null, $addData = null, $auth = null) {
    global $serendipity;
    require_once S9Y_PEAR_PATH . 'HTTP/Request2.php';
    $options = array('follow_redirects' => true, 'max_redirects' => 5);

    if (is_array($extra_options)) {
        foreach($extra_options AS $okey => $oval) {
            $options[$okey] = $oval;
        }
    }
    serendipity_plugin_api::hook_event('backend_http_request', $options, $addData);
    serendipity_request_start();
    if (version_compare(PHP_VERSION, '5.6.0', '<')) {
        // On earlier PHP versions, the certificate validation fails. We deactivate it on them to restore the functionality we had with HTTP/Request1
        $options['ssl_verify_peer'] = false;
    }
    switch(strtoupper($method)) {
        case 'GET':
            $http_method = HTTP_Request2::METHOD_GET;
            break;
        case 'PUT':
            $http_method = HTTP_Request2::METHOD_PUT;
            break;
        case 'OPTIONS':
            $http_method = HTTP_Request2::METHOD_OPTIONS;
            break;
        case 'HEAD':
            $http_method = HTTP_Request2::METHOD_HEAD;
            break;
        case 'DELETE':
            $http_method = HTTP_Request2::METHOD_DELETE;
            break;
        case 'TRACE':
            $http_method = HTTP_Request2::METHOD_TRACE;
            break;
        case 'CONNECT':
            $http_method = HTTP_Request2::METHOD_CONNECT;
            break;
        default:
        case 'POST':
            $http_method = HTTP_Request2::METHOD_POST;
            break;
    }
    $req = new HTTP_Request2($uri, $http_method, $options);
    if (isset($contenttype) && $contenttype !== null) {
       $req->setHeader('Content-Type', $contenttype);
    }

    if (is_array($auth)) {
        $req->setAuth($auth['user'], $auth['pass']);
    }
    if ($data != null) {
        if (is_array($data)) {
            $req->addPostParameter($data);
        } else {
            $req->setBody($data);
        }
    }
    try {
        $res = $req->send();
    } catch (HTTP_Request2_Exception $e) {
        serendipity_request_end();
        return false;
    }
    $fContent = $res->getBody();
    $serendipity['last_http_request'] = array(
        'responseCode' => $res->getStatus(),
        'effectiveUrl' => $res->getEffectiveUrl(),
        'reasonPhrase' => $res->getReasonPhrase(),
        'isRedirect'   => $res->isRedirect(),
        'cookies'      => $res->getCookies(),
        'version'      => $res->getVersion(),
        'header'       => $res->getHeader(),
        'object'       => $res // forward compatibility for possible other checks
    );

    serendipity_request_end();
    return $fContent;
}

/**
 * Serendipity strpos iteration mapper to also check needled arrays
 *
 * @access public
 * @param   string          The haystack
 * @param   string/array    The needle
 * @return
 */
function serendipity_strpos($haystack, $needles) {
    if (is_array($needles)) {
        foreach ($needles AS $str) {
            // keep in mind if needle is not a string, it is converted to an integer and applied as the ordinal value of a character
            if (is_string($str)) {
                return strpos($haystack, $str);
            } else {
                serendipity_strpos($haystack, $str);
            }
        }
    } else {
        return strpos($haystack, $needles);
    }
}

/**
 * Get the Referer calling function name for the current HTTP Request
 *
 * @access public
 * @return string parent level function name
 */
function serendipity_debugCallerId(){
    $trace = debug_backtrace();
    $level = count($trace)-1;
    return $trace[$level]['function'];
}

/**
 * Truncate a string to a specific length, multibyte aware. Appends '...' if successfully truncated
 *
 * @access public
 * @param   string  Input string
 * @param   int     Length the final string should have
 * @return  string  Truncated string
 */
function serendipity_truncateString($s, $len) {
    if ( strlen($s) > ($len+3) ) {
        $s = serendipity_mb('substr', $s, 0, $len) . '...';
    }
    return $s;
}

/**
 * Optionally turn on GZip Compression, if configured
 *
 * @access public
 */
function serendipity_gzCompression() {
    global $serendipity;
    if (isset($serendipity['useGzip']) && serendipity_db_bool($serendipity['useGzip'])
        && function_exists('ob_gzhandler') && extension_loaded('zlib')
        && serendipity_ini_bool(ini_get('zlib.output_compression')) == false
        && serendipity_ini_bool(ini_get('session.use_trans_sid')) == false) {
        ob_start('ob_gzhandler');
    }
}

/**
 * Returns a timestamp formatted according to the current Server timezone offset
 *
 * @access public
 * @param  int      The timestamp you want to convert into the current server timezone. Defaults to "now".
 * @param  boolean  A toggle to indicate, if the timezone offset should be ADDED or SUBSTRACTED from the timezone. Substracting is required to restore original time when posting an entry.
 * @return int      The final timestamp
 */
function serendipity_serverOffsetHour($timestamp = null, $negative = false) {
    global $serendipity;

    if ($timestamp === null) {
        $timestamp = time();
    }

    if (empty($serendipity['serverOffsetHours']) || !is_numeric($serendipity['serverOffsetHours']) || $serendipity['serverOffsetHours'] == 0) {
        return $timestamp;
    } else {
        return $timestamp + (($negative ? -$serendipity['serverOffsetHours'] : $serendipity['serverOffsetHours']) * 60 * 60);
    }
}

/* Converts a date string (DD.MM.YYYY, YYYY-MM-DD, MM/DD/YYYY) into a unix timestamp
 *
 * @access public
 * @param  string  The input date
 * @return int     The output unix timestamp
 */
function &serendipity_convertToTimestamp($in) {
    if (preg_match('@([0-9]+)([/\.-])([0-9]+)([/\.-])([0-9]+)@', $in, $m)) {
        if ($m[2] != $m[4]) {
            return $in;
        }

        switch($m[2]) {
            case '.':
                return mktime(0, 0, 0, /* month */ $m[3], /* day */ $m[1], /* year */ $m[5]);
                break;

            case '/':
                return mktime(0, 0, 0, /* month */ $m[1], /* day */ $m[3], /* year */ $m[5]);
                break;

            case '-':
                return mktime(0, 0, 0, /* month */ $m[3], /* day */ $m[5], /* year */ $m[1]);
                break;
        }

        return $in;
    }

    return $in;
}

/**
 * Format a timestamp
 *
 * This function can convert an input timestamp into specific PHP strftime() outputs, including applying necessary timezone calculations.
 *
 * @access public
 * @param   string      Output format for the timestamp
 * @param   int         Timestamp to use for displaying
 * @param   boolean     Indicates, if timezone calculations shall be used.
 * @param   boolean     Whether to use strftime or simply date
 * @return  string      The formatted timestamp
 */
function serendipity_strftime($format, $timestamp = null, $useOffset = true, $useDate = false) {
    global $serendipity;
    static $is_win_utf = null;

    if ($is_win_utf === null) {
        // Windows does not have UTF-8 locales.
        $is_win_utf = (LANG_CHARSET == 'UTF-8' && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? true : false);
    }

    if ($useDate) {
        $out = date($format, $timestamp);
    } else {
        switch($serendipity['calendar']) {
            default:
            case 'gregorian':
                if ($timestamp == null) {
                    $timestamp = serendipity_serverOffsetHour();
                } elseif ($useOffset) {
                    $timestamp = serendipity_serverOffsetHour($timestamp);
                }
                $out = strftime($format, $timestamp);
                break;

            case 'persian-utf8':
                if ($timestamp == null) {
                    $timestamp = serendipity_serverOffsetHour();
                } elseif ($useOffset) {
                    $timestamp = serendipity_serverOffsetHour($timestamp);
                }

                require_once S9Y_INCLUDE_PATH . 'include/functions_calendars.inc.php';
                $out = persian_strftime_utf($format, $timestamp);
                break;
        }
    }

    if ($is_win_utf && (empty($serendipity['calendar']) || $serendipity['calendar'] == 'gregorian')) {
        $out = utf8_encode($out);
    }

    return $out;
}

/**
 * A wrapper function call for formatting Timestamps.
 *
 * Utilizes serendipity_strftime() and prepares the output timestamp with a few tweaks, and applies automatic uppercasing of the return.
 *
 * @see serendipity_strftime()
 * @param   string      Output format for the timestamp
 * @param   int         Timestamp to use for displaying
 * @param   boolean     Indicates, if timezone calculations shall be used.
 * @param   boolean     Whether to use strftime or simply date
 * @return  string      The formatted timestamp
 */
function serendipity_formatTime($format, $time, $useOffset = true, $useDate = false) {
    static $cache;
    if (!isset($cache)) {
        $cache = array();
    }

    if (!isset($cache[$format])) {
        $cache[$format] = $format;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $cache[$format] = str_replace('%e', '%d', $cache[$format]);
        }
    }

    return serendipity_mb('ucfirst', serendipity_strftime($cache[$format], (int)$time, $useOffset, $useDate));
}

/**
 * Fetches the list of available templates/themes/styles.
 *
 * @access public
 * @param   string  Directory to search for a template [recursive use]
 * @return  array   Sorted array of available template names
 */
function serendipity_fetchTemplates($dir = '') {
    global $serendipity;

    $cdir = @opendir($serendipity['serendipityPath'] . $serendipity['templatePath'] . $dir);
    $rv   = array();
    if (!$cdir) {
        return $rv;
    }
    while (($file = readdir($cdir)) !== false) {
        if (is_dir($serendipity['serendipityPath'] . $serendipity['templatePath'] . $dir . $file) && !preg_match('@^(\.|CVS)@i', $file) && !file_exists($serendipity['serendipityPath'] . $serendipity['templatePath'] . $dir . $file . '/inactive.txt')) {
            if (file_exists($serendipity['serendipityPath'] . $serendipity['templatePath'] . $dir . $file . '/info.txt')) {
                $key = strtolower($file);
                if (isset($rv[$key])) {
                    $key = $dir . $key;
                }
                $rv[$key] = $dir . $file;
            } else {
                $temp = serendipity_fetchTemplates($dir . $file . '/');
                if (count($temp) > 0) {
                    $rv = array_merge($rv, $temp);
                }
            }
        }
    }
    closedir($cdir);
    ksort($rv);
    return $rv;
}

/**
 * Get information about a specific theme/template/style
 *
 * @access public
 * @param   string  Directory name of a theme
 * @param   string  Absolute path to the templates [for use on CVS mounted directories]
 * @return  array   Associative array if template information
 */
function serendipity_fetchTemplateInfo($theme, $abspath = null) {
    global $serendipity;

    if ($abspath === null) {
        $abspath = $serendipity['serendipityPath'] . $serendipity['templatePath'];
    }

    $lines = @file($abspath . $theme . '/info.txt');
    if ( !$lines ) {
        return array();
    }

    for($x=0; $x<count($lines); $x++) {
        $j = preg_split('/([^\:]+)\:/', $lines[$x], -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($j[2]) {
            $currSec = $j[1];
            $data[strtolower($currSec)][] = trim($j[2]);
        } else {
            $data[strtolower($currSec)][] = trim($j[0]);
        }
    }

    foreach ($data AS $k => $v) {
        $data[$k] = @trim(implode("\n", $v));
    }

    if (@is_file($serendipity['templatePath'] . $theme . '/config.inc.php')) {
        $data['custom_config'] = YES;
        $data['custom_config_engine'] = $theme;
    }

    // Templates can depend on a possible "Engine" (i.e. "Engine: 2k11").
    // We support the fallback chain also of a template's configuration, so let's check each engine for a config file.
    if (!empty($data['engine'])) {
        $engines = explode(',', $data['engine']);
        foreach($engines AS $engine) {
            $engine = trim($engine);
            if (empty($engine)) continue;

            if (@is_file($serendipity['templatePath'] . $engine . '/config.inc.php')) {
                $data['custom_config'] = YES;
                $data['custom_config_engine'] = $engine;
            }
        }
    }

    if ( $theme != 'default' && $theme != 'default-rtl'
                && @is_dir($serendipity['templatePath'] . $theme . '/admin')
                && strtolower($data['backend']) == 'yes' ) {
        $data['custom_admin_interface'] = YES;
    } else {
        $data['custom_admin_interface'] = NO;
    }

    // Templates can depend on a modul only setting (i.e. "Modul: backend"). This might get extended in future...
    if (!empty($data['modul'])) {
        $modul = explode(',', $data['modul']);
        if (strtolower($modul[0]) == 'backend') {
            $data['custom_admin_only_interface'] = true;
            $data['custom_config'] = NO;
        }
    }

    return $data;
}

/**
 * Recursively walks an 1-dimensional array to map parent IDs and depths, depending on the nested array set.
 *
 * Used for sorting a list of comments, for example. The list of comment is iterated, and the nesting level is calculated, and the array will be sorted to represent the amount of nesting.
 *
 * @access public
 * @param   array   Input array to investigate [consecutively sliced for recursive calls]
 * @param   string  Array index name to indicate the ID value of an array index
 * @param   string  Array index name to indicate the PARENT ID value of an array index, matched against the $child_name value
 * @param   int     The parent id to check an element against for recursive nesting
 * @param   int     The current depth of the cycled array
 * @return  array   The sorted and shiny polished result array
 */
function serendipity_walkRecursive($ary, $child_name = 'id', $parent_name = 'parent_id', $parentid = 0, $depth = 0) {
    global $serendipity;
    static $_resArray;
    static $_remain;

    if (!is_array($ary) || sizeof($ary) == 0) {
        return array();
    }

    if ($parentid === VIEWMODE_THREADED) {
        $parentid = 0;
    }

    if ($depth == 0) {
        $_resArray = array();
        $_remain   = $ary;
    }

    foreach($ary AS $key => $data) {
        if ($parentid === VIEWMODE_LINEAR || !isset($data[$parent_name]) || $data[$parent_name] == $parentid) {
            $data['depth'] = $depth;
            $_resArray[]   = $data;
            unset($_remain[$key]);
            if ($data[$child_name] && $parentid !== VIEWMODE_LINEAR ) {
                serendipity_walkRecursive($ary, $child_name, $parent_name, $data[$child_name], ($depth+1));
            }
        }
    }

    /* We are inside a recusive child, and we need to break out */
    if ($depth !== 0) {
        return true;
    }

    if (count($_remain) > 0) {
        // Remaining items need to be appended
        foreach($_remain AS $key => $data) {
            $data['depth'] = 0;
            $_resArray[]   = $data;
        }
    }

    return $_resArray;
}

/**
 * Fetch the list of Serendipity Authors
 *
 * @access public
 * @param   int     Fetch only a specific User
 * @param   array   Can contain an array of group IDs you only want to fetch authors of.
 * @param   boolean If set to TRUE, the amount of entries per author will also be returned
 * @return  array   Result of the SQL query
 */
function serendipity_fetchUsers($user = '', $group = null, $is_count = false) {
    global $serendipity;

    $where = '';
    if (!empty($user)) {
        $where = "WHERE a.authorid = '" . (int)$user ."'";
    }

    $query_select   = '';
    $query_join     = '';
    $query_group    = '';
    $query_distinct = '';
    if ($is_count) {
        $query_select = ', count(e.authorid) AS artcount';
        $query_join   = "LEFT OUTER JOIN {$serendipity['dbPrefix']}entries AS e
                                      ON (a.authorid = e.authorid AND e.isdraft = 'false')";
    }

    if ($is_count || $group != null) {
        if ($serendipity['dbType'] == 'postgres' ||
            $serendipity['dbType'] == 'pdo-postgres') {
            // Why does PostgreSQL keep doing this to us? :-)
            $query_group    = 'GROUP BY a.authorid, a.realname, a.username, a.password, a.hashtype, a.mail_comments, a.mail_trackbacks, a.email, a.userlevel, a.right_publish';
            $query_distinct = 'DISTINCT';
        } else {
            $query_group    = 'GROUP BY a.authorid';
            $query_distinct = '';
        }
    }


    if ($group === null) {
        $querystring = "SELECT $query_distinct
                               a.authorid,
                               a.realname,
                               a.username,
                               a.password,
                               a.hashtype,
                               a.mail_comments,
                               a.mail_trackbacks,
                               a.email,
                               a.userlevel,
                               a.right_publish
                               $query_select
                          FROM {$serendipity['dbPrefix']}authors AS a
                               $query_join
                               $where
                               $query_group
                      ORDER BY a.realname ASC";
    } else {

        if ($group === 'hidden') {
            $query_join .= "LEFT OUTER JOIN {$serendipity['dbPrefix']}groupconfig AS gc
                                         ON (gc.property = 'hiddenGroup' AND gc.id = ag.groupid AND gc.value = 'true')";
            $where .= ' AND gc.id IS NULL ';
        } elseif (is_array($group)) {
            foreach($group AS $idx => $groupid) {
                $group[$idx] = (int)$groupid;
            }
            $group_sql = implode(', ', $group);
        } else {
            $group_sql = (int)$group;
        }

        $querystring = "SELECT $query_distinct
                               a.authorid,
                               a.realname,
                               a.username,
                               a.password,
                               a.hashtype,
                               a.mail_comments,
                               a.mail_trackbacks,
                               a.email,
                               a.userlevel,
                               a.right_publish
                               $query_select
                          FROM {$serendipity['dbPrefix']}authors AS a
               LEFT OUTER JOIN {$serendipity['dbPrefix']}authorgroups AS ag
                            ON a.authorid = ag.authorid
               LEFT OUTER JOIN {$serendipity['dbPrefix']}groups AS g
                            ON ag.groupid  = g.id
                               $query_join
                         WHERE " . ($group_sql ? "g.id IN ($group_sql)" : '1=1') . "
                               $where
                               $query_group
                      ORDER BY a.realname ASC";
    }

    return serendipity_db_query($querystring);
}


/**
 * Sends a Mail with Serendipity formatting
 *
 * @access public
 * @param   string  The recipient address of the mail
 * @param   string  The subject of the mail
 * @param   string  The body of the mail
 * @param   string  The sender mail address of the mail
 * @param   array   additional headers to pass to the E-Mail
 * @param   string  The name of the sender
 * @return  int     Return code of the PHP mail() function
 */
function serendipity_sendMail($to, $subject, $message, $fromMail, $headers = NULL, $fromName = NULL) {
    global $serendipity;

    if (!is_null($headers) && !is_array($headers)) {
        trigger_error(__FUNCTION__ . ': $headers must be either an array or null', E_USER_ERROR);
    }

    if (is_null($fromName) || empty($fromName)) {
        $fromName = $serendipity['blogTitle'];
    }

    if (is_null($fromMail) || empty($fromMail)) {
        $fromMail = $to;
    }

    if (is_null($headers)) {
        $headers = array();
    }

    // Fix special characters
    $fromName = str_replace(array('"', "\r", "\n"), array("'", '', ''), $fromName);
    $fromMail = str_replace(array("\r","\n"), array('', ''), $fromMail);

    // Prefix all mail with weblog title
    $subject = '['. $serendipity['blogTitle'] . '] '.  $subject;

    // Append signature to every mail
    $message .= "\n" . sprintf(SIGNATURE, $serendipity['blogTitle']);

    $maildata = array(
        'to'       => &$to,
        'subject'  => &$subject,
        'fromName' => &$fromName,
        'fromMail' => &$fromMail,
        'blogMail' => $serendipity['blogMail'],
        'version'  => 'Serendipity' . ($serendipity['expose_s9y'] ? '/' . $serendipity['version'] : ''),
        'legacy'   => true,
        'headers'  => &$headers,
        'message'  => &$message
    );

    serendipity_plugin_api::hook_event('backend_sendmail', $maildata, LANG_CHARSET);

    // This routine can be overridden by a plugin.
    if ($maildata['legacy']) {
        // Check for mb_* function, and use it to encode headers etc. */
        if (function_exists('mb_encode_mimeheader')) {
            // mb_encode_mimeheader function insertes linebreaks after 74 chars.
            // Usually this is according to spec, but for us it caused more trouble than
            // it prevented.
            // Regards to Mark Kronsbein for finding this issue!
            $maildata['subject'] = str_replace(array("\n", "\r"), array('', ''), mb_encode_mimeheader($maildata['subject'], LANG_CHARSET));
            $maildata['fromName'] = str_replace(array("\n", "\r"), array('', ''), mb_encode_mimeheader($maildata['fromName'], LANG_CHARSET));
        }


        // Always add these headers
        if (!empty($maildata['blogMail'])) {
            $maildata['headers'][] = 'From: "'. $maildata['fromName'] .'" <'. $maildata['blogMail'] .'>';
        }
        $maildata['headers'][] = 'Reply-To: "'. $maildata['fromName'] .'" <'. $maildata['fromMail'] .'>';
        if ($serendipity['expose_s9y']) {
            $maildata['headers'][] = 'X-Mailer: ' . $maildata['version'];
            $maildata['headers'][] = 'X-Engine: PHP/'. PHP_VERSION;
        }
        $maildata['headers'][] = 'Message-ID: <'. md5(microtime() . uniqid(time())) .'@'. $_SERVER['HTTP_HOST'] .'>';
        $maildata['headers'][] = 'MIME-Version: 1.0';
        $maildata['headers'][] = 'Precedence: bulk';
        $maildata['headers'][] = 'Content-Type: text/plain; charset=' . LANG_CHARSET;
        $maildata['headers'][] = 'Auto-Submitted: auto-generated';

        if (LANG_CHARSET == 'UTF-8') {
            if (function_exists('imap_8bit') && !$serendipity['forceBase64']) {
                $maildata['headers'][] = 'Content-Transfer-Encoding: quoted-printable';
                $maildata['message']   = imap_8bit($maildata['message']);
            } else {
                $maildata['headers'][] = 'Content-Transfer-Encoding: base64';
                $maildata['message']   = chunk_split(base64_encode($maildata['message']));
            }
        }
    }

    if ($serendipity['dumpMail']) {
        $fp = fopen($serendipity['serendipityPath'] . '/templates_c/mail.log', 'a');
        fwrite($fp, date('Y-m-d H:i') . "\n" . print_r($maildata, true));
        fclose($fp);
    }

    if (!isset($maildata['skip_native']) && !empty($maildata['to'])) {
        return mail($maildata['to'], $maildata['subject'], $maildata['message'], implode("\n", $maildata['headers']));
    }
}

/**
 * Fetch all references (links) from a given entry ID
 *
 * @access public
 * @param   int     The entry ID
 * @return  array   The SQL result containing the references/links of an entry
 */
function serendipity_fetchReferences($id) {
    global $serendipity;

    $query = "SELECT name,link FROM {$serendipity['dbPrefix']}references WHERE entry_id = '" . (int)$id . "' AND (type = '' OR type IS NULL)";

    return serendipity_db_query($query);
}


/**
 * Encode a string to UTF-8, if not already in UTF-8 format.
 *
 * @access public
 * @param   string  The input string
 * @return  string  The output string
 */
function serendipity_utf8_encode($string) {
    if (strtolower(LANG_CHARSET) != 'utf-8') {
        if (function_exists('iconv')) {
            $new = iconv(LANG_CHARSET, 'UTF-8', $string);
            if ($new !== false) {
                return $new;
            } else {
                return utf8_encode($string);
            }
        } else if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($string, 'UTF-8', LANG_CHARSET);
        } else {
            return utf8_encode($string);
        }
    } else {
        return $string;
    }
}

/**
 * Create a link that can be used within a RSS feed to indicate a permalink for an entry or comment
 *
 * @access public
 * @param   array       The input entry array
 * @param   boolean     Toggle whether the link will be for a COMMENT [true] or an ENTRY [false]
 * @return  string      A permalink for the given entry
 */
function serendipity_rss_getguid($entry, $comments = false) {
    global $serendipity;

    $id = (isset($entry['entryid']) && $entry['entryid'] != '' ? $entry['entryid'] : $entry['id']);

    // When using %id%, we can make the GUID shorter and independent from the title.
    // If not using %id%, the entryid needs to be used for uniqueness.
    if (stristr($serendipity['permalinkStructure'], '%id%') !== FALSE) {
        $title = 'guid';
    } else {
        $title = $id;
    }

    $guid = serendipity_archiveURL(
        $id,
        $title,
        'baseURL',
        true,
        array('timestamp' => $entry['timestamp'])
    );

    if ($comments == true) {
        $guid .= '#c' . $entry['commentid'];
    }

    return $guid;
}

/**
 * Perform some replacement calls to make valid XHTML content
 *
 * jbalcorn: starter function to clean up xhtml for atom feed.  Add things to this as we find common
 * mistakes, unless someone finds a better way to do this.
 *      DONE:
 *          since someone encoded all the urls, we can now assume any amp followed by
 *              whitespace or a HTML tag (i.e. &<br /> )should be
 *              encoded and most not with a space are intentional
 *
 * @access public
 * @param   string  Input HTML code
 * @return  string  Cleaned HTML code
 */
function xhtml_cleanup($html) {
    static $p = array(
        '/\&([\s\<])/',                 // ampersand followed by whitespace or tag
        '/\&$/',                        // ampersand at end of body
        '/<(br|hr|img)([^\/>]*)>/i',    // unclosed br tag - attributes included
        '/\&nbsp;/'                     // Protect whitespace
    );

    static $r = array(
        '&amp;\1',
        '&amp;',
        '<\1\2 />',
        '&#160;'
    );

    return preg_replace($p, $r, $html);
}

/**
 * Fetch user data for a specific Serendipity author
 *
 * @access public
 * @param   int     The requested author id
 * @return  array   The SQL result array
 */
function serendipity_fetchAuthor($author) {
    global $serendipity;

    return serendipity_db_query("SELECT * FROM {$serendipity['dbPrefix']}authors WHERE " . (is_numeric($author) ? "authorid={$author};" : "username='" . serendipity_db_escape_string($author) . "';"));
}

/**
 * Split a filename into basename and extension parts
 *
 * @access public
 * @param   string  Filename
 * @return  array   Return array containing the basename and file extension
 */
function serendipity_parseFileName($file) {
    $x = explode('.', $file);
    if (count($x)>1){
        $suf = array_pop($x);
        $f   = @implode('.', $x);
        return array($f, $suf);
    }
    else {
        return array($file,'');
    }
}

/**
 * Track the referer to a specific Entry ID
 *
 * @access public
 * @param   int     Entry ID
 * @return  null
 */
function serendipity_track_referrer($entry = 0) {
    global $serendipity;

    // Tracking disabled.
    if ($serendipity['trackReferrer'] === false) {
        return;
    }

    if (isset($_SERVER['HTTP_REFERER'])) {
        if (stristr($_SERVER['HTTP_REFERER'], $serendipity['baseURL']) !== false) {
            return;
        }

        if (!isset($serendipity['_blockReferer']) || !is_array($serendipity['_blockReferer'])) {
            // Only generate an array once per call
            $serendipity['_blockReferer'] = array();
            $serendipity['_blockReferer'] = @explode(';', $serendipity['blockReferer']);
        }

        $url_parts  = parse_url($_SERVER['HTTP_REFERER']);
        $host_parts = explode('.', $url_parts['host']);
        if (!$url_parts['host'] ||
            strstr($url_parts['host'], $_SERVER['SERVER_NAME'])) {
            return;
        }

        foreach($serendipity['_blockReferer'] AS $idx => $hostname) {
            if (@strstr($url_parts['host'], $hostname)) {
                return;
            }
        }

        if (rand(0, 100) < 1) {
            serendipity_track_referrer_gc();
        }

        $ts       = serendipity_db_get_interval('ts');
        $interval = serendipity_db_get_interval('interval', 900);

        $url_parts['query'] = substr($url_parts['query'], 0, 255);

        $suppressq = "SELECT count(1)
                      FROM {$serendipity['dbPrefix']}suppress
                      WHERE ip = '" . serendipity_db_escape_string($_SERVER['REMOTE_ADDR']) . "'
                      AND scheme = '" . serendipity_db_escape_string($url_parts['scheme']) . "'
                      AND port = '" . serendipity_db_escape_string($url_parts['port']) . "'
                      AND host = '" . serendipity_db_escape_string($url_parts['host']) . "'
                      AND path = '" . serendipity_db_escape_string($url_parts['path']) . "'
                      AND query = '" . serendipity_db_escape_string($url_parts['query']) . "'
                      AND last > $ts - $interval";

        $suppressp = "DELETE FROM {$serendipity['dbPrefix']}suppress
                      WHERE ip = '" . serendipity_db_escape_string($_SERVER['REMOTE_ADDR']) . "'
                      AND scheme = '" . serendipity_db_escape_string($url_parts['scheme']) . "'
                      AND host = '" . serendipity_db_escape_string($url_parts['host']) . "'
                      AND port = '" . serendipity_db_escape_string($url_parts['port']) . "'
                      AND query = '" . serendipity_db_escape_string($url_parts['query']) . "'
                      AND path = '" . serendipity_db_escape_string($url_parts['path']) . "'";

        $suppressu = "INSERT INTO {$serendipity['dbPrefix']}suppress
                      (ip, last, scheme, host, port, path, query)
                      VALUES (
                      '" . serendipity_db_escape_string($_SERVER['REMOTE_ADDR']) . "',
                      $ts,
                      '" . serendipity_db_escape_string($url_parts['scheme']) . "',
                      '" . serendipity_db_escape_string($url_parts['host']) . "',
                      '" . serendipity_db_escape_string($url_parts['port']) . "',
                      '" . serendipity_db_escape_string($url_parts['path']) . "',
                      '" . serendipity_db_escape_string($url_parts['query']) . "'
                      )";

        $count = serendipity_db_query($suppressq, true);

        if ($count[0] == 0) {
            serendipity_db_query($suppressu);
            return;
        }

        serendipity_db_query($suppressp);
        serendipity_db_query($suppressu);

        serendipity_track_url('referrers', $_SERVER['HTTP_REFERER'], $entry);
    }
}

/**
 * Garbage Collection for suppressed referrers
 *
 * "Bad" referrers, that only occurred once to your entry are put within a
 * SUPPRESS database table. Entries contained there will be cleaned up eventually.
 *
 * @access public
 * @return null
 */
function serendipity_track_referrer_gc() {
    global $serendipity;

    $ts       = serendipity_db_get_interval('ts');
    $interval = serendipity_db_get_interval('interval', 900);
    $gc = "DELETE FROM {$serendipity['dbPrefix']}suppress WHERE last <= $ts - $interval";
    serendipity_db_query($gc);
}

/**
 * Track a URL used in your Blog (Exit-Tracking)
 *
 * @access public
 * @param  string   Name of the DB table where to store the link (exits|referrers)
 * @param  string   The URL to track
 * @param  int      The Entry ID to relate the track to
 * @return null
 */
function serendipity_track_url($list, $url, $entry_id = 0) {
    global $serendipity;

    $url_parts = parse_url($url);
    $url_parts['query'] = substr($url_parts['query'], 0, 255);

    serendipity_db_query(
      @sprintf(
        "UPDATE %s%s
            SET count = count + 1
          WHERE scheme = '%s'
            AND host   = '%s'
            AND port   = '%s'
            AND path   = '%s'
            AND query  = '%s'
            AND day    = '%s'
            %s",

        $serendipity['dbPrefix'],
        $list,
        serendipity_db_escape_string($url_parts['scheme']),
        serendipity_db_escape_string($url_parts['host']),
        serendipity_db_escape_string($url_parts['port']),
        serendipity_db_escape_string($url_parts['path']),
        serendipity_db_escape_string($url_parts['query']),
        date('Y-m-d'),
        ($entry_id != 0) ? "AND entry_id = '". (int)$entry_id ."'" : ''
      )
    );

    if (serendipity_db_affected_rows() == 0) {
        serendipity_db_query(
          sprintf(
            "INSERT INTO %s%s
                    (entry_id, day, count, scheme, host, port, path, query)
             VALUES (%d, '%s', 1, '%s', '%s', '%s', '%s', '%s')",

            $serendipity['dbPrefix'],
            $list,
            (int)$entry_id,
            date('Y-m-d'),
            serendipity_db_escape_string($url_parts['scheme']),
            serendipity_db_escape_string($url_parts['host']),
            serendipity_db_escape_string($url_parts['port']),
            serendipity_db_escape_string($url_parts['path']),
            serendipity_db_escape_string($url_parts['query'])
          )
        );
    }
}

/**
 * Display the list of top referrers
 *
 * @access public
 * @see serendipity_displayTopUrlList()
 * @param  int      Number of referrers to show
 * @param  boolean  Whether to use HTML links for URLs
 * @param  int      Interval for which the top referrers are aggregated
 * @return string   List of Top referrers
 */
function serendipity_displayTopReferrers($limit = 10, $use_links = true, $interval = 7) {
    return serendipity_displayTopUrlList('referrers', $limit, $use_links, $interval);
}

/**
 * Display the list of top exits
 *
 * @access public
 * @see serendipity_displayTopUrlList()
 * @param  int      Number of exits to show
 * @param  boolean  Whether to use HTML links for URLs
 * @param  int      Interval for which the top exits are aggregated
 * @return string   List of Top exits
 */
function serendipity_displayTopExits($limit = 10, $use_links = true, $interval = 7) {
    return serendipity_displayTopUrlList('exits', $limit, $use_links, $interval);
}

/**
 * Display HTML output data of a Exit/Referrer list
 *
 * @access public
 * @see serendipity_displayTopExits()
 * @see serendipity_displayTopReferrers()
 * @param   string      Name of the DB table to show data from (exits|referrers)
 * @param  boolean  Whether to use HTML links for URLs
 * @param  int      Interval for which the top exits are aggregated
 * @return
 */
function serendipity_displayTopUrlList($list, $limit, $use_links = true, $interval = 7) {
    global $serendipity;

    if ($limit){
        $limit = serendipity_db_limit_sql($limit);
    }

    /* HACK */
    if (preg_match('/^mysqli?/', $serendipity['dbType'])) {
        /* Nonportable SQL due to MySQL date functions,
         * but produces rolling 7 day totals, which is more
         * interesting
         */
        $query = "SELECT scheme, host, SUM(count) AS total
                    FROM {$serendipity['dbPrefix']}$list
                   WHERE day > date_sub(current_date, interval " . (int)$interval . " day)
                GROUP BY host
                ORDER BY total DESC, host
                  $limit";
    } else {
        /* Portable version of the same query */
        $query = "SELECT scheme, host, SUM(count) AS total
                    FROM {$serendipity['dbPrefix']}$list
                GROUP BY scheme, host
                ORDER BY total DESC, host
                  $limit";
    }

    $rows = serendipity_db_query($query);
    $output = '<span class="serendipityReferer">';
    if (is_array($rows)) {
        foreach ($rows AS $row) {
            if ($use_links) {
                $output .= sprintf(
                    '<span class="block_level"><a href="%1$s://%2$s" title="%2$s" >%2$s</a> (%3$s) </span>',
                    serendipity_specialchars($row['scheme']),
                    serendipity_specialchars($row['host']),
                    serendipity_specialchars($row['total'])
                );
            } else {
                $output .= sprintf(
                    '<span class="block_level">%1$s (%2$s) </span>',
                    serendipity_specialchars($row['host']),
                    serendipity_specialchars($row['total'])
                );
            }
        }
    }
    $output .= "</span>\n";
    return $output;
}

/**
 * Return either HTML or XHTML code for an '<a target...> attribute.
 *
 * @access public
 * @param   string  The target to use (_blank, _parent, ...)
 * @return  string  HTML string containig the valid markup for the target attribute.
 */
function serendipity_xhtml_target($target) {
    global $serendipity;

    if ($serendipity['enablePopup'] != true)
        return '';

    return ' onclick="window.open(this.href, \'target' . time() . '\'); return false;" ';
}

/**
 * Parse a URI portion to return which RSS Feed version was requested
 *
 * @access public
 * @param  string  Name of the core URI part
 * @param  string  File extension name of the URI
 * @return string  RSS feed type/version
 */
function serendipity_discover_rss($name, $ext) {
    static $default = '2.0';

    /* Detect type */
    if ($name == 'comments') {
        $type = 'comments';
    } elseif ($name == 'comments_and_trackbacks') {
        $type = 'comments_and_trackbacks';
    } elseif ($name == 'trackbacks') {
        $type = 'trackbacks';
    } else {
        $type = 'content';
    }

    /* Detect version */
    if ($name == 'atom' || $name == 'atom10' || $ext == 'atom') {
        $ver = 'atom1.0';
    } elseif ($name == 'atom03') {
        $ver = 'atom0.3';
    } elseif ($name == 'opml' || $ext == 'opml') {
        $ver = 'opml1.0';
    } elseif ($ext == 'rss') {
        $ver = '0.91';
    } elseif ($ext == 'rss1') {
        $ver = '1.0';
    } else {
        $ver = $default;
    }

    return array($ver, $type);
}

/**
 * Check whether an input string contains "evil" characters used for HTTP Response Splitting
 *
 * @access public
 * @param   string      String to check for evil characters
 * @return  boolean     Return true on success, false on failure
 */
function serendipity_isResponseClean($d) {
    return (strpos($d, "\r") === false && strpos($d, "\n") === false && stripos($d, "%0A") === false && stripos($d, "%0D") === false);
}

/**
 * Create a new Category
 *
 * @access public
 * @param   string  The new category name
 * @param   string  The new category description
 * @param   int     The category owner
 * @param   string  An icon representing the category
 * @param   int     A possible parentid to a category
 * @return  int     The new category's ID
 */
function serendipity_addCategory($name, $desc, $authorid, $icon, $parentid) {
    global $serendipity;
    $query = "INSERT INTO {$serendipity['dbPrefix']}category
                    (category_name, category_description, authorid, category_icon, parentid, category_left, category_right)
                  VALUES
                    ('". serendipity_db_escape_string($name) ."',
                     '". serendipity_db_escape_string($desc) ."',
                      ". (int)$authorid .",
                     '". serendipity_db_escape_string($icon) ."',
                      ". (int)$parentid .",
                       0,
                       0)";

    serendipity_db_query($query);
    $cid = serendipity_db_insert_id('category', 'categoryid');
    serendipity_plugin_api::hook_event('backend_category_addNew', $cid);

    $data = array(
        'categoryid'           => $cid,
        'category_name'        => $name,
        'category_description' => $desc
    );

    serendipity_insertPermalink($data, 'category');
    return $cid;
}

/**
 * Update an existing category
 *
 * @access public
 * @param   int     Category ID to update
 * @param   string  The new category name
 * @param   string  The new category description
 * @param   int     The new category owner
 * @param   string  The new category icon
 * @param   int     The new category parent ID
 * @param   int     The new category sort order
 * @param   int     The new category subcat hiding
 * @return null
 */
function serendipity_updateCategory($cid, $name, $desc, $authorid, $icon, $parentid, $sort_order = 0, $hide_sub = 0, $admin_category = '') {
    global $serendipity;

    $query = "UPDATE {$serendipity['dbPrefix']}category
                    SET category_name = '". serendipity_db_escape_string($name) ."',
                        category_description = '". serendipity_db_escape_string($desc) ."',
                        authorid = ". (int)$authorid .",
                        category_icon = '". serendipity_db_escape_string($icon) ."',
                        parentid = ". (int)$parentid .",
                        sort_order = ". (int)$sort_order . ",
                        hide_sub = ". (int)$hide_sub . "
                    WHERE categoryid = ". (int)$cid ."
                        $admin_category";
    serendipity_db_query($query);
    serendipity_plugin_api::hook_event('backend_category_update', $cid);

    $data = array(
        'id'                   => $cid,
        'categoryid'           => $cid,
        'category_name'        => $name,
        'category_description' => $desc
    );

    serendipity_updatePermalink($data, 'category');
}

/**
 * Ends a session, so that while a file requests happens, Serendipity can work on in that session
 */
function serendipity_request_start() {
    @session_write_close();
    return true;
}

/**
 * Continues a session after a file request
 */
function serendipity_request_end() {
    @session_start();
    return true;
}

if (!function_exists('microtime_float')) {
    /**
     * Get current timestamp as microseconds
     *
     * @access public
     * @return float    the time
     */
    function microtime_float() {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }
}

/**
 * Converts Array data to be used as a GET string
 *
 * @access public
 * @param   array   The input array
 * @param   string  An array prefix
 * @param   string  How to join the array
 * @return  string  The HTTP query string
 */
function serendipity_build_query(&$array, $array_prefix = null, $comb_char = '&amp;') {
    $ret = array();
    if (!is_array($array)) {
        return '';
    }

    foreach ($array AS $k => $v) {
        $newkey = urlencode($k);
        if ($array_prefix) {
            $newkey = $array_prefix . '[' . $newkey . ']';
        }
        if (is_array($v)) {
            $ret[] = serendipity_build_query($v, $newkey, $comb_char);
        } else {
            $ret[] = $newkey . '=' . urlencode($v);
        }
    }

    return implode($comb_char, $ret);
}

/**
 * Picks a specified key from an array and returns it
 *
 * @access public
 * @param   array   The input array
 * @param   string  The key to search for
 * @param   string  The default value to return when not found
 * @return null
 */
function &serendipity_pickKey(&$array, $key, $default) {
    if (!is_array($array)) {
        return $default;
    }

    // array_key_exists() copies array, so is much slower.
    if (in_array($key, array_keys($array))) {
        if (isset($array[$key])) {
            return $array[$key];
        }
    }
    foreach($array AS $child) {
        if (is_array($child) && isset($child[$key]) && !empty($child[$key])) {
            return $child[$key];
        }
    }

    return $default;
}

/**
 * Retrieves the current timestamp but only deals with minutes to optimize Database caching
 * @access public
 * @return timestamp
 * @author Matthew Groeninger
 */
function serendipity_db_time() {
    static $ts    = null;
    static $cache = 300; // Seconds to cache

    if ($ts === null) {
        $now = time();
        $ts = $now - ($now % $cache) + $cache;
    }

    return $ts;
}

/**
 * Inits the logger.
 * @return null
 */
function serendipity_initLog() {
    global $serendipity;

    if (isset($serendipity['logLevel']) && $serendipity['logLevel'] !== 'Off') {
        if ($serendipity['logLevel'] == 'debug') {
            $log_level = Psr\Log\LogLevel::DEBUG;
        } else {
            $log_level = Psr\Log\LogLevel::ERROR;
        }
        $serendipity['logger'] = new Katzgrau\KLogger\Logger($serendipity['serendipityPath'] . 'templates_c/logs', $log_level);
    }
}

/**
 * Sanitize non-unicode characters supported by the Symbol font to unicode / html entities before saving to database
 * Thanks to http://stackoverflow.com/questions/8240030/how-to-convert-symbol-font-to-standard-utf8-html-entity
 * Conversion table used http://www.fileformat.info/info/unicode/font/symbol/nonunicode.htm
 *
 * @see     https://github.com/s9y/Serendipity/issues/394 (unrelated, since this here does not touch 'private use area' symbols, and will possibly be removed when the issue is fixed!)
 * @see     symbol_map_utf8()
 * @see     symbol_utf8()
 * @param   string  $string $entry[body] | [extended] | $commentInfo['comment']
 * @return  string  $string
 */
function symbol_sanitize($string) {
    // replace font symbols
    $string = preg_replace_callback('/&#(61\d+?);/i', 'symbol_map_utf8', $string);
    return $string;
}

function symbol_map_utf8( $match ){
    return symbol_utf8( $match[1] );
}

function symbol_utf8( $decimal ) {
    $_Symbol = array(
        61472 => '020',
        61473 => '021',
        61474 => '022',
        61475 => '023',
        61476 => '024',
        61477 => '025',
        61478 => '026',
        61479 => '027',
        61480 => '028',
        61481 => '029',
        61482 => '02A',
        61483 => '02B',
        61484 => '02C',
        61485 => '02D',
        61486 => '02E',
        61487 => '02F',
        61488 => '030',
        61489 => '031',
        61490 => '032',
        61491 => '033',
        61492 => '034',
        61493 => '035',
        61494 => '036',
        61495 => '037',
        61496 => '038',
        61497 => '039',
        61498 => '03A',
        61499 => '03B',
        61500 => '03C',
        61501 => '03D',
        61502 => '03E',
        61503 => '03F',
        61504 => '040',
        61505 => '041',
        61506 => '042',
        61507 => '043',
        61508 => '044',
        61509 => '045',
        61510 => '046',
        61511 => '047',
        61512 => '048',
        61513 => '049',
        61514 => '04A',
        61515 => '04B',
        61516 => '04C',
        61517 => '04D',
        61518 => '04E',
        61519 => '04F',
        61520 => '050',
        61521 => '051',
        61522 => '052',
        61523 => '053',
        61524 => '054',
        61525 => '055',
        61526 => '056',
        61527 => '057',
        61528 => '058',
        61529 => '059',
        61530 => '05A',
        61531 => '05B',
        61532 => '05C',
        61533 => '05D',
        61534 => '05E',
        61535 => '05F',
        61536 => '060',
        61537 => '061',
        61538 => '062',
        61539 => '063',
        61540 => '064',
        61541 => '065',
        61542 => '066',
        61543 => '067',
        61544 => '068',
        61545 => '069',
        61546 => '06A',
        61547 => '06B',
        61548 => '06C',
        61549 => '06D',
        61550 => '06E',
        61551 => '06F',
        61552 => '070',
        61553 => '071',
        61554 => '072',
        61555 => '073',
        61556 => '074',
        61557 => '075',
        61558 => '076',
        61559 => '077',
        61560 => '078',
        61561 => '079',
        61562 => '07A',
        61563 => '07B',
        61564 => '07C',
        61565 => '07D',
        61566 => '07E',
        61601 => '0A1',
        61602 => '0A2',
        61603 => '0A3',
        61604 => '0A4',
        61605 => '0A5',
        61606 => '0A6',
        61607 => '0A7',
        61608 => '0A8',
        61609 => '0A9',
        61610 => '0AA',
        61611 => '0AB',
        61612 => '0AC',
        61613 => '0AD',
        61614 => '0AE',
        61615 => '0AF',
        61616 => '0B0',
        61617 => '0B1',
        61618 => '0B2',
        61619 => '0B3',
        61620 => '0B4',
        61621 => '0B5',
        61622 => '0B6',
        61623 => '0B7',
        61624 => '0B8',
        61625 => '0B9',
        61626 => '0BA',
        61627 => '0BB',
        61628 => '0BC',
        61629 => '0BD',
        61630 => '0BE',
        61631 => '0BF',
        61632 => '0C0',
        61633 => '0C1',
        61634 => '0C2',
        61635 => '0C3',
        61636 => '0C4',
        61637 => '0C5',
        61638 => '0C6',
        61639 => '0C7',
        61640 => '0C8',
        61641 => '0C9',
        61642 => '0CA',
        61643 => '0CB',
        61644 => '0CC',
        61645 => '0CD',
        61646 => '0CE',
        61647 => '0CF',
        61648 => '0D0',
        61649 => '0D1',
        61650 => '0D2',
        61651 => '0D3',
        61652 => '0D4',
        61653 => '0D5',
        61654 => '0D6',
        61655 => '0D7',
        61656 => '0D8',
        61657 => '0D9',
        61658 => '0DA',
        61659 => '0DB',
        61660 => '0DC',
        61661 => '0DD',
        61662 => '0DE',
        61663 => '0DF',
        61664 => '0E0',
        61665 => '0E1',
        61666 => '0E2',
        61667 => '0E3',
        61668 => '0E4',
        61669 => '0E5',
        61670 => '0E6',
        61671 => '0E7',
        61672 => '0E8',
        61673 => '0E9',
        61674 => '0EA',
        61675 => '0EB',
        61676 => '0EC',
        61677 => '0ED',
        61678 => '0EE',
        61679 => '0EF',
        61681 => '0F1',
        61682 => '0F2',
        61683 => '0F3',
        61684 => '0F4',
        61685 => '0F5',
        61686 => '0F6',
        61687 => '0F7',
        61688 => '0F8',
        61689 => '0F9',
        61690 => '0FA',
        61691 => '0FB',
        61692 => '0FC',
        61693 => '0FD',
        61694 => '0FE'
    );
    $key = $decimal;
    if ( array_key_exists( $key, $_Symbol ) ) {
        if( $key <= 61487 ) {
            $c = '0';
        } else {
            $c = 'f';
        }
        $char = json_decode( '"\u' . $c . $_Symbol[ $key ] . '"');
        return $char;
    } else {
        return "&#$decimal;";
    }
}

/**
 * Check whether a given URL is valid to be locally requested
 * @return boolean
 */
function serendipity_url_allowed($url) {
    global $serendipity;

    if ($serendipity['allowLocalURL']) {
        return true;
    }

    $parts = @parse_url($url);
    if (!is_array($parts) || empty($parts['host'])) {
        return false;
    }

    $host = trim($parts['host'], '.');
    if (preg_match('@^(([1-9]?\d|1\d\d|25[0-5]|2[0-4]\d)\.){3}([1-9]?\d|1\d\d|25[0-5]|2[0-4]\d)$@imsU', $host)) {
        $ip = $host;
    } else {
        $ip = gethostbyname($host);
        if ($ip === $host) {
            $ip = false;
        }
    }

    if ($ip) {
        $ipparts = array_map('intval', explode('.', $ip));
        if ( 127 === $ipparts[0] || 10 === $ipparts[0] || 0 === $ipparts[0]
            || ( 172 === $ipparts[0] && 16 <= $ipparts[1] && 31 >= $ipparts[1] )
            || ( 192 === $ipparts[0] && 168 === $ipparts[1])
        ) {
            return false;
        }
    }

    return true;
}

define('serendipity_FUNCTIONS_LOADED', true);
/* vim: set sts=4 ts=4 expandtab : */
