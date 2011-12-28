<?php
/**
 * ETSZONE Call Me (ECM) configuration file.
 *
 * @copyright Copyright (c) 2011, Essential Technology Solutions, LLC
 */

/* Make sure prepend.php called us */
assert(defined('EWL_PREPENDED'));

/* Get Required Libraries */
require_once 'ecm_general.php';
require_once 'template_functions.php';

/**
 * Site Constants
 *
 */
define('TEMPLATE_PATH', INCLUDE_PATH . 'templates/'); // Templates location
define('TABLE_PREFIX', 'ecm_'); 

/**
 *  Relative path to redirect Guest user when 
 *  trying to access something outside its scope
 *
 */
define('GUEST_REDIRECT', '/index.php');
/**
 *  Relative path to redirect logged users when 
 *  trying to access something outside its scope
 *
 */
define('USER_REDIRECT', '/public/no-access.php');

/**
 * Access level constants
 */
define('GUEST', 0); // for unauthenticated users.
define('USER',  1); // for authenticated users.
define('ADMIN', 5); // for administrators.


/**
 * Accessible directories.
 */
$GLOBALS['ACCESS_DIRS'] = array('ROOT'   => GUEST, // Root dir
								'public' => GUEST,
                                'user'    => USER,
                                'admin'   => ADMIN,
								);

/**
 * HTTP path to www/ directory.
 */
define('WWW_ROOT', '/'); // 
define('SITE_PATH', '/var/www/callme');

define('SSL_FORCED', false);

// If page requires SSL, and we're not in SSL mode, 
// redirect to the SSL version of the page
if(SSL_FORCED && $_SERVER['SERVER_PORT'] != 443) {
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
   exit();
}

/**
 * Mail Site Configuration Array
 */

$_SiteConfig = array();

/**
 * Database Connection
 * 
 */
$_SiteConfig['dsn'] = array(
	    'dbtype'  => 'mysqlt',
	    'username' => 'callme',
	    'password' => 'd3VNpZvaJhwvH4XL',
	    'host' => 'localhost',
	    'database' => 'callme',
	    'optionstring' =>  '' //'?persist'
	);

$_SiteConfig['site'] = array(
	'name'  		=> 'ETSZONE Call Me',
	'title' 		=> 'Call Me | ETSZONE',
	'url'   		=> 'http://call.etszone.com/',
	'sslurl'   		=> 'https://call.etszone.com/',
	'keywords'		=> '',
	'keywords-extra'	=> '',
	'description'	=> '',
	'email'			=> 'do-not-reply@etszone.com',
	'company' 		=> 'ETSZONE, LLC.',
);

$_SiteConfig['notifyemails'] = array(
	'Administrator'			=> 'mariano@etszone.com',
	'Mariano Luna'  		=> 'mariano@etszone.com',
	'Kim Stautner'  		=> 'kim@etszone.com',
);

$_SiteConfig['emailsentfrom'] = array(
	'email'  		=> 'sales@etszone.com',
	'name'  		=> 'ETS Payments',
);

$_SiteConfig['error_messages'] = array(
	0	=> 'General Error',
	1  	=> 'Login error, please check your username or password.',
	2 	=> 'Login error, too many login attempts in a short period of time.',
	3 	=> 'Invalid or non existent Project ID',
	4 	=> 'Invalid or non existent User ID',
	5 	=> 'No Data To Display',
);

$_SiteConfig['usermap'] = array (
	GUEST => 'No Access',
	USER => 'Client',
	ADMIN => 'Administrator',
);

// Userlevels that an ADMIN can create
$_SiteConfig['admin_usermap'] = array (
	USER => 'Client',
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