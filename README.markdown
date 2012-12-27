JasperServer REST Integration Sample
========================================

https://github.com/marianol/JasperServer-for-PHP/

Author Mariano Luna

Version 1.1

Requirements
------------

* PHP version 5.3.x
* JasperServer 4.5 or > 4.7 installed in the same domian
* The PEAR package manager (http://pear.php.net)
  * XML_Serializer PEAR package (http://pear.php.net/manual/en/package.xml.xml-serializer.php) 
* Embed theme installed on JasperServer (included in "./resources/JS-embed-theme") for Jasper Server version 4.5 use embed-4.5.zip and for version 4.7 or 5.0 use embed-4.7.zip 

Usage
-----

* For samples using the REST API check the "Web Services Integration" tab
* For samples using iFrames and full UI integration check the "Jasper UI Integration" tab

Instalation Instructions
------------------------

Quick Overview

* Uncompress this folder into a path accessible to your Webserver
* Set the Webserver Document Root: "./web_root"
* Add "./resources/" to the include path of your PHP.ini
* Modify "./resources/config.php" to match your JS and App instalation paths and URLs
* For the SSO integration implemented in this sample to work, Jasper and this sample must be in the same domain 

### Instalation Step by Step How-To

Assuming that you have unzipped the file in:

	<myfolder>/php-sample-app/

And inside that folder you have 2 folders:

	./resources
	./web-root

1) in httpd.com (apache configuration file) add this:

	...
	# Alias for PHP APP demo
	Alias /myphpapp "<myfolder>\php-sample-app\web_root\"
	<Directory "<myfolder>\php-sample-app\web_root\">
	    Options Indexes  FollowSymLinks MultiViews ExecCGI
	    AllowOverride All
	    Order allow,deny
	    Allow from all
	</Directory>
	...

2) Modify the .htaccess file in web-root

- Edit the file called htaccess-sample that is in web-root there is only one line in this file should look like this:

	php_value include_path "<myfolder>\php-sample-app\resources;_myPEARinstallfolder_;."

_myPEARinstallfolder_ is your path to php-pear installation folder, this may be in a different path check your php.ini file

- SAVE this file as ".htaccess"  (IMPORTANT! : note the . (dot) at the beginning of the file name.)

3) Restart apache


4) Change the paths inside ./resources/config.php

	- Line 49: define('SITE_PATH', '/Library/WebServer/Documents/JSDemo/');
	- Line 72: define('JS_WS_URL', 'http://localhost:8080/jasperserver-pro/rest/');  // if needed

5) Change the path to JRS in  iframe.php

	- Line 47: $iFrameServerURI = "http://localhost:8080/jasperserver-pro/";

6) go to: http://localhost/myphpapp and Voila!


The RESTclient.php file has the input control rendering funtions and all the meat is in:

	- login.php (authentication)
	- home.php (repository browser)
	- viewReport.php and executeReport.php (view and execute report)

External Libraries Used
-----------------------

All the external libraries are included in this sample, check each library folder or website for each individual license.

* JasperSoft PHP Client (http://community.jaspersoft.com/project/php-client) /resources/jasper-rest
* PEST Rest Client (http://github.com/educoder/pest) /resources/PEST
* PHP Markdown (http://michelf.ca/projects/php-markdown/) /resources/php-markdown-extra-1.2.5
* Bluprint CSS (http://www.blueprintcss.org/) /web_root/css/blueprint

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

