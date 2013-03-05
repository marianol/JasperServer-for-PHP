<?php
/**
 * logout.php 
 * There is nothing interesting to see here.. destroy the session and redirect
 *
 *
 * @author Mariano Luna
 * @copyright Copyright (c) 2011 - 2012
 * 
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

require_once('config.php');
// Delete JRS session
setcookie('JSESSIONID', '', time() - 1000, $_SESSION["JRSPath"] );
// Destroy my session
session_unset();
session_destroy();

header('Location: ' . WWW_ROOT );
?>