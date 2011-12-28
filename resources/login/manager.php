<?php
/**
 * Main login management class.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package PNP_Login
 */

require_once 'login/base.php';
require_once 'login/iManager.php';

/**
 * The main login class to authenticate, change passwords, etc.
 */
class PNP_Login_Manager implements _PNP_Login_iManager
{
    /**
     * Logger.
     */
    protected $logger;

    /**
     * Generates a random alpha-numeric character.
     * @return string the random character
     */
    protected static function _randomChar()
    {
        static $chars;
        static $chars_len;

        if (!isset($chars)) {
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            $chars_len = strlen($chars);
        }

        return $chars{rand(0, $chars_len - 1)};

    }

    /**
     * Generates a random alpha-numeric string.
     * @param int $len the desired length of the string
     * @return string the random string
     */
    protected static function _randomString($len)
    {
        $str = '';
        for ($i = 0; $i < $len; $i++)
            $str .= self::_randomChar();
        return $str;
    }

    /**
     * Take the hash of a pssword with a salt.
     * @param string $password the password to hash
     * @param string $salt the salt to include
     * @return string the hashed result
     */
    protected static function _hash($password, $salt='')
    {
        return hash('sha256', $password.$salt);
    }

    /**
     * Generates a random salt.
     * @return string the salt
     */
    protected static function _generateSalt()
    {
        return self::_randomString(PNP_LOGIN_SALT_LENGTH);
    }

    public function __construct($model)
    {
        $this->model = $model;
        $this->logger =& EWL_getLogger(PNP_LOGIN_LOGGER);
    }

    public function authenticate($username, $password)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $failed = $this->model->getFailedLoginCount($username, time() - PNP_LOGIN_FAILED_SECONDS);
        if ($failed >= PNP_LOGIN_FAILED_COUNT) {
            $this->logger->log('Authentication back off for user: '.$username);
            throw new PNP_Login_Back_Off_Exception();
        }

        $row = $this->model->getLoginInfoByUsername($username);
        if (!$row) {
            $this->logger->log(sprintf('Bad username. Supplied: %s from IP %s', $username, $ip));
            $this->model->addFailedLogin($username, $ip);
            throw new PNP_Login_No_User_Found_Exception($username);
        }
		
        // Adding compatibility for CPDP old logins
        if ($row['salt'] == 'SALT') {
        	// Standard MD5 Hash
        	$givenHash = md5($password);
        } else {
        	// SHA256 Salted password
        	$givenHash = $this->_hash($password, $row['salt']);
        }
        if ($givenHash != $row['hash']) {
            $this->logger->log(sprintf('Bad password for user. Username: %s from IP %s', $username, $ip));
            $this->model->addFailedLogin($username, $ip);
            throw new PNP_Login_Bad_Password_Exception($username);
        }

        $this->logger->log(sprintf('User %s (%d) with userlevel %d logged in', $username, $row['id'], $row['userlevel']));
        return array('id' => $row['id'], 'userlevel' => $row['userlevel'], 'username' => $username);
    }

    private function _changePassword($user_id, $new_password)
    {
        $new = array();
        $new['salt'] = $this->_generateSalt();
        $new['hash'] = $this->_hash($new_password, $new['salt']);
        $this->model->updateLoginInfo($user_id, $new);
    }

    public function changePassword($user_id, $old_password, $new_password)
    {
        $this->model->start();

        $row = $this->model->getLoginInfoByUserID($user_id);
        if (!$row) {
            $this->model->fail();
            throw new RuntimeException('bad user_id: '.$user_id);
        }

        if ($this->_hash($old_password, $row['salt']) != $row['hash']) {
            $this->model->fail();
            $this->logger->log(sprintf('User id %d provided bad old password for changing password', $user_id));
            throw new PNP_Login_Bad_Password_Exception($user_id);
        }

        $this->_changePassword($user_id, $new_password);

        $this->model->complete();

        $this->logger->log(sprintf('User id %d changed his/her password', $user_id));
    }

    public function adminChangePassword($user_id, $new_password)
    {
        $this->model->start();

        $row = $this->model->getLoginInfoByUserID($user_id);
        if (!$row) {
            $this->model->fail();
            throw new RuntimeException('bad user_id: '.$user_id);
        }

        $this->_changePassword($user_id, $new_password);

        $this->model->complete();

        $this->logger->log(sprintf('User id %d had password changed by admin', $user_id));
    }

    private function _generatePasswordResetToken($user_id, $old_token_timestamp)
    {
        $this->model->start();

        $now = time();
        if ($old_token_timestamp > 0 && $now - $old_token_timestamp < PNP_LOGIN_TOKEN_REQUEST_BACK_OFF_SECONDS) {
            $this->model->fail();
            $this->logger->log(sprintf('Back off for generating password reset token for user id %d', $user_id));
            throw new PNP_Login_Back_Off_Exception();
        }

        $token = $this->_randomString(PNP_LOGIN_TOKEN_LENGTH);

        $new = array();
        $new['token_timestamp'] = $now;
        $new['token'] = $this->_hash($token);
        $this->model->updateLoginInfo($user_id, $new);

        $this->model->complete();
        return $token;
    }

    public function generatePasswordResetToken($username)
    {
        $this->model->start();

        $row = $this->model->getLoginInfoByUsername($username);
        if (!$row) {
            $this->model->fail();
            $this->logger->log(sprintf('Unknown username for generating password reset token (%s)', $username));
            throw new PNP_Login_No_User_Found_Exception($username);
        }

        $token = $this->_generatePasswordResetToken($row['id'], $row['token_timestamp']);

        $this->model->complete();

        $this->logger->log(sprintf('Generated password reset token for username %s', $username));
        return $token;
    }

    public function adminGeneratePasswordResetToken($user_id)
    {
        $this->model->start();

        $row = $this->model->getLoginInfoByUserID($user_id);
        if (!$row) {
            $this->model->fail();
            throw new RuntimeException('bad user_id: '.$user_id);
        }

        $token = $this->_generatePasswordResetToken($user_id, $row['token_timestamp']);

        $this->model->complete();

        $this->logger->log(sprintf('Admin generated password reset token for user id %d', $user_id));
        return $token;
    }

    public function usePasswordResetToken($token, $new_password)
    {
        $this->model->start();

        $row = $this->model->getLoginInfoByToken($this->_hash($token));
        if (!$row) {
            $this->model->fail();
            $this->logger->log('Bad password reset token');
            throw new PNP_Login_Bad_Token_Exception();
        }

        if (time() - $row['token_timestamp'] > PNP_LOGIN_TOKEN_LIFETIME_SECONDS) {
            $this->model->fail();
            $this->logger->log('Password reset token expired for user id '.$row['id']);
            throw new PNP_Login_Token_Expired_Exception();
        }

        $new = array();
        $new['salt'] = $this->_generateSalt();
        $new['hash'] = $this->_hash($new_password, $new['salt']);
        $new['token'] = '';
        $new['token_timestamp'] = 0;
        $this->model->updateLoginInfo($row['id'], $new);

        $this->model->complete();

        $this->logger->log(sprintf('Password reset token used for user id %d with userlevel %d', $row['id'], $row['userlevel']));
        return array('id' => $row['id'], 'userlevel' => $row['userlevel']);
    }

    public function register($username, $password, $userlevel)
    {
        $this->model->start();

        $row = $this->model->getLoginInfoByUsername($username);
        if ($row) {
            $this->logger->log(sprintf('Username %s exists, can\'t register', $username));
            $this->model->fail();
            throw new PNP_Login_User_Exists_Exception();
        }

        $new = array();
        $new['username'] = $username;
        $new['userlevel'] = $userlevel;
        $new['salt'] = $this->_generateSalt();
        $new['hash'] = $this->_hash($password, $new['salt']);
        $new['token'] = '';
        $new['token_timestamp'] = 0;
        $row = $this->model->insertLoginInfo($new);

        $this->model->complete();

        return array('id' => $row['id'], 'userlevel' => $row['userlevel']);
    }

    public function clean()
    {
        $this->model->cleanFailedLogins(time() - PNP_LOGIN_FAILED_SECONDS);
    }

}
?>
