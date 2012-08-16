<?php
/* ==========================================================================

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

=========================================================================== */
use Jasper\JasperClient;
use Jasper\ResourceDescriptor;
use Jasper\ResourceProperty;
use Jasper\Permission;

require_once(dirname(__FILE__) . '/../client/JasperClient.php');


class JasperPermissionServiceTest extends PHPUnit_Framework_TestCase {

	protected $jc;
	protected $newUser;

	public function setUp() {
		$bootstrap = parse_ini_file(dirname(__FILE__) . '/test.properties');
		$this->jc = new JasperClient(
				$bootstrap['hostname'],
				$bootstrap['port'],
				$bootstrap['admin_username'],
				$bootstrap['admin_password'],
				$bootstrap['base_url'],
				$bootstrap['admin_org']
				);

		// The following is a client authorized as 'superuser' needed for some tests
		$this->jcSuper = new JasperClient(
				$bootstrap['hostname'],
				$bootstrap['port'],
				$bootstrap['super_username'],
				$bootstrap['super_password'],
				$bootstrap['base_url']
				);

		$timecode = md5(microtime());
		$this->test_folder = new ResourceDescriptor($timecode, 'folder', '/' . $timecode, 'false');
		$this->test_folder->setLabel('testtesttest');
		$this->test_folder->setDescription('REST Test Folder');
		$this->test_folder->addProperty(new ResourceProperty('PROP_PARENT_FOLDER', '/'));
	}

	public function tearDown() {

	}


	/* Tests below */

	public function testGetPermissions_properSizeReturned() {
		$permissionsRoot = $this->jcSuper->getPermissions('/');
		$this->assertEquals(count($permissionsRoot), 3);

		$permissionsReports = $this->jc->getPermissions('/reports');
		$this->assertEquals(sizeof($permissionsReports), 1);
		$this->assertEquals($permissionsReports[0]->getPermissionMask(), 30);
		$this->assertEquals($permissionsReports[0]->getPermissionRecipient()->getRoleName(), 'ROLE_USER');
	}

	public function testPostPermissions_addsPermissionCorrectly() {
		$this->jc->putResource('/', $this->test_folder);
		$joeuser = $this->jc->getUsers('joeuser');
		$perm = new Permission('32', $joeuser[0], $this->test_folder->getUriString());
		$this->jc->updatePermissions($this->test_folder->getUriString(), $perm);
		$test_folder_perms = $this->jc->getPermissions($this->test_folder->getUriString());
		$this->jc->deleteResource($this->test_folder->getUriString());
		$this->assertEquals(sizeof($test_folder_perms), 1);
		$this->assertEquals($test_folder_perms[0]->getPermissionMask(), $perm->getPermissionMask());
		$this->assertEquals($test_folder_perms[0]->getPermissionRecipient()->getUsername(), $perm->getPermissionRecipient()->getUsername());
	}

	public function testDeletePermissions_deletesPermissionCorrectly() {
		$this->jc->putResource('/', $this->test_folder);
		$joeuser = $this->jc->getUsers('joeuser');
		$perm = new Permission('32', $joeuser[0], $this->test_folder->getUriString());
		$this->jc->updatePermissions($this->test_folder->getUriString(), $perm);
		$test_folder_perms = $this->jc->getPermissions($this->test_folder->getUriString());
		$this->assertEquals(sizeof($test_folder_perms), 1);
		$this->jc->deletePermission($test_folder_perms[0]);
		$perms_after_delete = $this->jc->getPermissions($this->test_folder->getUriString());
		$this->assertEquals(sizeof($perms_after_delete), 0);
		$this->jc->deleteResource($this->test_folder->getUriString());
	}



}

?>