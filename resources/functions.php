<?php

function decorateError($errorText, $errorType = '') {
    return '<div class="error">' . $errorText . '</div>';
}

function decoratePageTabs($tabArray, $selectedTab = -1) {
    $output = '';
    foreach ($tabArray as $item => $legend) {
        $selected = ($selectedTab == $item) ? 'selected' : '';
        $output .= '<li class="' . $selected . '">' . $legend . '</li>' . "\n";

    }
    return $output;
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
function makeSelectArray($ComboName, $SelectedValue, $array, $css = "", $atributo = "", $emptyitem = false, $selectall = false)
{
    
    $combo_box = "";
    $combo_box .= '<select name="'.$ComboName.'" class="'.$css.'" '.$atributo.' >'."\n";
    
    if ($selectall) {   
        $combo_box .= '<option value="_ALL_">ALL</option>'."\n";
    } 
    if ($emptyitem) {
        $combo_box .= '<option value=""></option>'."\n";
    }

    foreach ($array as $id => $optionvalue) {
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

function fix_session_register() {
    function session_register() {
        $args = func_get_args();
        foreach ($args as $key) {
            $_SESSION[$key]=$GLOBALS[$key];
        }
    }
    function session_is_registered($key) {
        return isset($_SESSION[$key]);
    }
    function session_unregister($key) {
        unset($_SESSION[$key]);
    }
}
if (!function_exists('session_register')) fix_session_register();
?>
