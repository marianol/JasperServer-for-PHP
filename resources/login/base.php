<?php
/**
 * Base exceptions and constants.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package PNP_Login
 */

/**
 * The length of the salt to use in number of characters (int).
 */
if (!defined('PNP_LOGIN_SALT_LENGTH'))
    define('PNP_LOGIN_SALT_LENGTH', 10);

/**
 * The time elapsed after which to forget about failed login attempts in
 * seconds (int).
 * That is, no matter how many failed attempts a user had over
 * PNP_LOGIN_FAILED_SECONDS ago, they will not be held against him/her now.
 * @see PNP_LOGIN_FAILED_COUNT, PNP_Login_Back_Off_Exception
 */
if (!defined('PNP_LOGIN_FAILED_SECONDS'))
    define('PNP_LOGIN_FAILED_SECONDS', 300); /* 5 minutes */

/**
 * The number of recent failed login attempts allowed (int).
 * That is, PNP_LOGIN_FAILED_COUNT defines how many failed attempts a user is
 * permitted within the chunk of time defined by {@link
 * PNP_LOGIN_FAILED_SECONDS}.
 * @see PNP_LOGIN_FAILED_SECONDS, PNP_Login_Back_Off_Exception
 */
if (!defined('PNP_LOGIN_FAILED_COUNT'))
    define('PNP_LOGIN_FAILED_COUNT', 3);

/**
 * The length of generated password reset tokens in number of characters (int).
 */
if (!defined('PNP_LOGIN_TOKEN_LENGTH'))
    define('PNP_LOGIN_TOKEN_LENGTH', 64);

/**
 * The amount of time in between generating new password reset tokens for a
 * given user in seconds (int).
 * That is, if a user or admin requests a token now, they will not be able to
 * request another one until PNP_LOGIN_TOKEN_REQUEST_BACK_OFF_SECONDS have
 * elapsed.
 * @see PNP_Login_Back_Off_Exception
 */
if (!defined('PNP_LOGIN_TOKEN_REQUEST_BACK_OFF_SECONDS'))
    define('PNP_LOGIN_TOKEN_REQUEST_BACK_OFF_SECONDS', 3600); /* 1 hour */

/**
 * The amount of time a password reset token will be valid in seconds (int).
 * That is, if a user tries to use a password reset token once it is
 * PNP_LOGIN_TOKEN_LIFETIME_SECONDS old, it will not be accepted.
 * @see PNP_Login_Token_Expired_Exception
 */
if (!defined('PNP_LOGIN_TOKEN_LIFETIME_SECONDS'))
    define('PNP_LOGIN_TOKEN_LIFETIME_SECONDS', 86400); /* 1 day */

/**
 * The logger for the PNP_Login library.
 * @see EWL_getLogger
 */
if (!defined('PNP_LOGIN_LOGGER'))
    define('PNP_LOGIN_LOGGER', LOG_GENERAL);


/**
 * Invalid user input.
 */
class PNP_Login_Invalid_Input_Exception extends RuntimeException
{
    /**
     * @var string the field name containing invalid input
     */
    public $argument;

    public function __construct($argument, $message = null) {
        $this->argument = $argument;

        $m = sprintf('Argument %s given invalid input', $argument);
        if ($message != null)
            $m .= '. '.$message;
        parent::__construct($m);
    }
}

/**
 * Too many login attempts or token requests in a short time.
 */
class PNP_Login_Back_Off_Exception extends RuntimeException
{
}

/**
 * Bad login credentials.
 */
class PNP_Login_Bad_Credentials_Exception extends RuntimeException
{
}

/**
 * Bad username.
 */
class PNP_Login_No_User_Found_Exception extends PNP_Login_Bad_Credentials_Exception
{
    /**
     * @param string $username username that is incorrect
     */
    public function __construct($username) {
        parent::__construct(sprintf('Incorrect username: %s', $username));
    }
}

/**
 * Bad password.
 */
class PNP_Login_Bad_Password_Exception extends PNP_Login_Bad_Credentials_Exception
{
    /**
     * @param int|string $user username or user id whose password is incorrect
     */
    public function __construct($user) {
        parent::__construct(sprintf('Incorrect password for user: %s', $user));
    }
}

/**
 * Bad password reset token.
 */
class PNP_Login_Bad_Token_Exception extends PNP_Login_Bad_Credentials_Exception
{
}

/**
 * Expired password reset token.
 */
class PNP_Login_Token_Expired_Exception extends PNP_Login_Bad_Token_Exception
{
}

/**
 * Username already exists (can't re-register it).
 */
class PNP_Login_User_Exists_Exception extends RuntimeException
{
}

?>
