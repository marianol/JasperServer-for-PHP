<?php
/**
 * login.php 
 * Show the login form and authenticate the user against JasperServer
 * I also use the JS Session ID sent from the REST Auth to set a cookie for the iframe integration
 * 
 * 
 * @author Mariano Luna
 * 
 * @copyright Copyright (c) 2012 Jaspersoft Corporation - All rights reserved. 
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

// http://http//localhost/myphpapp/accountedit.php?account_id=a7df6541-fb86-851e-c556-438dfbffa507
require_once('config.php');

if($_SESSION['userlevel'] < USER) {
    // Guest, please login.
    header('Location: ' . WWW_ROOT . 'login.php');
    exit();
} 
$dbconn = pg_connect("host=localhost port=5432 dbname=sugarcrm user=postgres password=jasper*mariano");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Edit account and redirect
      // var_dump($_POST);
      /*
       * array(5) { ["account_id"]=> string(36) "a7df6541-fb86-851e-c556-438dfbffa507" 
       * ["origURL"]=> string(0) "" 
       * ["name"]=> string(33) "Orona-Warmack Transportation, Inc" 
       * ["phone_office"]=> string(12) "259-555-5742" 
       * ["billing_address_city"]=> string(7) "Burnaby" }
      */
      $account_id = $_POST['account_id'] ;
      $updateq = "UPDATE accounts SET 
      name = '" . $_POST['name'] . "' ,
      phone_office = '" . $_POST['phone_office'] . "' ,
      billing_address_city = '" . $_POST['billing_address_city'] . "' ,
      billing_address_street = '" . $_POST['billing_address_street'] . "' ,
      billing_address_country = '" . $_POST['billing_address_country'] . "'
      where id = '" . $_POST['account_id'] . "'";
      $result = pg_exec($dbconn, $updateq);
      header("location: iframe.php?action=report2" ); // . $_POST['origURL']
      exit();
} else {
    $account_id = (isset($_GET['account_id'])) ? htmlentities($_GET['account_id']) : '/';
    $origURL = $_SERVER["HTTP_REFERER"];
}
$result = pg_exec($dbconn, "select * from accounts where id = '" . $account_id . "'");
$numrows = pg_numrows($result);

$accountrow = pg_fetch_array($result, 0);

$_PageTitle = 'Edit Account' ; 
$tabArray =  array();


$screen = '';
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
            <?php echo $screen; ?>
                <form id="dummy" action="" method="post">

          <fieldset>
            <legend>Editing Account ID: <?php echo $account_id ; ?></legend>
            <input type="hidden" name="account_id" id="account_id" value="<?php echo $account_id ; ?>">
            
            <input type="hidden" name="origURL" id="origURL" value="<?php echo $origURL ; ?>">
            <p>
              <label for="dummy0">Account Name</label><br>
              <input type="text" class="title" name="name" id="dummy0" value="<?php echo $accountrow['name'] ; ?>">
            </p>

            <p>
              <label for="dummy1">Phone</label><br>
              <input type="text" class="text" id="dummy1" name="phone_office" value="<?php echo $accountrow['phone_office'] ; ?>">
            </p>

             <p>
              <label for="dummy2">Street</label><br>
             <input type="text" class="text" id="dummy2" name="billing_address_street" value="<?php echo $accountrow['billing_address_street'] ; ?>">

            </p>
             <p>
              <label for="dummy3">City</label><br>
             <input type="text" class="text" id="dummy3" name="billing_address_city" value="<?php echo $accountrow['billing_address_city'] ; ?>">

            </p>
            <p>
              <label for="dummy4">Country</label><br>
             <input type="text" class="text" id="dummy4" name="billing_address_country" value="<?php echo $accountrow['billing_address_country'] ; ?>">

            </p>


            <p>
              <input type="submit" value="Submit">
              <input type="reset" value="Reset">
            </p>

          </fieldset>
        </form>
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
