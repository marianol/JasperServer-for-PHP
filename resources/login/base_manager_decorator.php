<?php
/**
 * Base class for login manager decorators.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package PNP_Login
 */

require_once 'login/base.php';
require_once 'login/iManager.php';

/**
 * A base class for {@link PNP_Login_Manager} decorators.
 * An instance of this class would be entirely transparent and would simply
 * chain to the decorated object.
 */
abstract class PNP_Login_Base_Manager_Decorator implements _PNP_Login_iManager
{

    /**
     * The object which this decorator decorates.
     * @var _PNP_Login_iManager
     */
    protected $decorated;

    /**
     * @param _PNP_Login_iManager $decorated the object which this decorator decorates
     */
    public function __construct($decorated)
    {
        $this->decorated = $decorated;
    }

    public function authenticate($username, $password)
    {
        return $this->decorated->authenticate($username, $password);
    }

    public function changePassword($user_id, $old_password, $new_password)
    {
        return $this->decorated->changePassword($user_id, $old_password, $new_password);
    }

    public function adminChangePassword($user_id, $new_password)
    {
        return $this->decorated->adminChangePassword($user_id, $new_password);
    }

    public function generatePasswordResetToken($username)
    {
        return $this->decorated->generatePasswordResetToken($username);
    }

    public function adminGeneratePasswordResetToken($user_id)
    {
        return $this->decorated->adminGeneratePasswordResetToken($user_id);
    }

    public function usePasswordResetToken($token, $new_password)
    {
        return $this->decorated->usePasswordResetToken($token, $new_password);
    }

    public function register($username, $password, $userlevel)
    {
        return $this->decorated->register($username, $password, $userlevel);
    }

    public function clean()
    {
        return $this->decorated->clean();
    }
}

?>
