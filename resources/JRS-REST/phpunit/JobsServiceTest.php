<?php
use Jasper\JasperClient;
use Jasper\Job;
use Jasper\JobSummary;
use Jasper\JasperTestUtils;

require_once(dirname(__FILE__) . '/lib/JasperTestUtils.php');
require_once(dirname(__FILE__) . '/../client/JasperClient.php');

class JasperJobTest extends PHPUnit_Framework_TestCase {
	protected $jc;


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
	}

	public function tearDown() {

	}

	public function testCreateJobObj_isJobObj() {
		$folderForJob = JasperTestUtils::createFolder();
		$job = JasperTestUtils::createJob($folderForJob);
		$this->assertTrue($job instanceof Job);
	}

	public function testPutJob_createsNewJob() {
		$folderForJob = JasperTestUtils::createFolder();
		$job = JasperTestUtils::createJob($folderForJob);

		$jobsBeforePut = $this->jc->getJobs();
		$this->jc->putResource('', $folderForJob);
		$newJobId = $this->jc->putJob($job);

		$jobsAfterPut = $this->jc->getJobs();

		$this->jc->deleteJob($newJobId);
		$this->jc->deleteResource($folderForJob->getUriString());

		$this->assertEquals((count($jobsBeforePut) + 1), count($jobsAfterPut));
	}

	public function testPostJob_updatesJob() {
		$folder = JasperTestUtils::createFolder();
		$job = JasperTestUtils::createJob($folder);

		$this->jc->putResource('', $folder);
		$jobServerId = $this->jc->putJob($job);

		$job->id = $jobServerId;

		$unique = md5(microtime());

		$job->description = $unique;
		$job->version = '0';

		$this->jc->postJob($job);

		$jobObjFromServer = $this->jc->getJob($jobServerId);

		$this->jc->deleteJob($jobServerId);
		$this->jc->deleteResource($folder->getUriString());

		$this->assertEquals($unique, $jobObjFromServer->description);
	}

}

?>