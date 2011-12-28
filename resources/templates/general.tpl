<!-- START TEMPLATE HEADER -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>{SITE_TITLE}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="robots" content="noindex,nofollow">
<meta name="Description" content="{META_DESC}">
<meta name="keywords" content="{META_KEYWORDS}">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
  <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print"> 
  <!--[if lt IE 8]>
    <link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection">
  <![endif]-->
  <link rel="stylesheet" href="/css/blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection" />  
  <link rel="stylesheet" href="/css/blueprint/plugins/tabs/screen.css" type="text/css" media="screen,projection">
  <link rel="stylesheet" href="/css/blueprint/plugins/buttons/screen.css" type="text/css" media="screen,projection">
  <link href="/css/dropdown/themes/default/helper.css" media="screen" rel="stylesheet" type="text/css" media="screen, projection" />
  <link href="/css/dropdown/dropdown.limited.css" media="screen" rel="stylesheet" type="text/css" />
  <link href="/css/dropdown/themes/default/default.css" media="screen" rel="stylesheet" type="text/css" />
  <!--[if lt IE 7]>
   <style type="text/css" media="screen">
   body { behavior:url("/js/csshover.htc"); }
  </style>
  <![endif]-->
  <link href="/css/style.css" media="screen, projection"  rel="stylesheet" type="text/css" />
{PAGE_HEADER}
</head>
<body {BODY_TAG}>
<div class="container">
		<div id="header" class="span-24 last">
			<h1><a href="/" title="Home">{SITE_TITLE}</a></h1>								
		</div> 
        <div id="subheader" class="span-24 last">
          <h3 class="alt">{PAGE_TITLE}</h3>
        </div>
<!-- END TEMPLATE HEADER -->
<!-- START TEMPLATE MAIN -->
<div id="mainmenu" class="span-24 last">
<ul id="nav" class="dropdown dropdown-horizontal">	
    {MAIN_MENU}
	
<!-- START TEMPLATE NO_MENU -->
<!-- END TEMPLATE NO_MENU -->
<!-- START TEMPLATE PUBLICMENU -->
	<li ><a href="{WWW_ROOT}">Home</a></li> 
	<li ><a href="{WWW_ROOT}public/about.php">About</a></li> 
	<li ><a href="{WWW_ROOT}public/login.php">Log In</a></li> 

<!-- END TEMPLATE PUBLICMENU -->	
<!-- START TEMPLATE ADMINMENU -->

	<li ><a href="{WWW_ROOT}">Home</a></li>
	<li ><a href="{WWW_ROOT}admin/index.php">Admin</a></li> 
	<li ><a href="{WWW_ROOT}public/about.php">About</a></li> 
	<li ><a href="{WWW_ROOT}user/my_account.php">My Account</a></li> 
	<li ><a href="{WWW_ROOT}public/logout.php">Log out</a></li> 

<!-- END TEMPLATE ADMINMENU -->		
<!-- START TEMPLATE USERMENU -->

	<li ><a href="{WWW_ROOT}">Home</a></li>
	<li ><a href="{WWW_ROOT}user/index.php">User</a></li> 
	<li ><a href="{WWW_ROOT}public/about.php">About</a></li> 
	<li ><a href="{WWW_ROOT}user/my_account.php">My Account</a></li> 
	<li ><a href="{WWW_ROOT}public/logout.php">Log out</a></li> 
		
<!-- END TEMPLATE USERMENU -->	
</ul>			
</div> 

<div id="maincontent" class="span-24 last"> 

<ul class="tabs">
	<li class="selected" ><a href="{WWW_ROOT}user/settings_show.php" class="active">Logged as: {MY_USERNAME}</a></li> 
	{TABS_MENU}
</ul> 

{CONTENT}
<!-- START TEMPLATE EMPTY -->
{PAGE_CONTENT}
<!-- END TEMPLATE EMPTY -->
</div>
<!-- END TEMPLATE MAIN -->
<!-- START TEMPLATE FOOTER -->
<hr />
<!-- /footer --> 
<div id="footer" class="span-16"> 
{QUICK_LINKS}
</div> 
<div class="alt span-7 last">
&copy; <a href="http://www.etszone.com">ETSZONE Web Application Services</a>
</div>
</div>
</body>
</html>
<!-- END TEMPLATE FOOTER -->

<!-- START TEMPLATE CLEANHEADER -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>{SITE_TITLE}</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta name="robots" content="noindex,nofollow">
<style type="text/css">
form {
  margin: 0;
  padding: 0;
}
fieldset {
  margin-bottom: 1em;
  padding: .5em;
}
img {
  border: 0;
}
table {
  border-collapse: collapse;
}
body, td, p {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  font-size:10px; /* Sets default font size to 10px */
  color:#222222;
}
</style>
{PAGE_HEADER}
</head>
<body {BODY_TAG}>
<!-- END TEMPLATE CLEANHEADER -->
<!-- START TEMPLATE CLEANFOOTER -->
</body>
</html>
<!-- END TEMPLATE CLEANFOOTER -->