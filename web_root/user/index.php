<?php
/**
 * user/index.php General Home Page for authenticated users
 *
 * redirects to guest/login.php on not authenticated
 *
 * @author Mariano Lun
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 */

/* Make sure prepend.php was called */
assert(defined('EWL_PREPENDED'));

switch ($_SESSION['userlevel']) {
	case ADMIN:
		$hometemplate = 'ADMIN';
		header('Location: /admin/index.php');
		exit();
		break;
	case USER:
		$hometemplate = 'USER';
		break;
	default:
		// This is now good...
		trigger_error('Undefined Home Page UL:'. $_SESSION['userlevel'] . ' on ' . $_SERVER['PHP_SELF'] , E_USER_ERROR);
		break;
}
$template = array('content' => 'general.tpl#EMPTY');


$TPL = masterTemplate($template);

$TPL->assign('PAGE_CONTENT', '<h4>Welcome Mister User</h4>');
$TPL->assign("PAGE_TITLE", 'Welcome back ' . ECM_GetUserName($DBcon, $_SESSION['login']['id'], false) ) ;
template_output($TPL);


?>