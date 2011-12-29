<?php
/**
 * iframe.php 
 * JS integration via iFrame
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011
 */

 
require_once('config.php');
$_PageTitle = 'Integration using iframes'; 

$currentUri = "/";
$parentUri = "/";

$myPath = (isset($_GET['action'])) ? urldecode($_GET['action']) : 'none';

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
$iFrameLoginInfo = "&theme=embed&j_username=" . $_SESSION["username"] . "&j_password=" . $_SESSION["password"] ;
$myIframeSRC = '';
$myIframeheight = "818px";

//Initialize tabs
$tabon = 'ontab';
$tab['home'] = "offtab";
$tab['repository'] = "offtab";
$tab['analisys'] = "offtab";
$tab['adHoc'] = "offtab";
$tab['section7'] = "offtab";
$tab['section8'] = "offtab";
$tab['section9'] = "offtab";
$tab['section10'] = "offtab";
$tab['section11'] = "offtab";
//set active tab
$tab[$myPath] = $tabon;
switch ($myPath) {
	case 'adHoc':
		$iFramePath = "flow.html?_flowId=adhocFlow&userLocale=en_US";
		break;
	case 'repository':
		$iFramePath = "flow.html?_flowId=searchFlow&userLocale=en_US";
		break;	
	case 'section7':
		$iFramePath = "flow.html?_flowId=viewReportFlow&_flowId=viewReportFlow&reportUnit=/reports/Balance_Sheet_1&userLocale=en_US";
		// 
		break;	
	case 'section8': //open ad hoc crosstab
		$iFramePath = "flow.html?_flowId=adhocFlow&_eventId=initForExistingReport&resource=/reports/Segmentation_Ad_Hoc&userLocale=en_US";  
		// or
		// $myIframeSRC = "http://www.google.com";
		break;
	case 'section9':
		$iFramePath = "flow.html?_flowId=viewReportFlow&standAlone=true&_flowId=viewReportFlow&reportUnit=/reports/FormattedReport_1";
		//flow.html?_flowId=adhocFlow&_eventId=initForExistingReport&resource=/public/Adhoc_Crosstab";
		break;			
	case 'section10': // actually section 13 - run in Spanish
		$iFramePath = "flow.html?_flowId=homeFlow&userLocale=es";
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