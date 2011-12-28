<?php
/**
 * Check user input for login manager.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package PNP_Login
 */

require_once 'login/base.php';
require_once 'login/base_manager_decorator.php';

/**
 * A {@link PNP_Login_Manager} decorator to check user input.
 */
class PNP_Login_Sanitizing_Decorator extends PNP_Login_Base_Manager_Decorator
{

    protected static function _is_valid_user_id($user_id)
    {
        return preg_match('/^(0|[1-9][0-9]*)$/', $user_id) === 1;
    }

    protected static function _is_valid_username($username)
    {
        return preg_match('/^[A-Za-z]\w{2,30}$/', $username) === 1;
    }

    protected static function _is_valid_password($password)
    {
        return preg_match('/^.{6,30}$/', $password) === 1;
    }

    protected static function _is_valid_token($token)
    {
        return preg_match('/^[A-Za-z0-9]{'.PNP_LOGIN_TOKEN_LENGTH.'}$/', $token) === 1;
    }

    protected static function _is_valid_userlevel($userlevel)
    {
        return preg_match('/^(0|[1-9][0-9]*)$/', $userlevel) === 1;
    }

    public function authenticate($username, $password)
    {
        if (!$this->_is_valid_username($username))
            throw new PNP_Login_Invalid_Input_Exception('username');
        if (!$this->_is_valid_password($password))
            throw new PNP_Login_Invalid_Input_Exception('password');
        return parent::authenticate($username, $password);
    }

    public function changePassword($user_id, $old_password, $new_password)
    {
        if (!$this->_is_valid_user_id($user_id))
            throw new PNP_Login_Invalid_Input_Exception('user_id');
        if (!$this->_is_valid_password($old_password))
            throw new PNP_Login_Invalid_Input_Exception('old_password');
        if (!$this->_is_valid_password($new_password))
            throw new PNP_Login_Invalid_Input_Exception('new_password');
        return parent::changePassword($user_id, $old_password, $new_password);
    }

    public function adminChangePassword($user_id, $new_password)
    {
        if (!$this->_is_valid_user_id($user_id))
            throw new PNP_Login_Invalid_Input_Exception('user_id');
        if (!$this->_is_valid_password($new_password))
            throw new PNP_Login_Invalid_Input_Exception('new_password');
        return parent::adminChangePassword($user_id, $new_password);
    }

    public function generatePasswordResetToken($username)
    {
        if (!$this->_is_valid_username($username))
            throw new PNP_Login_Invalid_Input_Exception('username');
        return parent::generatePasswordResetToken($username);
    }

    public function adminGeneratePasswordResetToken($user_id)
    {
        if (!$this->_is_valid_user_id($user_id))
            throw new PNP_Login_Invalid_Input_Exception('user_id');
        return parent::adminGeneratePasswordResetToken($user_id);
    }

    public function usePasswordResetToken($token, $new_password)
    {
        if (!$this->_is_valid_token($token))
            throw new PNP_Login_Invalid_Input_Exception('token');
        if (!$this->_is_valid_password($new_password))
            throw new PNP_Login_Invalid_Input_Exception('new_password');
        return parent::usePasswordResetToken($token, $new_password);
    }

    public function register($username, $password, $userlevel)
    {
        if (!$this->_is_valid_username($username))
            throw new PNP_Login_Invalid_Input_Exception('username');
        if (!$this->_is_valid_password($password))
            throw new PNP_Login_Invalid_Input_Exception('password');
        if (!$this->_is_valid_userlevel($userlevel))
            throw new PNP_Login_Invalid_Input_Exception('userlevel');
        return parent::register($username, $password, $userlevel);
    }
}

?>
