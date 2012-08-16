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
use Jasper\Attribute;
use Jasper\User;
use Jasper\JasperTestUtils;

require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');
require_once(dirname(__FILE__) . '/../client/JasperClient.php');


class JasperAttributeServiceTest extends PHPUnit_Framework_TestCase {

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
		$this->attr = new Attribute('Gender', 'Robot');
		$this->attr2 = new Attribute('Favorite Beer', 'Anchor Steam');
		$this->attrArr = array($this->attr, $this->attr2);

		$this->jc->putUsers($this->newUser);
	}

	public function tearDown() {
		if ($this->newUser !== null) {
			$this->jc->deleteUser($this->newUser);
		}
		$this->newUser = null;
		$this->jc = null;
	}

	/* Tests below */

	public function testAttributeConstructor_allocatesDataToCorrectProperties() {
		$test = new Attribute('Name', 'Value');
		$this->assertEquals('Name', $test->getAttrName());
		$this->assertEquals('Value', $test->getAttrValue());
	}

	public function testPostAttributes_addsOneAttributeData() {
		$this->jc->postAttributes($this->newUser, $this->attr);
		$tempAttr = $this->jc->getAttributes($this->newUser);
		$tempAttrValue = $tempAttr[0]->getAttrValue();
		$tempAttrName = $tempAttr[0]->getAttrName();

		$this->assertEquals('Robot', $tempAttrValue);
		$this->assertEquals('Gender', $tempAttrName);
	}

	public function testPostAttributes_addsMultipleAttributesCount() {
		$attrCount = count($this->jc->getAttributes($this->newUser));
		$this->jc->postAttributes($this->newUser, $this->attrArr);
		$attr2Value = $this->jc->getAttributes($this->newUser);
		$newCount = count($attr2Value);

		$this->assertEquals($attrCount+2, $newCount);
		$this->assertEquals('Anchor Steam', $attr2Value[1]->getAttrValue());

	}

}

?>