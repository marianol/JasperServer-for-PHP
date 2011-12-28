<?php
/**
 * index.php Main Site Page.
 * There is nothing interesting to see here.. just redirect to the login or proper page
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011
 */

require(config.php);

if($_SESSION['userlevel'] >= USER) {
	// You are already Logged in!!
	header('Location: ' . WWW_ROOT . 'home.php');
} else {
	// Guest
	header('Location: ' . WWW_ROOT . 'login.php');
}

?>