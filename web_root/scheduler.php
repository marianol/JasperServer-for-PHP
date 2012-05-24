<?php
/**
 * scheduler.php REST Scheduler V2
 * show the menu and options
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

$_PageTitle = 'Report Scheduler'; //'Welcome ' . $_SESSION["username"] ; 
$tabArray =  array();
$tabArray['repository'] = '<a href="home.php" class="active">Repository Browser</a>';
$tabArray['scheduler'] = '<a href="scheduler.php" class="active">Scheduler</a>';
$tabArray[99] = '<a href="#" class="active">Logged as: ' . $_SESSION["username"] . '</a>';
$_PageTabs = decoratePageTabs($tabArray, 99);

$root = (isset($_GET['root'])) ? htmlentities($_GET['root']) : '/';
$WSRest = new PestXML('http://50.19.151.95/jasperserver-pro/rest_v2/'); //JS_WS_URL);
// Set auth Header
// @todo
//$WSRest->curl_opts[CURLOPT_COOKIE] = $_SESSION["JSCookie"] ;
$WSRest->setupBasicAuth('jasperadmin', 'jasperadmin');
//$WSRest->curl_opts[CURLOPT_HTTPHEADER] = 'Authorization: Basic amFzcGVyYWRtaW46amFzcGVyYWRtaW4=';

$sortOptions = array(
 'NONE' => '', 
 'SORTBY_JOBID' => 'Job ID', 
 'SORTBY_JOBNAME' => 'Job Name', 
 'SORTBY_OWNER' => 'Owner', 
 'SORTBY_REPORTURI' => 'Report URI', 
 'SORTBY_REPORTNAME' => 'Report Name',
 'SORTBY_REPORTFOLDER' => 'Report Folder', 
 'SORTBY_STATUS' => 'Status', 
 'SORTBY_LASTRUN' => 'Last Run', 
 'SORTBY_NEXTRUN' => 'Next Run'
);

$selectedSort = (isset($_GET['sort'])) ? htmlentities($_GET['sort']) : '';

echo $parameters = ($selectedSort != '') ? '?sortType=' . $selectedSort : '';

try 
{		    
	$jobs = $WSRest->get('jobs' , $selectedSort);
	/*
	 * 
<jobs>
    <jobsummary>
        <id>4746</id>
        <label>Test Schedule</label>
        <reportUnitURI>/organizations/organization_1/reports/Mariano/Table_Report_Mariano</reportUnitURI>
        <owner>jasperadmin|organization_1</owner>
        <version>0</version>
    </jobsummary>
    <jobsummary>
        <id>4783</id>
        <label>New Meeting</label>
        <reportUnitURI>/organizations/organization_1/reports/Mariano/New_Report</reportUnitURI>
        <owner>jasperadmin|organization_1</owner>
        <version>0</version>
    </jobsummary>
</jobs>
	 */
	$screen .= '<table>';
	$screen .= '<tr><th>Job ID</th><th>Name</th><th>Created By</th><th>Report URI</th></tr>';
	foreach ($jobs->jobsummary as $contents) {
			$jobID = $contents->id;
			$screen .= '<tr>';
			$screen .= '<td>' . $contents->id . '</td>';
			$screen .= '<td><a href="schedulerJob.php?jobid=' . $jobID . '" >' . $contents->label . '</a></td>';
			$screen .= '<td>'  . $contents->owner . '</td>';
			$screen .= '<td>'  . $contents->reportUnitURI . '</td>';
			$screen .= '</tr>';
	}
	$screen .= '</table>';
} 
catch (Pest_Unauthorized $e) 
{
	// Check for a 401 (login timed out)	
	echo "Exception: Auth Failed";
	$screen .= "\n" . print_r($WSRest->last_response, true);
}
catch (Exception $e) 
{
    $screen .=  "Other Exception: <pre>" .  $e->getMessage() . "</pre>";
	$screen .= "\n" . print_r($WSRest->last_response, true);
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
    		<h3>Scheduled Reports</h3>
			<h5><?php echo $currentPath; ?></h5>
			<form action="scheduler.php" method="GET">
              <?php echo makeSelectArray('sort', $selectedSort, $sortOptions); ?>
              <input type="submit" />
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
