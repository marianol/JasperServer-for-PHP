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
use Jasper\User;
use Jasper\Role;
use Jasper\RESTRequestException;
use Jasper\JasperTestUtils;

require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');
require_once(dirname(__FILE__) . '/../client/JasperClient.php');

class JasperUserServiceTest extends PHPUnit_Framework_TestCase {

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

		$this->newUser = JasperTestUtils::createUser();
	}

	public function tearDown() {

	}

	/* Tests below */

	public function testGetUser_getsCorrectUser() {
		$this->jc->putUsers($this->newUser);
		$tempUser = $this->jc->getUsers($this->newUser->getUsername());
		$tempUser = $tempUser[0];
		$this->jc->deleteUser($tempUser);
		$this->assertEquals($this->newUser->getFullName(), $tempUser->getFullName());
	}

	public function testCreateUser_increasesUserCountByOne() {
		$userCount = count($this->jc->getUsers());
		$this->jc->putUsers($this->newUser);
		$this->assertEquals($userCount+1, (count($this->jc->getUsers())));
		$this->jc->deleteUser($this->newUser);
	}

	/**
	 * @depends testCreateUser_increasesUserCountByOne
	 */
	public function testDeleteUser_reducesUserCountByOne() {
		$userCount = count($this->jc->getUsers());
		$this->jc->putUsers($this->newUser);
		$this->jc->deleteUser($this->newUser);
		$this->assertEquals($userCount, count($this->jc->getUsers()));
	}

	/**
	 * @depends testCreateUser_increasesUserCountByOne
	 */
	public function testPostUser_changesUserData() {
		$this->jc->putUsers($this->newUser);
		$this->newUser->setEmailAddress('test@dude.com');
		$this->jc->postUser($this->newUser);
		$tempUser = $this->jc->getUsers($this->newUser->getUsername());
		$tempUser = $tempUser[0];
		$this->jc->deleteUser($tempUser);
		$this->assertEquals($tempUser->getEmailAddress(), 'test@dude.com');
	}

	/**
	 * @expectedException Jasper\RESTRequestException
	 */
	# This test expects and exception by deleting a user that should not exist.
	public function testDeleteUser_thatDoesntExists() {
		$this->jc->deleteUser($this->newUser);
	}

	public function testAddingARole_actuallyAddsARole() {
		$user = JasperTestUtils::createUser();
		$role = new Role('ROLE_DEMO', null, 'false');
		$this->jc->putUsers($user);
		$userServerObj_beforePost = $this->jc->getUsers($user->getUsername());
		$userServerObj_beforePost = $userServerObj_beforePost[0];

		$user->addRole($role);
		$this->jc->postUser($user);
		$userServerObj_afterPost = $this->jc->getUsers($user->getUsername());
		$userServerObj_afterPost = $userServerObj_afterPost[0];

		$this->jc->deleteUser($user);

		$this->assertEquals((count($userServerObj_beforePost->getRoles()) + 1), count($userServerObj_afterPost->getRoles()));
	}
}

?>