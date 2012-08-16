JasperServer 4.5 REST Integration Sample
========================================

Author Mariano Luna

Requirements
------------

* PHP version 5.3.x
* JasperServer 4.5 or 4.7
* For SOAP API:  Pear SOAP client. 
* Embed theme installed on JasperServer (included in "./resources/JS-embed-theme")
* For RESTful API: PEST (included in this code) Rest Client (http://github.com/educoder/pest)
* built using Bluprint CSS (http://www.blueprintcss.org/)


Instalation Instructions
------------------------

Quick Overview
* Uncompress this folder into a path accessible to your Webserver
* Set the Webserver Document Root: "./web_root"
* Add "./resources/" to the include path of your PHP.ini
* Modify "./resources/config.php" to match your JS and App instalation paths and URLs

Instalation How-To:

Assuming that you have unzipped the file in:

<myfolder>/php-sample-app/

And inside that folder you have 2 folders:
- ./resources
- ./web-root

1) in httpd.com (apache configuration file) add this:
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

2) Modify the .htaccess file in web-root
- Edit the file called htaccess-sample that is in web-root there is only one line in this file should look like this:

php_value include_path "<myfolder>\php-sample-app\resources;<myPEARinstallfolder>;."

<myPEARinstallfolder> is your path to php-pear installation folder, this may be in a different path check your php.ini file

- SAVE this file as ".htaccess" note the . (dot) at the beginning. (IMPORTANT)

3) Restart apache

4) Change the paths inside ./resources/config.php
- Line 49: define('SITE_PATH', '/Library/WebServer/Documents/JSDemo/');
- Line 72: define('JS_WS_URL', 'http://localhost:8080/jasperserver-pro/rest/');  // if needed

5) Change the path to JRS in  iframe.php
- Line 47: $iFrameServerURI = "http://localhost:8080/jasperserver-pro/";

6) go to: http://localhost/myphpapp and Voila!

The RESTclient.php file has the imput control rendering funtions and  all the meat is in:
- login.php (authentication)
- home.php (repository browser)
- viewReport.php and executeReport.php (view and execute report)


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

