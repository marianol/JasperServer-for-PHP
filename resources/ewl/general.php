<?php
/**
 * General utilities.
 * 
 * @copyright Copyright (c) 2009, Essential Technology Solutions, LLC
 * @package ewl
 */

/**
 * Output a Debug message to the Browser
 *
 * @param string $message
 */
function _EWL_Debug_Print($message) {
	echo "<hr>" . strip_tags($message) . "<hr>";
}

/**
 * Check if a user is logged in (based on Session UserLevel
 *
 * @return boolean
 */
function _EWL_isLoggedIn()
{
    if (!isset($_SESSION['userlevel'])) {
    	return false;
    } elseif ($_SESSION['userlevel'] >= USER) {
     	return true;
    } else {
    	return false;
    }
      
}

/**
 * Transforms MYSQL DY TIME fields
 *
 * @param string $format
 * @param MySQL daytime Field $datetime
 * @return string
 */
function _EWL_mysql_datetime( $format, $datetime) {
   $pattern = "/^(\d{4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2}):(\d{2})$/i";
   if(preg_match($pattern, $datetime, $dt) && checkdate($dt[2], $dt[3], $dt[1])) {
       return date($format, mktime($dt[4], $dt[5], $dt[6], $dt[2], $dt[3], $dt[1]));    
   }
   return $datetime;
}

/**
 * Sanitizes a string
 *
 * @param string $string
 * @return string
 */
function _EWL_string_clean ($string) {
    $string = trim($string);
    $string = htmlentities($string, ENT_QUOTES);
    //$string = addslashes($string);

    return $string;
}

/**
 * Sanitizes an array of strings
 *
 * @param array $array
 * @return array
 */
function _EWL_string_clean_array ($array) {

	$clean_array = array();
	
    foreach ($array as $key => $value) {
		if (!is_array($value)) {
			$clean_array[$key] = _EWL_string_clean($value);
		} else {
			$clean_array[$key] = $value;
		}
    }

    return $clean_array;
}

/**
 * Checks if a string is a a properly formatted e-mail address
 *
 * @param string $string
 * @return boolean
 */
function _EWL_isValidEmail ($string)
{
	$pattern = "/^[\w-]+(\.[\w-]+)*@";
    $pattern .= "([0-9a-z][0-9a-z-]*[0-9a-z]\.)+([a-z]{2,4})$/i";
    if (preg_match($pattern, $string)) {
        // $parts = explode("@", $string);
        return true;
    } else {
        return false;
    }
}
/**
 * Creates a combo box from an array 
 *
 * @param string $ComboName
 * @param string $SelectedValue
 * @param array $array
 * @param string $css
 * @param string $atributo
 * @param boolean $selectall
 * @return string
 */
function _EWL_mk_select_array($ComboName, $SelectedValue, $array, $css = "", $atributo = "", $selectall = false)
{
	
	$combo_box = "";
	$combo_box .= '<select name="'.$ComboName.'" class="'.$css.'" '.$atributo.' >'."\n";
	
	if ($selectall) {   
		$combo_box .= '<option value="_ALL_">ALL</option>'."\n";
	} else {
		$combo_box .= '<option value=""></option>'."\n";
	}

	foreach ($array as $id => $optionvalue)	{
		if ($id === $SelectedValue) {
			$selection =  ' SELECTED ';
		} else {
			$selection = ($SelectedValue != null && $SelectedValue != '' && $id == $SelectedValue) ? ' SELECTED ' : '';
		}
		$combo_box .= '<option value="'. $id .'" '. $selection .'>'. $optionvalue .'</option>'."\n";
	}
	$combo_box .= "</select>\n";

	return $combo_box;
} 

/**
 * Create a combo box from values stored in drop_down table
 *
 * @param ADODB Connection $DBConn
 * @param string $DropDownName
 * @param string $ComboName
 * @param string $SelectedValue
 * @param boolean $emptyvalue
 * @param string $css
 * @param string $attributes
 * @return string
 */
function _EWL_mk_select_db($DBConn, $DropDownName, $ComboName, $SelectedValue, $emptyvalue = true, $css = "", $attributes = "")
{
	// global $db;
	
	$sql = "SELECT * FROM " . TABLE_PREFIX . "drop_down WHERE name='" . $DropDownName . "' order by sort_order";
	
	$result = $DBConn->Execute($sql);
	//$result = $AdodbConn->Execute($sql) or return "DB Error!";	// Fix this....
   	if ( $result === false) {
		trigger_error('No Combo Box Result in _EWL_mk_select_db', E_USER_ERROR);
		}
	$combo_box = "";
	$combo_box .= '<select name="'. $ComboName .'" id="'. $ComboName .'" class="'.$css.'" ' . $attributes . ' >' . "\n";
	
	if ($emptyvalue) $combo_box .= '<option value=""></option>'."\n";
		
	foreach($result as $row => $field) {
		$selection = ($field['value'] == $SelectedValue) ? " selected " : "";		
		$combo_box .= '<option value="'. $field['value'] .'" '. $selection .'>' .$field['option']. '</option>'."\n";	
	}
	$combo_box .= "</select>\n";

	return $combo_box;
} 

/**
 * Gives the timestamp of the given date
 *
 * @param string $date
 * @param string $sep
 * @return int
 */
function _EWL_Date2Timestamp($date, $sep = '-') {
	$date_pieces = explode($sep,$date);
	if (count($date_pieces) == 3) { // @todo check the date more
		$timestamp = mktime(0,0,0, $date_pieces[0], $date_pieces[1], $date_pieces[2]);
	} else {
		$timestamp = false;
	}
	return $timestamp;
}

/**
 * function EWL_SendDownloadHeaders - send file to the browser
 *
 * Original Source: SM core src/download.php
 * moved here to make it available to other code, and separate
 * front end from back end functionality.
 *
 * @param string $type0 first half of mime type
 * @param string $type1 second half of mime type
 * @param string $filename filename to tell the browser for downloaded file
 * @param boolean $force whether to force the download dialog to pop
 * @param optional integer $filesize send the Content-Header and length to the browser
 * @return void
 */
 function EWL_SendDownloadHeaders($type0, $type1, $filename, $force, $filesize=0) {

     $isIE = $isIE6 = 0;

     $get= 'HTTP_USER_AGENT';
     $HTTP_USER_AGENT = '';
     getGlobalVar($HTTP_USER_AGENT, $get);

     if (strstr($HTTP_USER_AGENT, 'compatible; MSIE ') !== false &&
         strstr($HTTP_USER_AGENT, 'Opera') === false) {
         $isIE = 1;
     }

     if (strstr($HTTP_USER_AGENT, 'compatible; MSIE 6') !== false &&
         strstr($HTTP_USER_AGENT, 'Opera') === false) {
         $isIE6 = 1;
     }

     if (isset($languages[$squirrelmail_language]['XTRA_CODE']) &&
         function_exists($languages[$squirrelmail_language]['XTRA_CODE'])) {
         $filename =
         $languages[$squirrelmail_language]['XTRA_CODE']('downloadfilename', $filename, $HTTP_USER_AGENT);
     } else {
         $filename = ereg_replace('[\\/:\*\?"<>\|;]', '_', str_replace('&nbsp;', ' ', $filename));
     }


     // See this article on Cache Control headers and SSL
     // http://support.microsoft.com/default.aspx?scid=kb;en-us;323308

     if ($isIE) {
         header ("Pragma: public");
         header ("Cache-Control: no-store, max-age=0, no-cache, must-revalidate"); # HTTP/1.1
         header ("Cache-Control: post-check=0, pre-check=0", false);
         header ("Cache-control: private");

         //set the inline header for IE, we'll add the attachment header later if we need it
         header ("Content-Disposition: inline; filename=$filename");
     }

     if (!$force) {
         // Try to show in browser window
         header ("Content-Disposition: inline; filename=\"$filename\"");
         header ("Content-Type: $type0/$type1; name=\"$filename\"");
     } else {
         // Try to pop up the "save as" box

         // IE makes this hard.  It pops up 2 save boxes, or none.
         // http://support.microsoft.com/support/kb/articles/Q238/5/88.ASP
         // http://support.microsoft.com/default.aspx?scid=kb;EN-US;260519
         // But, according to Microsoft, it is "RFC compliant but doesn't
         // take into account some deviations that allowed within the
         // specification."  Doesn't that mean RFC non-compliant?
         // http://support.microsoft.com/support/kb/articles/Q258/4/52.ASP

         // all browsers need the application/octet-stream header for this
         header ("Content-Type: application/octet-stream; name=\"$filename\"");

         // http://support.microsoft.com/support/kb/articles/Q182/3/15.asp
         // Do not have quotes around filename, but that applied to
         // "attachment"... does it apply to inline too?
         header ("Content-Disposition: attachment; filename=\"$filename\"");

         if ($isIE && !$isIE6) {
             // This combination seems to work mostly.  IE 5.5 SP 1 has
             // known issues (see the Microsoft Knowledge Base)

             // This works for most types, but doesn't work with Word files
             header ("Content-Type: application/download; name=\"$filename\"");

             // These are spares, just in case.  :-)
             //header("Content-Type: $type0/$type1; name=\"$filename\"");
             //header("Content-Type: application/x-msdownload; name=\"$filename\"");
             //header("Content-Type: application/octet-stream; name=\"$filename\"");
         } else {
             // another application/octet-stream forces download for Netscape
             header ("Content-Type: application/octet-stream; name=\"$filename\"");
         }
     }

     //send the content-length header if the calling function provides it
     if ($filesize > 0) {
         header("Content-Length: $filesize");
     }

}  // end fn SendDownloadHeaders
?>
