<?php
/**
 * public/login_process.php Handles the user Login
 *
 * redirects to guest/index.php on log in error
 * redirects to user/index.php on log in
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 */

$con =& get_dbcon();
// $con->debug = true;

$login =& get_login_manager();

$_SESSION['login'] = array();
$_SESSION['userlevel'] = GUEST;

if (!isset($_POST['username']) || !isset($_POST['password']))
    trigger_error('POST data missing', E_USER_ERROR);

try {
    $info = $login->authenticate($_POST['username'], $_POST['password']);
}
catch (PNP_Login_Bad_Credentials_Exception $e) {
    header('Location: '.WWW_ROOT.'public/login.php?emsg=1');
    exit;
}
catch (PNP_Login_Back_Off_Exception $e) {
    header('Location: '.WWW_ROOT.'public/login.php?emsg=2');
    exit;
}
catch (PNP_Login_Invalid_Input_Exception $e) {
	// on invalid username or password
    header('Location: '.WWW_ROOT.'public/login.php?emsg=1');
    exit;
}
catch (PNP_Login_No_User_Found_Exception $e) {
	// on invalid username or password
    header('Location: '.WWW_ROOT.'public/login.php?emsg=1');
    exit;
}
catch (PNP_Login_Bad_Password_Exception $e) {
	// on invalid username or password
    header('Location: '.WWW_ROOT.'public/login.php?emsg=1');
    exit;
}

$_SESSION['login'] = $info;
$_SESSION['userlevel'] = $info['userlevel'];
$_SESSION['username'] = $info['username'];
$login->clean();

header('Location: '.WWW_ROOT.'user/index.php');
exit;

?>