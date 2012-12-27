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
// Delete JRS session
setcookie('JSESSIONID', '', time() - 1000, $_SESSION["JRSPath"] );
// Destroy my session
session_unset();
session_destroy();

header('Location: ' . WWW_ROOT );
?>