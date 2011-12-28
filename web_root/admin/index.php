<?php
/**
 * admin/index.php General Home Page for authenticated users
 *
 * redirects to guest/login.php on not authenticated
 *
 * @author Mariano Lun
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 */

/* Make sure prepend.php was called */
assert(defined('EWL_PREPENDED'));

$template = array('content' => 'general.tpl#EMPTY');

$DBcon =& get_dbcon();

$TPL = masterTemplate($template);

$TPL->assign('PAGE_CONTENT', '<h1>Hey! You are an Administrator!!</h1>');
$TPL->assign("PAGE_TITLE", 'Welcome back ' . ECM_GetUserName($DBcon, $_SESSION['login']['id'], false) ) ;
template_output($TPL);
?>