<?php
/**
 * repository.php List JapserServer Repository
 * show the menu and options
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011
 * 
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

$_PageTitle = 'Welcome ' . $_SESSION["username"] ; 
$tabArray =  array();
$tabArray['repository'] = '<a href="repository.php" class="active">Repository Browser</a>';
$tabArray['scheduler'] = '<a href="scheduler.php" class="active">Scheduler</a>';
$tabArray[99] = '<a href="#" class="active">Logged as: ' . $_SESSION["username"] . '</a>';
$_PageTabs = decoratePageTabs($tabArray, 99);

$root = (isset($_GET['root'])) ? htmlentities($_GET['root']) : '/';


$client = new Jasper\JasperClient(
    JRS_HOST, // Hostname
    JRS_PORT, // Port
    $_SESSION['username'], // Username
    $_SESSION['password'], // Password
    JRS_BASE, // Base URL
    $_SESSION['org'] // Organization 
);

// get the repository contents
$repository = $client->getRepository($root); 

// Set the path breadcrumns
foreach (explode('/', $root) as $key => $items) {
	$tempArray[] = $items;
	if ($item == '' and $key == 0) {
		$currentPathArray[] = '<a href="repository.php">Repository</a>';
	} else {
		$currentPathArray[] = '<a href="repository.php?root=' . implode("/", $tempArray) . '">' . ucfirst($items) . '</a>';
	}
}
$currentPath = implode(" &raquo; ", $currentPathArray);

// list contents
foreach ($repository as $resourceDescriptor) {
        // select proper icon and link for each resource type
		switch ( $resourceDescriptor->getWsType() ) {
			case 'folder':
				$screen .= '<li> <img src="'. WWW_ROOT .'images/icon-folder.png" align="absmiddle" >
				    <a href="repository.php?root=' . $resourceDescriptor->getUriString() . '" 
				    title="' .  addslashes($resourceDescriptor) . '">' . $resourceDescriptor->getLabel() . '</a></li>';
			break;
			case 'reportUnit';
				$screen .= '<li> <img src="'. WWW_ROOT .'images/icon-edit.gif" align="absmiddle" >
				    <a href="viewReport.php?uri=' . $resourceDescriptor->getUriString()  . '" 
				    title="' . addslashes($resourceDescriptor) . '">' . $resourceDescriptor->getLabel() . '</a></li>';
				    $report_options = $client->getReportOptions($resourceDescriptor->getUriString());
                            
                        foreach($report_options as $ro) {
                           // echo $ro->getLabel() . "<br />";
                        }   
				    
			break;
			default:
				$screen .= '<li>' . $resourceDescriptor->getLabel() . ' (' . $resourceDescriptor->getWsType() . ')</li>';
		}
	    
}
$screen .= '</ul>';
 


//$screen .= htmlentities(print_r($resources, true));
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
    		<h3>Welcome to your Report Repository</h3>
			<h5><?php echo $currentPath; ?></h5>
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
