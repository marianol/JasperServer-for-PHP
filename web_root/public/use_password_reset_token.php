<?php
/**
 * Describe public/use_password_reset_token.php here.
 *
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 */

$template = array('content' => 'login.tpl#USE_TOKEN');
$TPL = masterTemplate($template, true);
$message = '';
$TPL->assign("ERROR", $message);
if (empty($message)) {
		$TPL->assign("ERROR_CLASS", 'hide');
}
// We need to fill the previously entered username on error
$TPL->assign("USERNAME", '');

$TPL->assign("TOKEN", htmlentities($_GET['tkn'], ENT_QUOTES));

template_output($TPL);
?>