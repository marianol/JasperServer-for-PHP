<?php
/**
 * index.php Main Site Page.
 * There is nothing interesting to see here.. just redirect to the login or proper page
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

require_once('config.php');

if($_SESSION['userlevel'] >= USER) {
	// You are already Logged in!!
	header('Location: ' . WWW_ROOT . 'home.php');
} else {
	// Guest
	header('Location: ' . WWW_ROOT . 'login.php');
}

?>