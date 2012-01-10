<?php
/**
 * viewReport.php Get and view a report unit
 * 
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011
 */

require_once('config.php');

if($_SESSION['userlevel'] < USER) {
	// Guest, please login.
	header('Location: ' . WWW_ROOT . 'login.php');
	exit();
} 

$reportUnit = (isset($_GET['uri'])) ? htmlentities($_GET['uri']) : false;

$_PageTitle = 'Welcome ' . $_SESSION["username"] ; 
$tabArray =  array();
$tabArray[99] = '<a href="#" class="active">Logged as: ' . $_SESSION["username"] . '</a>';
$_PageTabs = decoratePageTabs($tabArray, 99);


$WSRest = new PestXML(JS_WS_URL);
// Set auth Header
$WSRest->curl_opts[CURLOPT_COOKIE] = $_SESSION["JSCookie"] ;

try 
{		    
	$resource = $WSRest->get('resource' . $reportUnit);
	//$response = $pest->post('login', $restData);
	
	//$screen .= "\n" . print_r($WSRest->last_response, true);
	// Get the Datasource
	//$resource[resourceDescriptor][wsType]
	$currentReport = $reportName = $resource['name'];
	foreach ($resource->resourceDescriptor as $contents) {
		switch ($contents['wsType']) {
			case 'datasource':
				// Get the Datasource to be able to query the input controls if any.
				$dsUri = $contents->resourceProperty->value;
				//$screen .= "<hr> <strong>DS URI: </strong>" . $dsUri . "";
			break;	
			case 'inputControl':
				// Render Input Control
				$screen .= RenderInputControl($contents, $dsUri);
			break;
			default:
				//$screen .= "<hr><pre>" . htmlentities(print_r($contents, true)) . "</pre>";	
		}
	}
	$screen .= "<div style='display:none'> <hr> DEBUG - Full Response: <pre>" . htmlentities(print_r($resource->asXML(), true)) . "</pre> </div>";
} 
catch (Pest_Unauthorized $e) {
	// Check for a 401 (login timed out)	
	$WSRest->curl_opts[CURLOPT_HEADER] = true;
	$restData = array(
	  'j_username' => $_SESSION['username'],
	  'j_password' => $_SESSION['password']
	);
	
    try {		    
		$body = $WSRest->post('login', $restData);
		$response = $WSRest->last_response;
		if ($response['meta']['http_code'] == '200') {
			// Respose code 200 -> All OK
			// Extract the Cookie for further requests.
			preg_match('/^Set-Cookie: (.*?)$/sm', $body, $cookie);
			//Cookie: $Version=0; JSESSIONID=52E79BCEE51381DF32637EC69AD698AE; $Path=/jasperserver
			$_SESSION["JSCookie"] = '$Version=0; ' . str_replace('Path', '$Path', $cookie[1]);
			// Reload this page.
	        header("location: home.php");
	        exit();
		} else {
			header("location: logout.php");
			exit();
		}
	} 
	catch (Exception $e) {
	   	header("location: logout.php");
		exit();
	}
}
catch (Exception $e) 
{
    $screen .=  "Exception: <pre>" .  $e->getMessage() . "</pre>";
}
//$currentReport = '';
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
			<h3><?php echo $currentReport; ?></h3>
			<form action="executeReport.php" method="POST">
     			<input type="hidden" name="uri" value="<?php echo $reportUnit ?>">
     			<input type="hidden" name="name" value="<?php echo $currentReport ?>">
			     Export format: <select name="format">
			         <option value="PDF">PDF</option>
			         <option value="HTML">HTML</option>
			         
			         <option value="XLS">XLS</option>
			     </select>
			     
			     
			     
			     <input type="submit" value="Run the report">
		     </form>
			<?php echo $screen; ?>
   
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
