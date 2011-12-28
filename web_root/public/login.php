<?php
/**
 * login.php Main Site Page.
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011, Essential Technology Solutions, LLC
 */

/* Make sure prepend.php was called */
assert(defined('EWL_PREPENDED'));

// Check if the user is already authenticated
if($_SESSION['userlevel'] >= USER) {
	header('Location: ' . WWW_ROOT . 'user/index.php');
	exit();
}

//echo 'Hello!!';
//die;
$login =& get_login_manager();

$template = array('content' => 'login.tpl#CONTENT');

$TPL = masterTemplate($template, true);

if(isset($_REQUEST['emsg'])) {
	$emsg = intval($_REQUEST['emsg']);
	$emsg = ($emsg > count($_SiteConfig['error_messages'])) ? 0 : $emsg;
	$TPL->assign("ERROR", $_SiteConfig['error_messages'][$emsg]);
} else {
	$TPL->assign("ERROR", '');
	$TPL->assign("ERROR_CLASS", 'hide');
}

// We need to fill the previously entered username on error
$TPL->assign("USERNAME", '');
$TPL->assign("PAGE_TITLE", 'Welcome!');
$TPL->assign('SITE_TITLE', $_SiteConfig['site']['title']); 
template_output($TPL);
?>