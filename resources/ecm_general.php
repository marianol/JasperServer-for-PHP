<?php
/**
 * General utilities for this Application.
 * 
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package ewl
 */

function &get_dbcon() {
	global $_SiteConfig;
    static $con;

    if (!isset($con)) {
        require_once 'adodb5/adodb.inc.php';
        $dsn = $_SiteConfig['dsn']['dbtype'] . '://' 
        				. $_SiteConfig['dsn']['username'] . ':' . $_SiteConfig['dsn']['password'] 
        				. '@' . $_SiteConfig['dsn']['host'] . '/' . $_SiteConfig['dsn']['database'] . $_SiteConfig['dsn']['optionstring'];
        $con = ADONewConnection($dsn);
    }
	$con->SetFetchMode(ADODB_FETCH_ASSOC);
    return $con;
}

function &get_login_manager() {
    static $login;

    if (!isset($login)) {

        require_once 'login/database_model.php';
        require_once 'login/manager.php';
        require_once 'login/cracklib_decorator.php';
        require_once 'login/sanitizing_decorator.php';

        class PNP_Login_Email_Sanitizing_Decorator extends PNP_Login_Sanitizing_Decorator {
            protected static function _is_valid_username($username)
            {
                return filter_var($username, FILTER_VALIDATE_EMAIL) !== false;
            }
        }

        $con =& get_dbcon();
        
        $users_field_map =array(
                'id' => 'id',
                'username' => 'email',
                'hash' => 'password',
                'salt' => 'salt',
                'userlevel' => 'userlevel',
                'token' => 'login_token',
                'token_timestamp' => 'login_token_time' );

        $failed_logins_field_map = array(
            	'username' => 'username',
            	'ip' => 'ip',
            	'timestamp' => 'timestamp' );
            
        $model = new PNP_Login_Database_Model($con, 'ecm_users', $users_field_map, 'ecm_failed_logins', $failed_logins_field_map );
        $login = new PNP_Login_Manager($model);
        // $login = new PNP_Login_Cracklib_Decorator($login, false); // Use Cracklib (Not used in this project)
        $login = new PNP_Login_Email_Sanitizing_Decorator($login);

    }

    return $login;
}

/**
 * Get User Name by ID
 *
 * @param unknown_type $ADODBCon
 * @param unknown_type $givenID
 * @param unknown_type $linked
 * @return unknown
 */
function ECM_GetUserName($ADODBCon, $UserID, $linked = true) {

	$sql = 'SELECT `firstname` , `lastname` , `email` FROM `' . TABLE_PREFIX . 'users` WHERE `id` = ' . $UserID ;

	$result = $ADODBCon->GetRow($sql);
	if ($result === false) {
		// Errors
		$UserName = false;
	} else {
		$UserName = $result['firstname'] . ' ' . $result['lastname'];
	}
	if ($linked) {
		$UserName = '<a href="' . WWW_ROOT . 'user/user_show.php?uid=' . base64_encode($UserID) . '">' . $UserName . '</a>';
	}
	return $UserName;
}

function ECM_SendNotification ($Subject, $Body, $SentToEmail, $SentToName = '') {
	global $_SiteConfig;
	
	require_once('PHPMailer/class.phpmailer.php');
	
	$mail             = new PHPMailer(); // defaults to using php "mail()"
	
	$Body             .= "\n\n ETSZONE.com\n Phone: 713-559-1400";
	 
	
	$mail->From = $_SiteConfig['emailsentfrom']['email'];
	$mail->FromName  = $_SiteConfig['emailsentfrom']['name'];

	
	if (is_array($SentToEmail)) {
		foreach ($SentToEmail as $name => $emailaddress) {
			$mail->AddAddress($emailaddress, $name);
		}
	} else {
		$SentToName = ($SentToName == '') ? $SentToEmail : $SentToName;	
		$mail->AddAddress($SentToEmail, $SentToName);
	}

	$mail->Subject    = "ETSZONE Payment System: " . $Subject;
	
	$mail->Body = $Body;
	$mail->IsHTML(true);
	 
	if(!$mail->Send()) {
		return "Mailer Error: " . $mail->ErrorInfo;
	} else {
		return true;
	}
}

?>