<?php
/**
 * public/logout.php Log the user out.
 *
 * redirects to public/index.php
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 */

/* Make sure prepend.php was called */
assert(defined('EWL_PREPENDED'));

//$login =& get_login_manager();

// Is this right????? The login manager should handle this....
session_destroy();

header('Location: ' . WWW_ROOT . 'index.php');
?>