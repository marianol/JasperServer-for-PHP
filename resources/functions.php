<?php

// Decorators
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

/**
 * Creates a combo box from an array with the format send by the jasper API v2
 * array(n) ( 
 * 1 => array(3) {
 *   ["label"]=>
 *   string(3) "jim"
 *   ["value"]=>  string(6) "jim_id"
 *   ["selected"]=>  string(4) "true"
 * } 
 * ...
 * );
 *
 * @param string $ComboName
 * @param array $ComboElements
 * @param string $cssClass
 * @param string $tagAttributes
 * @return string
 */
function makeComboArray($ComboName, $ComboElements, $overrideDefaultSelection = array(), $cssClass = "", $tagAttributes = "" )
{
    
    $combo_box = "";
    $combo_box .= '<select name="'.$ComboName.'" class="'.$cssClass.'" '.$tagAttributes.' >'."\n";
    $overrideSelected = !empty($overrideDefaultSelection);
    foreach ($ComboElements as $item => $valuesArray) {
        if ($overrideSelected) {
            $selection = (in_array($valuesArray['value'], $overrideDefaultSelection)) ? ' SELECTED ' : '';
        } else {
            // Use default selections
            $selection = ($valuesArray['selected'] == 'true') ? ' SELECTED ' : '';
        }
        
        $combo_box .= '<option value="'. $valuesArray['value'] .'" '. $selection .'>'. $valuesArray['label']  .'</option>'."\n";
    }
    $combo_box .= "</select>\n";

    return $combo_box;
}

?>
