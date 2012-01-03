<?php
/**
 * iframe.php 
 * JS integration via iFrame this method uses the Rest Cookie seup in login.php to authenticate with the server.
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011
 */

 
require_once('config.php');
$_PageTitle = 'Integration using iframes'; 

$currentUri = "/";
$parentUri = "/";

$myPath = (isset($_GET['action'])) ? urldecode($_GET['action']) : 'home';

if ($_GET['uri'] != '') {
 	$currentUri = $_GET['uri'];
}
   
$pos = strrpos($currentUri, "/");

if($pos === false || $pos == 0) {
	$parentUri="/";
} else {
	$parentUri = substr($currentUri, 0, $pos );
}

// Change this to fir yout needs
$iFrameServerURI = "http://localhost:8080/jasperserver-pro/";   

// This iframe uses the embed theme if the theme is not installed comment this line
$iFrameLoginInfo = "&theme=embed"; 

// Check if there is a JS session cookie set if not pass the user and pass in the iframe
// For the Session cookie to work JS and this app must be on the same domain 
if (!isset($_COOKIE['JSESSIONID'])) {
	$iFrameLoginInfo .= "&j_username=" . $_SESSION["username"] . "&j_password=" . $_SESSION["password"] ;
}

$myIframeSRC = '';
$myIframeheight = "818px";

//Initialize tabs
$tabArray =  array();

$tabArray['home'] = '<a href="iframe.php" class="active">JS Home</a>';
$tabArray['adHoc'] =  '<a href="iframe.php?action=adHoc" class="active">Ad Hoc Report</a>';
$tabArray['repository'] = '<a href="iframe.php?action=repository" class="active">Repository View</a>';
$tabArray['analisys'] =  '<a href="iframe.php?action=analisys" class="active">Analisys View</a>';
$tabArray[99] = '<a href="#" class="inactive">Logged as: ' . $_SESSION["username"] . '</a>';

// Decorate and set active tab
$_PageTabs = decoratePageTabs($tabArray, $myPath);

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
	default:
		$iFramePath = "/flow.html?_flowId=homeFlow";
		$tab['home'] = $tabon;
		break;
}


$myIframeSRC = ($myIframeSRC == '') ? '"' . $iFrameServerURI . $iFramePath . $iFrameLoginInfo . '"' : '"' . $myIframeSRC . '"';

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
<iframe src=<?php echo $myIframeSRC; ?> height="<?php echo $myIframeheight; ?>" width="100%" marginheight="0" frameborder="0" scrolling="no"></iframe>
   
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