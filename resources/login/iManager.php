<?php
/**
 * Base exceptions and constants.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package PNP_Login
 */

require_once 'login/base.php';

/**
 * Interface for {@link PNP_Login_Manager} and {@link PNP_Login_Base_Manager_Decorator}.
 */
interface _PNP_Login_iManager
{
    /**
     * Authenticates a user.
     * @param string $username the user to authenticate
     * @param string $password the plaintext password
     * @return array array of the form array('id' => int, 'userlevel' => int) for the authenticated user
     * @throw {@link PNP_Login_Invalid_Input_Exception} on invalid username or password
     * @throw {@link PNP_Login_No_User_Found_Exception} on incorrect username
     * @throw {@link PNP_Login_Bad_Password_Exception} on incorrect password
     * @throw {@link PNP_Login_Back_Off_Exception} on too many login attempts
     */
    function authenticate($username, $password);

    /**
     * Changes a user's own password.
     * @param int $user_id the user whose password to change (yours)
     * @param string $old_password the plaintext old password
     * @param string $new_password the plaintext new password
     * @throw {@link PNP_Login_Invalid_Input_Exception} on invalid old password or new password
     * @throw {@link PNP_Login_Bad_Password_Exception} on incorrect old password
     */
    function changePassword($user_id, $old_password, $new_password);

    /**
     * Changes a user's password (as a superuser).
     * @param int $user_id the user whose password to change
     * @param string $new_password the plaintext new password
     * @throw {@link PNP_Login_Invalid_Input_Exception} on invalid new password
     */
    function adminChangePassword($user_id, $new_password);

    /**
     * Generates a password reset token.
     * @param string $username the user for which this token will be valid
     * @return string an opaque token to be used for resetting the password
     * @throw {@link PNP_Login_Invalid_Input_Exception} on invalid username
     * @throw {@link PNP_Login_No_User_Found_Exception} on incorrect username
     * @throw {@link PNP_Login_Back_Off_Exception} if existing token is too recent
     */
    function generatePasswordResetToken($username);

    /**
     * Generates a password reset token (as a superuser).
     * @param int $user_id the user for which this token will be valid
     * @return string an opaque token to be used for resetting the password
     * @throw {@link PNP_Login_Back_Off_Exception} if existing token is too recent
     */
    function adminGeneratePasswordResetToken($user_id);

    /**
     * Changes a user's password with a token.
     * @param int $user_id the user for which this token is valid
     * @param string $token the opaque token to use
     * @param string $new_password the plaintext new password
     * @return array array of the form array('id' => int, 'userlevel' => int) for the user
     * @throw {@link PNP_Login_Invalid_Input_Exception} on invalid token or new password
     * @throw {@link PNP_Login_Bad_Token_Exception} on incorrect token
     * @throw {@link PNP_Login_Token_Expired_Exception} on expired token
     */
    function usePasswordResetToken($token, $new_password);

    /**
     * Creates a new user.
     * @param string $username the user to create
     * @param string $password the plaintext password
     * @param int $userlevel the access level
     * @return array array of the form array('id' => int, 'userlevel' => int) for the new user
     * @throw {@link PNP_Login_Invalid_Input_Exception} on invalid username, password, or userlevel
     * @throw {@link PNP_Login_User_Exists_Exception} on existing username
     */
    function register($username, $password, $userlevel);

    /**
     * Runs routine maintenance.
     * Call this on occasion, maybe even periodically in a cron job.
     */
    function clean();
}

?>
