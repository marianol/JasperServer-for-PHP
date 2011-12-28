<?php
/**
 * Utilities for application logging.
 * 
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package ewl
 */

require_once 'Log.php';

/**
 * A blacklist for uninteresting error log entries 
 *
 * Each entry in this array has the form:
 *  array(['file' => '/PCRE pattern/',] ['message' => '/PRCE pattern/',]).
 *
 * Use {@link EWL_blacklistErrorLog()} only to modify this array.
 *
 * @global array $GLOBALS['_EWL_ERROR_LOG_BLACKLIST']
 */
$GLOBALS['_EWL_ERROR_LOG_BLACKLIST'] = array();

/* PEAR Log isn't declared static to be compatible with PHP4. */
EWL_blacklistErrorLog(array(
   'message' => '/^Non-static method Log::\w+\(\) should not be called statically$/',
   // 'file' => '/cls_fast_template/'
));

/**
 * Blacklists an uninteresting error log entry.
 * @param array $patterns has the form:
 *  array(['file' => '/PCRE pattern/',] ['message' => '/PRCE pattern/',])
 */
function EWL_blacklistErrorLog($patterns)
{
    global $_EWL_ERROR_LOG_BLACKLIST;
    $_EWL_ERROR_LOG_BLACKLIST[] = $patterns;
}

/**
 * Determines whether an error log entry is blacklisted.
 * @param int $code as in PHP error handler
 * @param string $message as in PHP error handler
 * @param string $file as in PHP error handler
 * @param int $line as in PHP error handler
 * @return bool whether the error log is blacklisted
 * @access private
 */
function _EWL_isErrorLogBlacklisted($code, $message, $file, $line)
{
    global $_EWL_ERROR_LOG_BLACKLIST;
	
    // Disregard library files
    if (preg_match('/cls_fasttemplate/', $file) == 1) return true;
    if (preg_match('/GUP_Pager/', $file) == 1) return true;
    
    foreach ($_EWL_ERROR_LOG_BLACKLIST as $blacklist_entry) {

    	if (isset($blacklist_entry['file']) &&
            preg_match($blacklist_entry['file'], $file) !== 1) {
            continue;
        } 

        if (isset($blacklist_entry['message']) &&
            preg_match($blacklist_entry['message'], $message) !== 1) {
            	
            continue;
        }

        return true;
    }
	
    return false;
}

/**
 * Gets the PEAR Log instance for the given file.
 * @param string $file The log file's name
 * @return Log reference to logger
 */
function &EWL_getLogger($file)
{
    $log =& Log::singleton('file', $file);
    if (!$log->open()) {
        ini_set('display_errors', true);
        trigger_error('Log file '.$file.' could not be opened', E_USER_ERROR);
    }
    return $log;
}

/**
 * Error handler that logs errors
 *
 * Modified to use {@link EWL_getLogger()}.
 * Modified to output generic message and exit or return false.
 *
 * @author http://www.indelible.org/php/Log/guide.html#logging-from-standard-error-handlers
 * @access private
 */
function _EWL_errorHandler($code, $message, $file, $line)
{

    if (!_EWL_isErrorLogBlacklisted($code, $message, $file, $line)) {

        /* Map the PHP error to a Log priority. */
        switch ($code) {
        case E_WARNING:
        case E_USER_WARNING:
            $priority = PEAR_LOG_WARNING;
            break;
        case E_STRICT:
        case E_NOTICE:
        case E_USER_NOTICE:
            $priority = PEAR_LOG_NOTICE;
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $priority = PEAR_LOG_ERR;
            break;
        default:
            $priority = PEAR_LOG_INFO;
        }

        $logger =& EWL_getLogger(LOG_GENERAL);
        //$errorlogger =& EWL_getLogger(LOG_ERROR);
        $logger->log($message . ' in ' . $file . ' at line ' . $line,
                     $priority);
		
    }

    switch ($code) {
    case E_STRICT:
    case E_NOTICE:
    case E_USER_NOTICE:
    case E_DEPRECATED:
    case E_USER_DEPRECATED:
	case E_WARNING:
    	/* calls normal error handler */
        return false; 
	break;
    
    case E_USER_WARNING:
    case E_ERROR:
    case E_USER_ERROR:
    default:
        echo '<h1>Error! - ' . $code . '</h1>';
        echo '<p>This application has encountered a critical error.<br />';
        echo 'Please email <a href="mailto:support@etszone.com">';
        echo 'support@etszone.com</a> or call 713-559-1400 ';
        echo 'with a description of the problem.</P>';
        exit;
    }
    
    /* Don't execute PHP internal error handler */
    return true;
}

set_error_handler('_EWL_errorHandler');

/**
 * Exception handler that logs errors
 *
 * Simply passes the exception off to {@link _EWL_errorHandler()}.
 * @access private
 */
function _EWL_exceptionHandler($exception)
{
    _EWL_errorHandler($exception->getCode(),
                      get_class($exception).' Exception: '.$exception->getMessage(),
                      $exception->getFile(),
                      $exception->getLine());
}

set_exception_handler('_EWL_exceptionHandler');

?>
