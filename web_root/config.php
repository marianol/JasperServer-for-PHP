<?php
/**
 * Jaspersoft Integration Sample configuration file.
 *
 * @copyright Copyright (c) 2011
 * @author Mariano Luna
 * 
LICENSE AND COPYRIGHT NOTIFICATION
==================================

The Proof of Concept deliverable(s) are (c) 2011 Jaspersoft Corporation - All rights reserved. 
Jaspersoft grants to you a non-exclusive, non-transferable, royalty-free license to use the deliverable(s) pursuant to 
the applicable evaluation license agreement (or, if you later purchase a subscription, the applicable subscription 
agreement) relating to the Jaspersoft software product at issue. 

The Jaspersoft Sales department provides the Proof of Concept deliverable(s) "AS IS" and WITHOUT A WARRANTY OF ANY KIND. 
It is not covered by any Jaspersoft Support agreement or included in any Professional Services offering. 
At the discretion of the head of the Jaspersoft Professional Services team, support, maintenance and enhancements may be 
available for such deliverable(s) as "Time for Hire": http://www.jaspersoft.com/time-for-hire.

 */

session_start();

/* Get Required Libraries */
require_once 'RESTclient.php';
require_once 'functions.php';
require_once 'PEST/PestXML.php'; // This is a rest wrapper library to make the REST calls to JasperRepors Web services

/**
 * Site Constants
 *
 */


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
 * - If Jasperserver and this app have to be in the same domain for
 * the iframe integration cookie to work
 * * see iframe.php
 */

define('JS_WS_URL', 'http://localhost:8080/jasperserver-pro/rest/'); // Pro
	
/**
 * Main Site Configuration Array
 */
$_PageTitle = ''; // Default Page title
$_PageTabs = ''; // Default Tabs

$_SiteConfig = array();

$_SiteConfig['site'] = array(
	'name'  		=> 'My Report App',
	'title' 		=> 'My App',
	'url'   		=> 'http://demo.com/',
	'sslurl'   		=> 'https://demo.com/',
	'keywords'		=> '',
	'keywords-extra'	=> '',
	'description'	=> '',
	'email'			=> 'do-not-reply@nomail.com',
	'company' 		=> 'Jaspersoft',
);

$_SiteConfig['user_menu']  = '
	<li ><a href="iframe.php">iFrame Integration</a></li> 
	<li ><a href="about.php">About</a></li> 
	<li ><a href="logout.php">Log out</a></li> 
';
$_SiteConfig['guest_menu']  = '
	<li ><a href="login.php">Login</a></li>
	<li ><a href="about.php">About</a></li> 
';

$_SiteConfig['error_messages'] = array(
	0	=> 'General Error',
	1  	=> 'Login error, please check your username or password.',
	2 	=> 'Login error, too many login attempts in a short period of time.',
	3 	=> 'Invalid or non existent ID.',
	4 	=> 'Database connection error.',
	5 	=> 'No Data To Display.',
);



/*
	Error reporting.
*/
ini_set("error_reporting", "true");
error_reporting(E_ALL|E_STRCT);

// Decorators

function decoratePageTabs($tabArray, $selectedTab = -1) {
	$output = '';
	foreach ($tabArray as $item => $legend) {
		$selected = ($selectedTab == $item) ? 'selected' : '';
	    $output .= '<li class="' . $selected . '">' . $legend . '</li>' . "\n";

	}
	return $output;
}
?>