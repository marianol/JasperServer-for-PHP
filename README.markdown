JasperServer REST Integration Sample
========================================


Code: http://github.com/marianol/JasperServer-for-PHP/

Author: Mariano Luna

Version: 2.0 beta

Requirements
------------

* PHP version 5.3.x
* JasperServer 4.5 or better installed in the same domian. (Tested with v 5.0.1)
* The PEAR package manager (http://pear.php.net)
  * XML_Serializer PEAR package (http://pear.php.net/manual/en/package.xml.xml-serializer.php) 
* Embed theme installed on JasperServer (included in "./resources/JS-embed-theme") for Jasper Server version 4.5 use embed-4.5.zip and for version 4.7 or 5.0 use embed-4.7.zip 

Using thiis Sample
------------------

Once running, go to your web server and login to the sample using a valid Jasper Server username and password (i.e. jasperadmin/jasperadmin)
* For samples using the REST API check the "Web Services Integration" tab
* For samples using iFrames and full UI integration check the "Jasper UI Integration" tab
* For documentation on the JasperServer PHP REST API wrapper class go to the "JasperReports Wrapper Docs" tab

Instalation Instructions
------------------------

Quick Overview

* Uncompress this folder into a path accessible to your Webserver
* Set the Webserver Document Root: "./web_root"
* Add "./resources/" to the include path of your PHP.ini
* Modify "./resources/config.php" to match your JS and App instalation paths and URLs
* For the SSO integration implemented in this sample to work, Jasper and this sample must be in the same domain 

### Instalation Step by Step How-To

This instructions assume that you already have JasperServer installed and a working Apache Web Server with PHP support.
Uncompress the sample source ZIP file in your drive:

	<myfolder>/php-sample-app/

Inside that folder you will find  2 folders:

	./resources -> libraries and other resources
	./web-root  -> application 

1) Configure your Apache web server to add an alias to the Application web-root folder. 
We will make an alias to our application web root folder, this will put our application in:

http://<your-server-IP>/myphpapp 

Find the httpd.conf (apache configuration file) and add the following:

	...
	# Alias for PHP APP demo
	Alias /myphpapp "<myfolder>/php-sample-app/web_root/"
	<Directory "<myfolder>/php-sample-app/web_root/">
	    Options Indexes  FollowSymLinks MultiViews ExecCGI
	    AllowOverride All
	    Order allow,deny
	    Allow from all
	</Directory>
	...
	
Change the path slashes accordingly depending on the OS that apache is installed on (windows or unix-like), check the Apache manual.

2) Modify the .htaccess file in web-root, to change the PHP include path, if you want this change can also be made server-wide in PHP.ini

- Edit the file called htaccess-sample that is in web-root there is only one line in this file should look like this:

	php_value include_path "<myfolder>\php-sample-app\resources;_myPEARinstallfolder_;."

_myPEARinstallfolder_ is your path to php-pear installation folder, this may be in a different path check your php.ini file

- SAVE this file as ".htaccess"  (IMPORTANT! : note the . (dot) at the beginning of the file name.)

3) Restart apache


4) Configure the sample application. Change the paths inside ./resources/config.php

	...
	// this should match the settings on your httpd.conf file 
	// i.e. your application lives in http://localhost/myphpapp
	define('WWW_ROOT', '/myphpapp/');
	// real OS path where the application is installed
	define('SITE_PATH', '/Library/WebServer/Documents/JSDemo/'); 
	...
	// CHANGE THIS TO POINT TO YOUR JASPER SERVER
	define('JRS_HOST', 'localhost');
	define('JRS_PORT', '8080');
	define('JRS_BASE', '/jasperserver-pro');
	...

6) go to: http://localhost/myphpapp and Voila!


The RESTclient.php file has the input control rendering functions and all the meat is in:

	- login.php (authentication)
	- repository.php (repository browser)
	- viewReport.php and executeReport.php (view and execute report)
	- iframe.php (iframe UI integration)

External Libraries Used
-----------------------

All the external libraries are included in this sample, check each library folder or website for each individual license.

* JasperSoft PHP Client (http://community.jaspersoft.com/project/php-client) /resources/jasper-rest
* PEST Rest Client (http://github.com/educoder/pest) /resources/PEST
* PHP Markdown (http://michelf.ca/projects/php-markdown/) /resources/php-markdown-extra-1.2.5
* Bluprint CSS (http://www.blueprintcss.org/) /web_root/css/blueprint

LICENSE AND COPYRIGHT NOTIFICATION
==================================

 Copyright (C) 2005 - 2012 Jaspersoft Corporation - All rights reserved. 

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
