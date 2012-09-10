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


 Copyright (C) 2005 - 2012 Jaspersoft Corporation. All rights reserved.
 http://www.jaspersoft.com.

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

