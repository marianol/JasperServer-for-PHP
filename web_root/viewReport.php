<?php
/**
 * viewReport.php Get and view a report unit
 * 
 *
 *
 * @author Mariano Luna
 * 
 * @copyright Copyright (c) 2011 Jaspersoft Corporation - All rights reserved. 
 Unless you have purchased a commercial license agreement from Jaspersoft,
 the following license terms apply:

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU Affero  General Public License for more details.

 You should have received a copy of the GNU Affero General Public  License
 along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once('config.php');

if($_SESSION['userlevel'] < USER) {
	// Guest, please login.
	header('Location: ' . WWW_ROOT . 'login.php');
	exit();
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sentControls = array();
    foreach ($_POST as $key => $value) {
        switch ($key) {
            case 'uri':
                $reportUnit = htmlentities($value);
                break;
            case 'name':
                $currentReportName = htmlentities($value);
                break;
            case 'format':
                $format = htmlentities($value);
                break;            
            default:
                $sentControls[$key][] = $value;
                break;
        }
    }
} else {
    // First call process the GET report URI
    $reportUnit = (isset($_GET['uri'])) ? htmlentities($_GET['uri']) : false;
    $currentReportName = '';
    $format = 'html';
}
$screen = '';
$_PageTitle = 'Report Viewer' ; 
$tabArray =  array();
$tabArray[99] = '<a href="#" class="active">Logged as: ' . $_SESSION["username"] . '</a>';
$_PageTabs = decoratePageTabs($tabArray, 99);

$client = new Jasper\JasperClient(
    JRS_HOST, // Hostname
    JRS_PORT, // Port
    $_SESSION['username'], // Username
    $_SESSION['password'], // Password
    JRS_BASE, // Base URL
    $_SESSION['org'] // Organization 
);

// Input Contols
$input_control_info = $client->getReportInputControlStructure($reportUnit);

$icRender = array();
$inputControlsDisplay = '';
$controls = array();
foreach($input_control_info as $ic) {
    // build html render  
    $overrideICs = isset($sentControls[$ic->getId()])? $sentControls[$ic->getId()] : array();
    switch ($ic->getType()) {
        case 'singleSelect':
            // render combo Box
            $icRender[$ic->getId()] =  makeComboArray($ic->getID(), $ic->inputOptions->getOptions(), $overrideICs );
            // set control defaults
            $defaultControls[$ic->getId()] = $ic->inputOptions->getSelected();           
            break;
        case 'multiSelect':
            // render combo Box
            $icRender[$ic->getId()] =  makeComboArray($ic->getID(), $ic->inputOptions->getOptions(), $overrideICs,'',' multiple ' );
            // set control defaults
            $defaultControls[$ic->getId()] = $ic->inputOptions->getSelected();           
            break;        
        default:
            // Render general Input box
            $inputType = ($ic->visible) ? 'text' : 'hidden';
            $icRender[$ic->getId()] = '<input type="'. $inputType . '" name="'. $ic->getID() . '" value="' . $ic->inputOptions->getValue() . '">';
            // set control defaults
            $defaultControls[$ic->getId()] = $ic->inputOptions->getValue();            
            break;
    }    
    $inputControlsDisplay .= '<br /><strong>' . $ic->getLabel(). '</strong>: ';
    $inputControlsDisplay .= ($ic->mandatory) ? '(*) ' : ' ';
    $inputControlsDisplay .= $icRender[$ic->getId()] ;
    $inputControlsDisplay .= ' - IC Type: ' . $ic->getType();
}   


// $controls = array(
   // 'Country_multi_select' => array('USA', 'Mexico'),
   // 'Cascading_state_multi_select' => array('CA', 'OR')
   // );
// Set report input control send submitted ones or default values
$controls = isset($sentControls)? $sentControls : $defaultControls;
// Get report
$report = $client->runReport($reportUnit, $format, null, $controls);

if ($format == 'html') {
    $screen .= $report;       
} else {
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename=report.' . $format );
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: ' . strlen($report));
    header('Content-Type: application/' . $format);
    
    echo $report;       
    die();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title><?php echo $_SiteConfig['site']['title'] ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="robots" content="noindex,nofollow">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/screen.css" type="text/css" media="screen, projection">
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/print.css" type="text/css" media="print"> 
  <!--[if lt IE 8]>
    <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/ie.css" type="text/css" media="screen, projection">
  <![endif]-->
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection" />  
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/plugins/tabs/screen.css" type="text/css" media="screen,projection">
  <link rel="stylesheet" href="<?php echo WWW_ROOT; ?>css/blueprint/plugins/buttons/screen.css" type="text/css" media="screen,projection">
  <link href="<?php echo WWW_ROOT; ?>css/dropdown/themes/default/helper.css" media="screen" rel="stylesheet" type="text/css" media="screen, projection" />
  <link href="<?php echo WWW_ROOT; ?>css/dropdown/dropdown.limited.css" media="screen" rel="stylesheet" type="text/css" />
  <link href="<?php echo WWW_ROOT; ?>css/dropdown/themes/default/default.css" media="screen" rel="stylesheet" type="text/css" />
  <!--[if lt IE 7]>
   <style type="text/css" media="screen">
   body { behavior:url("<?php echo WWW_ROOT; ?>js/csshover.htc"); }
  </style>
  <![endif]-->
  <link href="<?php echo WWW_ROOT; ?>css/style.css" media="screen, projection"  rel="stylesheet" type="text/css" />

</head>
<body >
	<div class="container">
		<div id="header" class="span-24 last">
			<h1><a href="<?php echo WWW_ROOT; ?>" title="Home"><?php echo $_SiteConfig['site']['name'] ?></a></h1>								
		</div> 
        <div id="subheader" class="span-24 last">
          <h3 class="alt"><?php echo $_PageTitle ?></h3>
        </div>
		<div id="mainmenu" class="span-24 last">
			<ul id="nav" class="dropdown dropdown-horizontal">	
	    	<?php echo $_SiteConfig['user_menu'] ?>
	    	</ul>			
		</div> 
		<div id="maincontent" class="span-24 last"> 
			<ul class="tabs">
			<?php echo $_PageTabs; ?>
			</ul> 
			<h3><?php echo $currentReportName; ?></h3>
			<form action="viewReport.php" method="POST">
     			<input type="hidden" name="uri" value="<?php echo $reportUnit ?>">
     			<input type="hidden" name="name" value="<?php echo $currentReportName ?>">
			     Export format: <select name="format">
			         
			         <option value="html">HTML</option>
			         <option value="pdf">PDF</option>
			         <option value="xls">XLS</option>
			         <option value="swf">SWF</option>
			     </select>
			     <?php echo $inputControlsDisplay; ?>
			    <br />			     
			     <input type="submit" value="Run the report">
   			</form>
   			<hr />
   			<?php echo $screen; ?>
		</div>
		<div id="footer" class="span-16"> 
			<!-- Footer Links -->
		</div> 
		<div class="alt span-7 last"><p>&nbsp;</p>
			<a href="http://www.jaspersoft.com">My Reporting Appication</a>
		</div>
</div>
    </body>
</html>
