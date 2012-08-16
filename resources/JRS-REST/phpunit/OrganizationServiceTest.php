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
use Jasper\Organization;

require_once(dirname(__FILE__) . '/../client/JasperClient.php');



class JasperOrganizationServiceTest extends PHPUnit_Framework_TestCase {

	protected $jc;
	protected $testOrg;
	protected $subOrg;

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

		$this->testOrg = new Organization(
			'testorg',
			'testorg',
			'organization_1',
			'testorg'
		);

		$this->subOrg = new Organization(
				'suborg',
				'suborg',
				'testorg',
				'suborg'
		);
	}

	public function tearDown() {
		if ($this->testOrg !== null) {
			$this->jc->deleteOrganization($this->testOrg);
		}

		$this->testOrg = null;
		$this->subOrg = null;
		$this->jc = null;
	}

	/* Tests below */

	public function testPutGetOrganization_withoutSubOrganzationFlag() {
		$this->jc->putOrganization($this->testOrg);
		$tempOrg = $this->jc->getOrganization($this->testOrg->getId(), false);
		$this->assertEquals($this->testOrg->getId(), $tempOrg->getId());
	}

	public function testPutGetOrganization_withSubOrganizationFlag() {
		$this->jc->putOrganization($this->testOrg);
		$this->jc->putOrganization($this->subOrg);

		$tempOrg = $this->jc->getOrganization($this->testOrg->getId(), true);
		$this->assertEquals($this->subOrg->getId(), $tempOrg->getId());

	}

	public function testPutPostOrganization_successfullyUpdatesOrganization() {
		$this->jc->putOrganization($this->testOrg);
		$this->testOrg->setTenantDesc('TEST_TEST_TEST');
		$this->jc->postOrganization($this->testOrg);

		$tempOrg = $this->jc->getOrganization($this->testOrg->getId());

		$this->assertEquals('TEST_TEST_TEST', $tempOrg->getTenantDesc());
	}

}

?>