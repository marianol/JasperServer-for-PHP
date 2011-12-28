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
?>