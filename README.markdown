JasperServer 4.5 IFRAME Integration Sample
========================================

Requirements
------------

* PHP version 5.3.x
* JasperServer 4.5
* RESTful Wrapper (included): PEST Rest Client (http://github.com/educoder/pest)
* Embed theme installed on JasperServer (included in "./web_root/JS-embed-theme")

Instalation Instructions
------------------------

Instalation:
* Uncompress this folder into a path accessible to your Webserver
* Set the Webserver Document Root: "./web_root"
* Modify "./web_root/config.php" to match your JS and App instalation paths and URLs

Instalation How-To on APache web server:

Assuming that you have unzipped the file in:

<myfolder>/php-sample-app/

And inside that folder you have 2 folders:
- ./web-root
- README.markdown

1) in httpd.conf (apache configuration file) add this:

---
# Alias for PHP APP demo
Alias /myphpapp "<myfolder>\php-sample-app\web_root\"
<Directory "<myfolder>\php-sample-app\web_root\">
    Options Indexes  FollowSymLinks MultiViews ExecCGI
    AllowOverride All
    Order allow,deny
    Allow from all
</Directory>
---

2) Restart apache

3) Change the paths inside ./web_app/config.php
- Line 40: define('SITE_PATH', '/Library/WebServer/Documents/JSDemo/');
- Line 51: define('JS_WS_URL', 'http://localhost:8080/jasperserver-pro/rest/');  // if needed
- Line 52: define('iFrame_JS_URI', 'http://localhost:8080/jasperserver-pro/');   // if needed

4) go to: http://localhost/myphpapp and Voila!

Scipts Information
- login.php (authentication) 
	This files uses REST to authenticate to JRS and places the JSESSION ID cookie 
	to allow the iframe to skip the login screen
- iframe.php (iframe integration)
	Here the JRS UI is integrated to the App via an Iframe, the script modifies the 
	iframe SRC as neede to render different views


LICENSE AND COPYRIGHT NOTIFICATION
==================================

The Proof of Concept deliverable(s) are (c) 2011 Jaspersoft Corporation - All rights reserved. 
Jaspersoft grants to you a non-exclusive, non-transferable, royalty-free license to use the deliverable(s) pursuant to 
the applicable evaluation license agreement (or, if you later purchase a subscription, the applicable subscription 
agreement) relating to the Jaspersoft software product at issue. 

The Jaspersoft Sales department provides the Proof of Concept deliverable(s) "AS IS" and WITHOUT A WARRANTY OF ANY KIND. 
It is not covered by any Jaspersoft Support agreement or included in any Professional Services offering. 
At the discretion of the head of the Jaspersoft Professional Services team, support, maintenance and enhancements may be 
available for such deliverable(s) as "Time for Hire": http://www.jaspersoft.com/time-for-hire.

