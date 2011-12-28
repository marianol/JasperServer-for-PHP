<?php
/**
 * Check passwords against Cracklib.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package PNP_Login
 */

require_once 'login/base.php';
require_once 'login/base_manager_decorator.php';

/**
 * Path to the cracklib-check binary (string).
 */
if (!defined('PNP_LOGIN_CRACKLIB_PATH'))
    define('PNP_LOGIN_CRACKLIB_PATH', '/usr/sbin/cracklib-check');

/**
 * Cracklib believes the password is too simple.
 */
class PNP_Login_Password_Too_Simple_Exception extends PNP_Login_Invalid_Input_Exception
{
}

/**
 * A {@link PNP_Login_Manager} decorator to check passwords against Cracklib.
 */
class PNP_Login_Cracklib_Decorator extends PNP_Login_Base_Manager_Decorator
{
    /**
     * @var boolean whether to raise RuntimeExceptions if Cracklib fails to run
     */
    protected $strict_mode;

    /**
     * Checks a password with cracklib.
     * @param string $argument the argument name supplying the password
     * @param string $password the password to check
     * @throw RuntimeException if running Cracklib fails
     * @throw {@link PNP_Login_Password_Too_Simple_Exception} if Cracklib doesn't like the password
     */
    protected static function _crack_check($argument, $password)
    {
        $pipes = null;
        $r = proc_open(PNP_LOGIN_CRACKLIB_PATH, array(
                0 => array('pipe', 'r'),
                1 => array('pipe', 'w'),
                2 => array('pipe', 'w')
            ), $pipes
        );
        if (!$r)
            throw new RuntimeException(sprintf('%s not available', PNP_LOGIN_CRACKLIB_PATH));
        fwrite($pipes[0], $password."\n");
        fclose($pipes[0]);

        $out = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $err = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exit_status = proc_close($r);
        if ($exit_status != 0)
            throw new RuntimeException(sprintf('%s returned non-zero status (%d)', PNP_LOGIN_CRACKLIB_PATH, $exit_status));

        $out = explode(': ', $out, 2);
        if ($out[0] != $password)
            throw new RuntimeException(sprintf('%s produced malformed output', PNP_LOGIN_CRACKLIB_PATH));

        $out[1] = trim($out[1]);

        if ($out[1] != 'OK')
            throw new PNP_Login_Password_Too_Simple_Exception($argument, 'Cracklib: '.$out[1]);
    }

    /**
     * Checks a password with cracklib (honoring {@link strict_mode}).
     * @param string $argument the argument name supplying the password
     * @param string $password the password to check
     * @throw RuntimeException if running Cracklib fails and {@link strict_mode} is on
     * @throw {@link PNP_Login_Password_Too_Simple_Exception} if Cracklib doesn't like the password
     */
    protected function crack_check($argument, $password)
    {
        if ($this->strict_mode) {
            return $this->_crack_check($argument, $password);
        }
        else{
            try {
                return $this->_crack_check($argument, $password);
            }
            catch (PNP_Login_Password_Too_Simple_Exception $e) {
                /* don't let this get caught (ignored) as a RuntimeException */
                throw $e;
            }
            catch (RuntimeException $e) {
                $logger =& EWL_getLogger(PNP_LOGIN_LOGGER);
                $logger->log('Craklib failed: '.$e->getMessage());
                return;
            }
        }
    }

    /**
     * @param _PNP_Login_iManager $decorated the object which this decorator decorates
     * @param boolean $strict_mode whether to raise RuntimeExceptions if Cracklib fails to run
     */
    public function __construct($decorated, $strict_mode=true)
    {
        parent::__construct($decorated);
        $this->strict_mode = $strict_mode;
    }

    public function changePassword($user_id, $old_password, $new_password)
    {
        $this->crack_check('new_password', $new_password);
        return parent::changePassword($user_id, $old_password, $new_password);
    }

    public function adminChangePassword($user_id, $new_password)
    {
        $this->crack_check('new_password', $new_password);
        return parent::adminChangePassword($user_id, $new_password);
    }

    public function usePasswordResetToken($token, $new_password)
    {
        $this->crack_check('new_password', $new_password);
        return parent::usePasswordResetToken($token, $new_password);
    }

    public function register($username, $password, $userlevel)
    {
        $this->crack_check('password', $password);
        return parent::register($username, $password, $userlevel);
    }
}

?>
