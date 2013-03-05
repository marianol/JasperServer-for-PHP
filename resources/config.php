<?php
/**
 * My App configuration file.
 *
 * @author Mariano Luna
 * 
 * Please check: https://github.com/marianol/JasperServer-for-PHP/blob/master/README.markdown 
 * 
 * @copyright Copyright (c) 2012 Jaspersoft Corporation - All rights reserved. 
 Unless you have purchased a commercial license agreement from Jaspersoft,
 the following license terms apply:

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero  General Public License for more details.

 You should have received a copy of the GNU Affero General Public  License
 along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
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
 
 // CHANGE THIS TO PATCH YOUT INSTALATION PATHS
// this should match the settings on your httpd.conf file 
// i.e. your application lives in http://localhost/myphpapp
define('WWW_ROOT', '/myphpapp/');
// real OS path where the application is installed
define('SITE_PATH', '/Library/WebServer/Documents/JSDemo/'); 
 // END
 
/**
 * Jasper Server Web Sevices Constants
 * Change this paths to fit your instalations.
 * Warining: 
 * - If Jasperserver and this app do not share the same domain
 * this sample will not work as intended for the iFrame integration
 * the iframe integration will require a SSO or passing user and password in the GET request see iframe.php
 */
 // CHANGE THIS TO POINT TO YOUR JASPER SERVER
 define('JRS_HOST', 'localhost');
 define('JRS_PORT', '8080');
 define('JRS_BASE', '/jasperserver-pro'); // Pro
 // define('JRS_BASE', '/jasperserver'); // Community
 // END
  
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
require_once 'PEST/PestXML.php';

require_once('jasper-rest/client/JasperClient.php');
require_once 'functions.php';

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