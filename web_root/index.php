<?php
/**
 * index.php Main Site Page.
 * There is nothing interesting to see here.. just redirect to the login.
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011, Essential Technology Solutions, LLC
 */

/* Make sure prepend.php was called */
assert(defined('EWL_PREPENDED'));

if($_SESSION['userlevel'] >= USER) {
	// You are already Logged in!!
	header('Location: ' . WWW_ROOT . 'user/index.php');
} else {
	// Guest
	header('Location: ' . WWW_ROOT . 'public/login.php');
}

?>