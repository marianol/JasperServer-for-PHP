<?php
/**
 * iframe.php 
 * JS integration via iFrame this method uses the Rest Cookie seup in login.php to authenticate with the server.
 *
 *
 * @copyright Copyright (c) 2011 - 2012
 * @author Mariano Luna
 * 
 *  Unless you have purchased a commercial license agreement from Jaspersoft,
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
$_PageTitle = 'JRS UI Integration'; 

$default_tab = 'library'; // set the default tab

// Which tab?
$myPath = (isset($_GET['action'])) ? urldecode($_GET['action']) : $default_tab;

// Set the Height of the iFrame in px
$iFrameHeight = "600px";

// This iframe uses the embed theme (see README.markdown) to cleanup the Jasper UI for embedding
$iFrameAttributes = "&theme=embed"; 

// Check if there is a JS session cookie set if not pass the user and pass in the iframe
// For the Session cookie to work JS and this app must be on the same domain 
/*
 * This sample uses the REST login cookie to authenticate against JRS (see login.php) so there
 * is no need to pass credentials to Jasper Server.
 * For this to work this Sample and Jasper Server must be installed in the same domain (e.g. localhost or mydomain.com)
 * If that is not the case, you will need to implement an SSO between this application and JasperServer
 * you can also pass the Login credentials in the URL to test this functionality (just uncomment the following line) 
 */
 
//	$iFrameAttributes .= "&j_username=" . $_SESSION["username"] . "&j_password=" . $_SESSION["password"] ;

//Initialize tabs
$tabArray =  array();

$tabArray['home'] = '<a href="iframe.php" class="active">Home</a>';
$tabArray['library'] = '<a href="iframe.php?action=library" class="active">Report Library</a>';
$tabArray['dashboard'] = '<a href="iframe.php?action=dashboard" class="active">Dashboard</a>';
$tabArray['report'] =  '<a href="iframe.php?action=report" class="active">Foodmart Report</a>';
$tabArray['adHoc'] =  '<a href="iframe.php?action=adHoc" class="active">Create Ad Hoc Report</a>';
$tabArray['repository'] = '<a href="iframe.php?action=repository" class="active">Repository View</a>';
$tabArray['analisys'] =  '<a href="iframe.php?action=analisys" class="active">Analisys View</a>';
$tabArray[99] = '<a href="#" class="inactive">Logged as: ' . $_SESSION["username"] . '</a>';


switch ($myPath) {
	case 'adHoc':
		$iFramePath = "flow.html?_flowId=adhocFlow&userLocale=en_US";
		break;
	case 'repository':
		$iFramePath = "flow.html?_flowId=searchFlow&userLocale=en_US";
		break;	
	case 'section7':
		$iFramePath = "flow.html?_flowId=viewReportFlow&_flowId=viewReportFlow&reportUnit=/reports/Balance_Sheet_1&userLocale=en_US";
		// View Specific report
		break;	
	case 'analisys': //open an olap view
		$iFramePath = "olap/viewOlap.html?new=true&parentFlow=searchFlow&name=%2Fsupermart%2FrevenueAndProfit%2FProfitView&ParentFolderUri=%2Fsupermart%2FrevenueAndProfit";  
		break;
    case 'report':
        $iFramePath = "flow.html?_flowId=viewReportFlow&standAlone=true&_flowId=viewReportFlow&ParentFolderUri=%2Freports%2Finteractive&reportUnit=%2Freports%2Finteractive%2FCascading_Report_2_Updated";
    break;
	case 'dashboard':
		$iFramePath = "flow.html?_flowId=dashboardRuntimeFlow&dashboardResource=%2Fpublic%2FSamples%2FDashboards%2FSupermartDashboard";
	break;
    case 'library':
        // Use Library
        $iFramePath = "/flow.html?_flowId=searchFlow&mode=library";
    break;
	default:
		$iFramePath = "/flow.html?_flowId=homeFlow";

		$myPath = 'home';
		break;
}
// Decorate and set active tab
$_PageTabs = decoratePageTabs($tabArray, $myPath);

$iFrameSRC = '"' . JS_IFRAME_URL . $iFramePath . $iFrameAttributes . '"'; // Build the iFrame SRC URL

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
<!-- JasperServer Embed -->
<iframe src=<?php echo $iFrameSRC; ?> height="<?php echo $iFrameHeight; ?>" width="100%" marginheight="0" frameborder="0" scrolling="no"></iframe>
   
		</div>
		<div id="footer" class="span-16"> 
			<!-- Footer Links -->
		</div> 
		<div class="alt span-7 last">
			<a href="http://www.jaspersoft.com">Jaspersoft.com</a>
		</div>
</div>
    </body>
</html>             