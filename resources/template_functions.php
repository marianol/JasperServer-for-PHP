<?php
/**
 * template_functions.php Functions to help build the standard pages
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 */

/**
 * @return object
 * @param mixed $template
 * @desc Initialize FastTemplate and returns an instance of the Template Object
 * $template can be an arrray in the form:
 *	$template = array(
 *		'NAME' => "TEMPLATE_FILE.tpl#TEMPLATE_NAME",
 *		'NAME' => "TEMPLATE_FILE.tpl#TEMPLATE_NAME" );
 * or a sting indicating the tempate name:
 *  $template = "TEMPLATE_FILE.tpl#TEMPLATE_NAME"
 *  in this case the  specified templete will be loaded to 'content'
*/
function masterTemplate($template = '',$no_menu = false) 
{
	global $_SiteConfig;

	require_once('cls_fasttemplate/cls_fast_template.php');
	
	$used_tpl = array(
			'page' => 'page.tpl#PAGE',
			'header' => 'general.tpl#HEADER',
			'main' => 'general.tpl#MAIN',		
			'footer' => 'general.tpl#FOOTER',
	);
	
	if (is_array($template)) {
		foreach ($template as $key => $value) {
			$used_tpl[$key] = $value;
		}
	} else {
			$used_tpl['content'] = $template;
	}

	if($no_menu) {
		// Do not show the main menu use the NO_MENU template instead (normally error pages)
		$used_tpl['menu'] = "general.tpl#NO_MENU";
	} else {
		// Select wich menu should be used
		if($_SESSION['userlevel'] >= USER) {
			switch ($_SESSION['userlevel']) {
			case ADMIN:
				$used_tpl['menu'] = "general.tpl#ADMINMENU";
				break;
			case USER:
				$used_tpl['menu'] = "general.tpl#USERMENU";
				break;
			default:
				// This is wired... Chech the DB
				$used_tpl['menu'] = "general.tpl#NO_MENU";
				break;
		}
		} else {
			// User not authenticated!! Show NO_MENU
			$used_tpl['menu'] = "general.tpl#PUBLICMENU";
		}
	}
	
	/*
	// Page dependant templates
	$page =  $_SERVER['PHP_SELF']; 
	
	switch ($page) {
		case 'index.php':
			// ADD Special Page Templates
			break;
		default:
	}
	*/

	$TPL = new FastTemplate(TEMPLATE_PATH);
	$TPL->define($used_tpl);
	
	$TPL->assign('SITE_TITLE', $_SiteConfig['site']['name']);
	$TPL->assign('PAGE_TITLE', $_SiteConfig['site']['title']);
	$TPL->assign('PAGE_DESCRIPTION', $_SiteConfig['site']['description']);
	$TPL->assign('PAGE_KEYWORDS', $_SiteConfig['site']['keywords']);
	$TPL->assign('PAGE_HEADER', '');
	$TPL->assign('SITE_URL', $_SiteConfig['site']['url']);
	$TPL->assign('BODY_TAG', '');
	$TPL->assign('QUICK_LINKS', '');
	$TPL->assign('TABS_MENU', '' );
	$TPL->assign('WWW_ROOT', WWW_ROOT );
	
	return $TPL;
}



/**
 * Parses and outputs the page
 *
 * @param object $TPL
 * @return boolean
 */
function template_output (&$TPL)
{

	if($_SESSION['userlevel'] >= USER) {
		// Authenticated
		$TPL->assign('MY_USERNAME', $_SESSION['username'] );
	} else {
		$TPL->assign('MY_USERNAME', "Not Logged in.");
	}
	$TPL->parse('CONTENT', 'content');
	$TPL->parse('HEADER', 'header');
	$TPL->parse('MAIN_MENU', 'menu')	;
	$TPL->parse('FOOTER', 'footer');
	$TPL->parse('CONTENT_PAGE', 'main');
	$TPL->parse('PAGE', 'page');
	$TPL->FastPrint('PAGE');

	return true;
}
?>