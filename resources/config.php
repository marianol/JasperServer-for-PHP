<?php
/**
 * My App configuration file.
 *
 * @copyright Copyright (c) 2011
 * @author Mariano Luna
 * 
 * License: See https://github.com/marianol/JasperServer-for-PHP/blob/master/README.markdown 
 */

session_start();

/**
 * Site Constants
 */
define('SSL_FORCED', false);
 
/**
 * If page requires SSL, and we're not in SSL mode, redirect to the SSL version of the page
 */ 

if(SSL_FORCED && $_SERVER['SERVER_PORT'] != 443) {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
   exit();
}
/**
 * Access level constants not used in this sample
 */
define('GUEST', 0); // for unauthenticated users.
define('USER',  1); // for authenticated users.
define('ADMIN', 5); // for administrators.

/**
 * HTTP path to www/ directory.
 * Change this to fit your instalation
 */
define('WWW_ROOT', '/myphpapp/'); // 
define('SITE_PATH', '/Library/WebServer/Documents/JSDemo/');

/**
 * Jasper Server Web Sevices Constants
 * Change this paths to fit your instalations.
 * Warining: 
 * - If Jasperserver and this app do not share the same domain
 * this sample will not work as intended for the iFrame integration
 * the iframe integration will require a SSO or passing user and password in the GET request see iframe.php
 */

 define('JRS_HOST', 'localhost');
 define('JRS_PORT', '8080');
 define('JRS_BASE', '/jasperserver-pro'); // Pro
 // define('JRS_BASE', '/jasperserver'); // Community
 
 define('JRS_BASE_URL', 'http://' . JRS_HOST . ':' . JRS_PORT . JRS_BASE . '/'); 

 define('JS_WS_URL', JRS_BASE_URL . 'rest/');
 define('JS_REST_URL', JRS_BASE_URL . 'rest_v2/');

/**
 * JRS Base URL For IFRAME for this to work without exposing the authentication 
 * Both JRS and this app should reside in the same TLD
 */ 

define('JS_IFRAME_URL', JRS_BASE_URL );

/* Get Required Libraries */
require_once 'RESTclient.php';
require_once 'functions.php';
require_once 'PEST/PestXML.php';

/**
 * Main Site Configuration Array
 */
$_PageTitle = ''; // Default Page title
$_PageTabs = ''; // Default Tabs

$_SiteConfig = array();

$_SiteConfig['site'] = array(
	'name'  		=> 'My Report App',
	'title' 		=> 'My App',
	'url'   		=> 'http://localhost/' . WWW_ROOT,
	'sslurl'   		=> 'https://localhost.com/' . WWW_ROOT,
	'keywords'		=> '',
	'keywords-extra'	=> '',
	'description'	=> '',
	'email'			=> 'do-not-reply@nomail.com',
	'company' 		=> 'Jaspersoft',
);

$_SiteConfig['user_menu']  = '
	<li ><a href="repository.php">Web Services Integration</a></li>
	<li ><a href="iframe.php">Jasper UI Integration</a></li> 
	<li ><a href="PHP-CLass-Docs.php">JasperReports Wrapper Docs</a></li>
	<li ><a href="about.php">About this Sample</a></li> 
	<li ><a href="logout.php">Log out</a></li> 
';
$_SiteConfig['guest_menu']  = '
	<li ><a href="login.php">Login</a></li>
	<li ><a href="about.php">About this Sample</a></li> 
';
$_SiteConfig['notifyemails'] = array(
	'Administrator'			=> 'admin@nomail.com',
);


$_SiteConfig['error_messages'] = array(
	0	=> 'General Error',
	1  	=> 'Login error, please check your username or password.',
	2 	=> 'Login error, too many login attempts in a short period of time.',
	3 	=> 'Invalid or non existent ID.',
	4 	=> 'Database connection error.',
	5 	=> 'No Data To Display.',
);

$_SiteConfig['usermap'] = array (
	GUEST => 'No Access',
	USER => 'User',
	ADMIN => 'Administrator',
);

/*
	Error reporting.
*/
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);
?>