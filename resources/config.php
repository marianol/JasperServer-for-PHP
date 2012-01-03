<?php
/**
 * My App configuration file.
 *
 * @copyright Copyright (c) 2008
 */

session_start();
/* Get Required Libraries */
require_once 'RESTclient.php';
require_once 'functions.php';
require_once 'PEST/PestXML.php';

/**
 * Site Constants
 *
 */

define('TABLE_PREFIX', 'ecm_'); 

/**
 * Access level constants
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

define('SSL_FORCED', false);

// If page requires SSL, and we're not in SSL mode, 
// redirect to the SSL version of the page
if(SSL_FORCED && $_SERVER['SERVER_PORT'] != 443) {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
   exit();
}

/**
 * Jasper Server Web Sevices Constants
 * Change this paths to fit your instalations.
 * Warining: 
 * - If Jasperserver and this app do not share the same domain
 * the iframe integration will require passing user and password to the iframe
 * * see iframe.php
 */


//define('JS_WS_URL', 'http://localhost:8080/jasperserver/rest/'); // Community
define('JS_WS_URL', 'http://localhost:8080/jasperserver-pro/rest/'); // Pro
	
/**
 * Main Site Configuration Array
 */
$_PageTitle = ''; // Default Page title
$_PageTabs = ''; // Default Tabs

$_SiteConfig = array();

$_SiteConfig['site'] = array(
	'name'  		=> 'My PHP App',
	'title' 		=> 'My App',
	'url'   		=> 'http://demo.com/',
	'sslurl'   		=> 'https://demo.com/',
	'keywords'		=> '',
	'keywords-extra'	=> '',
	'description'	=> '',
	'email'			=> 'do-not-reply@nomail.com',
	'company' 		=> 'App Company',
);

$_SiteConfig['user_menu']  = '
	<li ><a href="home.php">Repository</a></li>
	<li ><a href="iframe.php">iFrame Integration</a></li> 
	<li ><a href="about.php">About</a></li> 
	<li ><a href="logout.php">Log out</a></li> 
';
$_SiteConfig['guest_menu']  = '
	<li ><a href="login.php">Login</a></li>
	<li ><a href="about.php">About</a></li> 
';
$_SiteConfig['notifyemails'] = array(
	'Administrator'			=> 'admin@nomail.com',
);

/**
 * Database Connection
 * - Not used in this sample
 */
$_SiteConfig['dsn'] = array(
	    'dbtype'  => 'mysql',
	    'username' => 'root',
	    'password' => 'password',
	    'host' => 'localhost',
	    'database' => 'databse',
	    'optionstring' =>  '' //'?persist'
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


$_SiteConfig['location_states'] = array(
	"" => "----USA--------",
	"AL" => "Alabama",
	"AK" => "Alaska",
	"AZ" => "Arizona",
	"AR" => "Arkansas",
	"CA" => "California",
	"CO" => "Colorado",
	"CT" => "Connecticut",
	"DC" => "District of Columbia",
	"DE" => "Delaware",
	"FL" => "Florida",
	"GA" => "Georgia",
	"HI" => "Hawaii",
	"ID" => "Idaho",
	"IL" => "Illinois",
	"IN" => "Indiana",
	"IA" => "Iowa",
	"KS" => "Kansas",
	"KY" => "Kentucky",
	"LA" => "Louisiana",
	"ME" => "Maine",
	"MD" => "Maryland",
	"MA" => "Massachusetts",
	"MI" => "Michigan",
	"MN" => "Minnesota",
	"MS" => "Mississippi",
	"MO" => "Missouri",
	"MT" => "Montana",
	"NE" => "Nebraska",
	"NV" => "Nevada",
	"NH" => "New Hampshire",
	"NJ" => "New Jersey",
	"NM" => "New Mexico",
	"NY" => "New York",
	"NC" => "North Carolina",
	"ND" => "North Dakota",
	"OH" => "Ohio",
	"OK" => "Oklahoma",
	"OR" => "Oregon",
	"PA" => "Pennsylvania",
	"RI" => "Rhode Island",
	"SC" => "South Carolina",
	"SD" => "South Dakota",
	"TN" => "Tennessee",
	"TX" => "Texas",
	"UT" => "Utah",
	"VT" => "Vermont",
	"VA" => "Virginia",
	"WA" => "Washington",
	"WV" => "West Virginia",
	"WI" => "Wisconsin",
	"WY" => "Wyoming",
	""   => "----CANADA------",
	"AB" => "Alberta",
	"BC" => "British Columbia",
	"MB" => "Manitoba",
	"NB" => "New Brunswick",
	"NF" => "Newfoundland",
	"NS" => "Nova Scotia",
	"PE" => "Prince Edward Island",
	"ON" => "Ontario",
	"QC" => "Quebec",
	"SK" => "Saskatchewan",
	""   => "----------------",
	"XX" => "Outside the US &amp; Canada"	
);

/*
	Error reporting.
*/
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);
?>