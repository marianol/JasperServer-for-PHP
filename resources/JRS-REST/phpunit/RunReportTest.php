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

require_once(dirname(__FILE__) . '/../client/JasperClient.php');


class JasperRunReportTest extends PHPUnit_Framework_TestCase {

	protected $jc;
	protected $sample_report;

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

		$this->sample_report = "/reports/samples/AllAccounts";
		$this->sample_report_size = 220000;	// pre-determined

	}

	public function tearDown() {

	}

	public function testRunReport_getsSomewhatProperFileSize() {
		$data = $this->jc->runReport($this->sample_report, 'pdf');
		$this->assertTrue($this->sample_report_size <= strlen($data));
	}
}
?>