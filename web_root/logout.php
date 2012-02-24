<?php
/**
 * logout.php 
 * There is nothing interesting to see here.. destroy the session and redirect
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011
 */

require_once('config.php');
session_unset();
session_destroy();
setcookie('JSESSIONID', $cookievalue[1], time() - 1000, "/jasperserver-pro");

?>
