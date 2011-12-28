<?php
/**
 * Database model for login manager.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package PNP_Login
 */

require_once 'login/base.php';

/**
 * A database model for {@link PNP_Login_Manager}.
 * Login info is an array with keys:
 * <ul>
 *  <li>id (int) - user id</li>
 *  <li>username (string) - unique username</li>
 *  <li>salt (string) - password salt</li>
 *  <li>hash (string) - password hash</li>
 *  <li>userlevel (int) - access level</li>
 *  <li>token (string) - hash of password reset token, may be empty</li>
 *  <li>token_timestamp (int) - UNIX timestamp of when the token was generated, may be 0</li>
 * </ul>
 */
class PNP_Login_Database_Model
{
    protected function _usersColumn($name)
    {
        if (isset($this->_users_field_map[$name]))
            return $this->_users_field_map[$name];
        else
            return $name;
    }

    protected function _failedLoginsColumn($name)
    {
        if (isset($this->_failed_logins_field_map[$name]))
            return $this->_failed_logins_field_map[$name];
        else
            return $name;
    }

    /**
     * @param mixed $con ADODB database connection
     * @param string $users_table the name of the users database table
     * @param array $users_field_map a sparse map of renamed columns for the
     * users table. Valid keys are: id, username, salt, hash, userlevel, token,
     * token_timestamp.
     * @param string $failed_logins_table the name of the failed logins
     * database table
     * @param array $failed_logins_field_map a sparse map of renamed columns
     * for the failed logins table. Valid keys are: username, ip, timestamp.
     */
    public function __construct($con,
                                $users_table = 'users',
                                $users_field_map = array(),
                                $failed_logins_table = 'failed_logins',
                                $failed_logins_field_map = array())
    {
        $this->_con = $con;
        $this->_users_table     = $users_table;
        $this->_users_field_map = $users_field_map;
        $this->_failed_logins_table     = $failed_logins_table;
        $this->_failed_logins_field_map = $failed_logins_field_map;
    }

    /**
     * Start a transaction.
     */
    public function start()
    {
        $this->_con->StartTrans();
    }

    /**
     * Complete the current transaction.
     */
    public function complete()
    {
        $this->_con->CompleteTrans();
    }

    /**
     * Fail the current transaction.
     */
    public function fail()
    {
        $this->_con->CompleteTrans(false);
    }

    /**
     * Gets the number of failed logins since a given time.
     * @param string $username the username attempted
     * @param int $time_threshold the UNIX timestamp after which to count
     * @return int the number of failed logins
     */
    public function getFailedLoginCount($username, $time_threshold)
    {
        $failed = $this->_con->GetOne('
            SELECT COUNT(*)
            FROM '.$this->_failed_logins_table.'
            WHERE '.$this->_failedLoginsColumn('username').' = '.$this->_con->qstr($username).'
              AND '.$this->_failedLoginsColumn('timestamp').' > '.$time_threshold
        );
        if ($failed === false)
            throw new RuntimeException('database error');
        return $failed;
    }

    /**
     * Clears out old failed login attempts.
     * @param int $time_threshold the UNIX timestamp before which to clear
     */
    public function cleanFailedLogins($time_threshold)
    {
        if (!$this->_con->Execute('
                DELETE FROM '.$this->_failed_logins_table.'
                WHERE '.$this->_failedLoginsColumn('timestamp').' < '.$time_threshold)) {
            throw new RuntimeException('database error');
        }
    }

    /**
     * Records a failed login attempt.
     * @param string $username the username attempted
     * @param string $ip the IP address the attempt came from
     */
    public function addFailedLogin($username, $ip)
    {
        $new = array();
        $new[$this->_failedLoginsColumn('username')] = $username;
        $new[$this->_failedLoginsColumn('ip')] = $ip;
        $new[$this->_failedLoginsColumn('timestamp')] = time();
        if (!$this->_con->AutoExecute($this->_failed_logins_table, $new, 'INSERT'))
            throw new RuntimeException('database error');
    }

    protected function _getLoginInfoByWhere($where)
    {
        $row = $this->_con->GetRow('
            SELECT '.$this->_usersColumn('id').' AS id, '.
                     $this->_usersColumn('username').' AS username, '.
                     $this->_usersColumn('salt').' AS salt, '.
                     $this->_usersColumn('hash').' AS hash, '.
                     $this->_usersColumn('userlevel').' AS userlevel, '.
                     $this->_usersColumn('token').' AS token, '.
                     $this->_usersColumn('token_timestamp').' AS token_timestamp
            FROM '.$this->_users_table.'
            WHERE '.$where.'
            LIMIT 1'
        );
        if ($row === false)
            throw new RuntimeException('database error');
        elseif (empty($row))
            return false;
        else
            return $row;
    }

    /**
     * Gets login info array for username.
     * @param string $username username for which to get login info
     * @return mixed login info array, or false if username could not be found
     */
    public function getLoginInfoByUsername($username)
    {
        return $this->_getLoginInfoByWhere($this->_usersColumn('username').' = '.$this->_con->qstr($username));
    }

    /**
     * Gets login info array for user id.
     * @param int $user_id user id for which to get login info
     * @return mixed login info array, or false if user id could not be found
     */
    public function getLoginInfoByUserID($user_id)
    {
        return $this->_getLoginInfoByWhere($this->_usersColumn('id').' = '.$user_id);
    }

    /**
     * Gets login info array for token.
     * @param string $token reset password token for user for which to get login info
     * @return mixed login info array, or false if token could not be found
     */
    public function getLoginInfoByToken($token)
    {
        return $this->_getLoginInfoByWhere($this->_usersColumn('token').' = '.$this->_con->qstr($token));
    }

    /**
     * Updates login info for a user.
     * @param int $user_id the user id whose login info to update
     * @param array $new_info the array of columns to update. Missing columns
     * are OK and simply won't be updated.
     */
    public function updateLoginInfo($user_id, $new_info)
    {
        $rekeyed = array();
        foreach ($new_info as $column => $value)
            $rekeyed[$this->_usersColumn($column)] = $value;
        if (!$this->_con->AutoExecute($this->_users_table, $rekeyed, 'UPDATE',
                                      $this->_usersColumn('id').' = '.$user_id)) {
            throw new RuntimeException('database error');
        }
    }

    /**
     * Inserts login info for a user.
     * @param array $new_info the array of columns to insert.
     * @return mixed login info array, or false if token could not be found
     */
    public function insertLoginInfo($new_info)
    {
        $rekeyed = array();
        foreach ($new_info as $column => $value)
            $rekeyed[$this->_usersColumn($column)] = $value;
        if (!$this->_con->AutoExecute($this->_users_table, $rekeyed, 'INSERT'))
            throw new RuntimeException('database error');

        $new_info['id'] = $this->_con->Insert_ID();
        return $new_info;
    }

}

?>
