<?php
/**
 * Allow user to view his account information
 *
 * links to user/contact_info_edit.php on update contact info button
 * links to user/password_edit.php on update password button
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 */

/* Make sure prepend.php was called */
assert(defined('EWL_PREPENDED'));

// Define template(s) to be used
$template = array('content' => 'general.tpl#EMPTY');

// Init Template
$TPL = masterTemplate($template);

$userid = (isset($_SESSION['login']['id'])) ? intval($_SESSION['login']['id']) : false;

if ($userid === false) {
	trigger_error('Missing USER ID in Session Vars..', E_USER_ERROR);
}

// Are we being sent back here with an error?
if (isset($_REQUEST['msg'])) {
	$message = strip_tags(trim(base64_decode($_REQUEST['msg'])),'<p><br><b></p></b>') ;
	$TPL->assign('MESSAGE', $message) ;
} else {
	$TPL->assign('MESSAGE', '') ;
	
}

$con =& get_dbcon();

$sql = "SELECT u.id as UserID, firstname as FirstName, lastname as LastName, email, userlevel
FROM  `". TABLE_PREFIX ."users` AS u
WHERE u.id = '" . $userid . "'";

$user_data = $con->GetRow($sql);

/*
array(7) { ["userid"]=> string(1) "2" ["firstname"]=> string(7) "Mariano" ["lastname"]=> string(4) "Luna" ["companyid"]=> string(1) "1" ["email"]=> string(19) "mariano@etszone.com" ["userlevel"]=> string(1) "5" ["phone"]=> string(12) "713-599-1410" }
*/

$html_to_show = array();
foreach ($user_data as $key => $value) {
	$template_var = strtoupper($key);
	$value =  strip_tags($value);
	$html_to_show[] = $key . ': ' . $value;
}


$TPL->assign('PAGE_CONTENT', '<p>' . implode("</p> \n <p>", $html_to_show) . '</p>');
$TPL->assign('PAGE_TITLE', 'My Account');

// Show page template
template_output($TPL);

?>