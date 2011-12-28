<?php

/**
 * Prepended file to initialize application and check access control.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package ewl
 */

/**
 * Pulls the path to includes/ from PHP's include path.
 * @return string path to includes/
 * @access private
 */
function _EWL_inferIncludePath()
{
    /* 
    The following code makes a very bad assumption!!!!! 
    And also does not check that Linux and Win uses diferent path delimiters 
    This funtion will be deleted...
    
	$include_path = get_include_path(); // colon-delimited string
    $include_path = explode(':', $include_path); // array of paths 
    $include_path = $include_path[0]; // just the first path
    */
    $include_path = '';
    return $include_path;
}

/**
 * Makes sure the server's configuration satisfies a minimum version.
 *
 * etszone_cfg_ver is set on ETSZONE's php.ini file This function makes sure
 * that this variable is set (you're not using a stock config) and that it's
 * sufficiently recent.
 *
 * @param string $cfg_ver_min A version_compare()-formatted minimum version
 * string.
 */
function EWL_checkServerConfig($cfg_ver_min = '1.0')
{
    $etszone_cfg_ver = get_cfg_var('etszone_cfg_ver');
    assert($etszone_cfg_ver);
    assert(version_compare($etszone_cfg_ver, $cfg_ver_min, '>='));
}

/**
 * Asserts the project's configuration file includes some required definitions.
 * @access private
 */
function _EWL_checkProjectConfig()
{
    /* make sure WWW_ROOT was defined with a trailing slash */
    assert(defined('WWW_ROOT'));
    assert(substr(WWW_ROOT, -1) == '/');

    /* make sure access levels were defined sanely */
    assert(defined('GUEST'));
    assert(defined('USER'));
    assert(defined('ADMIN'));
    assert(GUEST <= USER);
    assert(USER <= ADMIN);

    assert(isset($GLOBALS['ACCESS_DIRS']));

    if (defined('PHP_ETSZONE_CFG_MIN_VER'))
        EWL_checkServerConfig(PHP_ETSZONE_CFG_MIN_VER);

}

/**
 * Starts the session and ensures $_SESSION['userlevel'] is set.
 * Defaults $_SESSION['userlevel'] to GUEST.
 * @access private
 */
function _EWL_initSession()
{
    /* start the session */
    session_start();

    /* default to guest */
    if (!isset($_SESSION['userlevel']))
      $_SESSION['userlevel'] = GUEST;
}


/**
 * Checks if the given userlevel can access the current file.
 * @param int $userlevel the user's userlevel
 * @access private
 */
function _EWL_checkAccessControl($userlevel)
{
    global $ACCESS_DIRS;

    $script = $_SERVER['PHP_SELF'];
	/* strip off WWW_ROOT prefix if exist */
	
	if (strlen(WWW_ROOT) > 1) {
		// We have a Prefix so strip it..
		$script = substr($script, strlen(WWW_ROOT));
	}
   	
    /* get top dir */
    $script = explode('/', $script, 3);
    
    switch (count($script)) {
    	case 1: // no script name???
    		trigger_error('Failed Script Path Parsing :' . $_SERVER['PHP_SELF'] , E_USER_ERROR);  	
   		break;
    	case 2: // Root folder e.g: /index.php
    		$script_dir = 'ROOT' ;	
   		break;
    	case 3: // 2 or more
    		$script_dir = $script[1];
   		break;
    	default: 
    		trigger_error('Failed Script Path Parsing :' . $_SERVER['PHP_SELF'] , E_USER_ERROR);
    }
   	
    /* make sure we're trying to access a valid directory */
    assert(isset($ACCESS_DIRS[$script_dir]));

    /* make sure we're allowed to access this directory */
    if (!($userlevel >= $ACCESS_DIRS[$script_dir])) {
    	// User Not Allowed
    	if (!_EWL_isLoggedIn()) {
    		header('Location: ' . GUEST_REDIRECT);
    	} else {
    		header('Location: ' . USER_REDIRECT);
    	}
    	exit();
    } 
}

/**
 * Cranks up PHP's error reporting/assertion settings.
 * @access private
 */
function _EWL_setErrorReporting()
{
    //error_reporting(E_ALL);
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    assert_options(ASSERT_ACTIVE, true);
    assert_options(ASSERT_BAIL, true);
    assert_options(ASSERT_WARNING, true);
}

/* make sure we catch errors */
_EWL_setErrorReporting();

/* enable logging and stop displaying errors */

/**
 * The filesystem path to the project's includes/ directory.
 */
define('INCLUDE_PATH', '/var/www/callme/resources/');

/**
 * The filesystem path to the project's includes/logs/ directory.
 */
define('LOG_PATH', INCLUDE_PATH. '/logs');

/**
 * The filesystem path to the project's includes/logs/general.log file.
 */
define('LOG_GENERAL', LOG_PATH. '/general.log');
define('LOG_ERROR', LOG_PATH. '/error.log');


require_once 'ewl/logging.php';

// ini_set('display_errors', 0); // Uncomment this line on Production

/* check server config */

//Bypassed for this app
//EWL_checkServerConfig('1.0');

/* load project config */

/**
 * A flag to config.php that prepend.php has included it.
 */
define('EWL_PREPENDED', 1);

require_once 'ewl/general.php';
require_once 'config.php';
_EWL_checkProjectConfig();

/* check that we can access this directory */
_EWL_initSession();
_EWL_checkAccessControl($_SESSION['userlevel']);


?>
