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

namespace Jasper;

// PEAR Packages
require_once('XML/Serializer.php');
require_once('XML/Unserializer.php');

// Objects used by the class
require_once('Constants.php');
require_once('REST_Request.php');
require_once('User.php');
require_once('Organization.php');
require_once('Role.php');
require_once('Attribute.php');
require_once('ResourceDescriptor.php');
require_once('JobSummary.php');
require_once('Job.php');
require_once('Permission.php');
require_once('ReportOptions.php');

class JasperClient {

	protected $hostname;
	protected $port;
	protected $username;
	protected $password;
	protected $orgId;
	protected $baseUrl;
	private $restReq;
	private $restUrl;
	private $restUrl2;

	/***> INTERNAL FUNCTIONS <***/

	/** Constructor for JasperClient. All these values are required to be defined so that
	 * the client can function properly
	 *
	 * @param string $hostname - Hostname of the JasperServer that the API is running on
	 * @param int|string $port - Port of the same server
	 * @param string $username - Username for authentication
	 * @param string $password - Password for authetication
	 * @param string $baseUrl - base URL (i.e: /jasperserver-pro or /jasperserver (community edition))
	 * @param string $orgId - organization ID, required for login within multiple tenancy
	 */
	public function __construct($hostname = 'localhost', $port = '8080', $username = null, $password = null, $baseUrl = "/jasperserver-pro", $orgId = null)
	{
		$this->hostname = $hostname;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;
		$this->baseUrl = $baseUrl;
		$this->orgId = $orgId;

		$this->restReq = new \REST_Request(); // This object is recycled
		if (!empty($this->orgId)) {
			$this->restReq->setUsername($this->username .'|'. $this->orgId);
		} else {
			$this->restReq->setUsername($this->username);	// Configure userpwd for our req object
		}
		$this->restReq->setPassword($this->password);
		$this->restUrl = PROTOCOL . $this->hostname . ":" . $this->port . $this->baseUrl . BASE_REST_URL;
		$this->restUrl2 = PROTOCOL . $this->hostname . ':' . $this->port . $this->baseUrl . BASE_REST2_URL;
	}

	/** Internal function that prepares and send the request. This function validates that
	 * the status code returned matches the $expectedCode provided and returns a bool
	 * based on that
	 *
	 * @param string $url - URL to be called
	 * @param string $verb - verb to be used
	 * @param int|string $expectedCode - Expected HTTP status code
	 * @param string $reqBody - The body of the request (POST/PUT)
	 * @param boolean $returnData - if true the responseInfo will be returned with the function
	 * @return boolean - true if expectedCode == statusCode; if no match, returns status code
	 * @throws Exception - If the statusCodes do not match
	 */
	protected function prepAndSend($url, $expectedCode = 200, $verb = null, $reqBody = null, $returnData = false, $contentType = 'application/xml', $acceptType = 'application/xml') {
		$expectedCode = (integer) $expectedCode;
		$this->restReq->flush();
		$this->restReq->setUrl($url);
		if ($verb !== null) {
			$this->restReq->setVerb($verb);
		}
		if ($reqBody !== null) {
			$this->restReq->buildPostBody($reqBody);
		}
		if (!empty($contentType)) {
			$this->restReq->setContentType($contentType);
		}
		if(!empty($acceptType)) {
			$this->restReq->setAcceptType($acceptType);
		}
		$this->restReq->execute();
		$statusCode = $this->restReq->getResponseInfo();
		$responseBody = $this->restReq->getResponseBody();
		$statusCode = $statusCode['http_code'];
		if ($statusCode !== $expectedCode) {
			if(!empty($responseBody)) {
				throw new RESTRequestException('Unexpected HTTP code returned: ' . $statusCode . ' The expected code was: ' . $expectedCode . ' Body of response: ' . strip_tags($responseBody));
			} else {
				throw new RESTRequestException('Unexpected HTTP code returned: ' . $statusCode . ' The expected code was: ' . $expectedCode);
			}
			return $statusCode;
		}
		if($returnData == true) {
			return $this->restReq->getResponseBody();
		}
		return true;
	}

	/** This function creates a multipart/form-data request and sends it to the server.
	 *  this function should only be used when a file is to be sent with a request (PUT/POST)
	 *
	 *
	 * @param string $url - URL to send request to
	 * @param string $expectedCode - HTTP Status Code you expect to recieve on success
	 * @param string $verb - HTTP Verb to send with request
	 * @param string $reqBody - The body of the request if necessary
	 * @param array $file - An array with the URI string representing the image, and the filepath to the image. (i.e: array('/images/JRLogo', '/home/user/jasper.jpg') )
	 * @param boolean $returnData - whether or not you wish to recieve the data returned by the server or not
	 * @return array - Returns an array with the response info and the response body, since the server sends a 100 request, it is hard to validate the success of the request
	 */
	protected function multipartRequestSend($url, $expectedCode = 200, $verb = 'PUT_MP', $reqBody = null, $file = null, $returnData = false) {
		$expectedCode = (integer) $expectedCode;
		$this->restReq->flush();
		$this->restReq->setUrl($url);
		$this->restReq->setVerb($verb);
		if (!empty($reqBody)) {
			$this->restReq->buildPostBody($reqBody);
		}
		if (!empty($file)) {
			$this->restReq->setFileToUpload($file);
		}
		$this->restReq->execute();
		$response = $this->restReq->getResponseInfo();
		$responseBody = $this->restReq->getResponseBody();
		$statusCode = $response['http_code'];

		return array($statusCode, $responseBody);
	}

	/***> ATTRIBUTE SERVICE <***/

	/** Retrieve attributes of a user
	 *
	 * @param User $user - user object of the user you wish to retrieve data about
	 * @return array<Attribute> - an array of attribute objects
	 * @throws Exception - if HTTP fails
	 */
	public function getAttributes(User $user) {
		$result = array();
		$url = $this->restUrl . ATTRIBUTE_BASE_URL . '/' . $user->getUsername();
		if($user->getTenantId() !== null) {
			$url .= PIPE . $user->getTenantId();
		}
		if ($data = $this->prepAndSend($url, 200, 'GET', null, true)) {
			$xml = new \SimpleXMLElement($data);
		} else {
			return false;
		}
		foreach ($xml->Item as $item) {
			$tempAttribute = new Attribute(
				$item->attrName,
				$item->attrValue);
			$result[] = $tempAttribute;
		}
		return $result;
	}

	/** Change attributes of a user or create new attribute
	 *
	 * Note: If you want to update an attribute, supply an attribute object with an existing attribute name
	 * but a different value. If you have multiple attributes with the same name, this function will NOT work
	 * as you may expect it to. The API overwrites the data that already matches
	 *
	 * Note 2: This function could be optimized so that multiple calls aren't being made to update more than
	 * one attribute at a ime
	 *
	 * @param User $user - user object of user whos attributes you wish to change
	 * @param array<Attribute> $attributes - array of attributes or one attribute object
	 * @throws Exception - if HTTP returns an error status code
	 */
	public function postAttributes(User $user, $attributes) {
		$url = $this->restUrl . ATTRIBUTE_BASE_URL . '/' . $user->getUsername();
		if ($user->getTenantId() !== null) {
			$url .= PIPE . $user->getTenantId();
		}
		if (is_array($attributes)) {
			foreach ($attributes as $attribute) {
				$this->prepAndSend($url, 201, 'PUT', $attribute->asXML());
			}
		} else {
			$this->prepAndSend($url, 201, 'PUT', $attributes->asXML());
		}
	}

	/***> USER SERVICE <***/

	/** Retrieve users from the server.
	 * Result will always be an array of zero or more User objects
	 *
	 * @param string $searchTerm - part of user name you would like to search for
	 *
	 * @return Array<User>
	 * @throws Exception if HTTP request fails
	 */
	public function getUsers($searchTerm = null) {
		$url = $this->restUrl . USER_BASE_URL . '/' . $searchTerm;
		$result = array();

		if($data = $this->prepAndSend($url, 200, 'GET', null, true)) {
			$xml = new \SimpleXMLElement($data);
		}
		foreach ($xml->user as $user) {
			$tempUser = new User(
				$user->username,
				$user->password,
				$user->emailAddress,
				$user->fullName,
				$user->tenantId,
			//	$user->roles,
				$user->enabled,
				$user->externallyDefined,
				$user->previousPasswordChangeTime);
			foreach ($user->roles as $role) {
				$tempUser->addRole(JasperClient::roleToRoleObj($role));
			}
			$result[] = $tempUser;
		}

		return $result;
	}

	protected static function roleToRoleObj(\SimpleXMLElement $xml) {
		$result = new Role();
		if (!empty($xml->roleName)) {
			$result->setRoleName($xml->roleName);
		}
		if (!empty($xml->externallyDefined)) {
			$result->setExternallyDefined($xml->externallyDefined);
		}
		if (!empty($xml->tenantId)) {
			$result->setTenantId($xml->tenantId);
		}
		return $result;
	}

	/** PUT User(s)
	 *
	 * This function adds NEW users. It will accept an array of User objects,
	 * or one User object to add to the database
	 *
	 * @param User | array<User> $users - single User object or array of User objects to be created
	 * @return boolean - based on success of function
	 */
	public function putUsers($users) {
		$url = $this->restUrl . USER_BASE_URL . '/';
		$xml = null;
		if (is_array($users)) {
			foreach ($users as $u)
			{
				$xml .= $u->toXML();
			}
		} else {
			$xml = $users->toXML();
		}
		$this->prepAndSend($url, 201, 'PUT', $xml);
		return true;
	}

	/** POST User
	 *
	 * This function UPDATES a user. You can only update one user at a time.
	 * the best practice is to retrieve a user object from the server initially,
	 * make modifications to the user as needed, and then POST the updates using this
	 * function. It is not advised to create a User object from scratch to make updates
	 *
	 * @param User $user - single User object
	 * @return boolean - based on success of function
	 */
	public function postUser(User $user, $old_username = null) {
		if(!empty($old_username)) {
			$url .= $old_username . PIPE . $user->getTenantId();
		} else {
			$url = $this->restUrl . USER_BASE_URL . '/' . $user->getUsername() . PIPE . $user->getTenantId();
		}
		$xml = $user->toXML();
		if ($this->prepAndSend($url, 200, 'POST', $xml)) {
			return true;
		}
		return false;
	}

	/** This function will delete a user, only one user
	 *
	 * First get the user using getUsers(), then provide the user you wish to delete
	 * as the parameter for this function
	 *
	 * @param User $user - user to delete
	 * @return boolean - based on success of function
	 */
	public function deleteUser(User $user) {
		$url = $this->restUrl . USER_BASE_URL . '/' . $user->getUsername();
		if ($user->getTenantId() !== null) { $url .= PIPE . $user->getTenantId(); }
		if ($this->prepAndSend($url, 200, 'DELETE')) {
			return true;
		}
		return false;
	}

	/***> ORGANIZATION SERVICE <***/

	/** This function retrieves an organization and its information by ID
	 *
	 * @param string $org - organization id (i.e: "organization_1")
	 * @param boolean $listSub - If this is true, suborganizations are only retrieved
	 * @return Organization - object that represents organization & its data
	 * @throws Exception - if HTTP request doesn't respond as expected
	 */
	public function getOrganization($org, $listSub = false) {
		$url = $this->restUrl . ORGANIZATION_BASE_URL . '/' . $org;
		if ($listSub == true) { $url .= '?listSubOrgs=true'; }
		if($data = $this->prepAndSend($url, 200, 'GET', null, true)) {
			$xml = new \SimpleXMLElement($data);
			if($listSub == true) {
				$arrayResult = array();
				// This recursion simplifies Serializer handling and unknown responses (may only have 0 or more suborganizations, etc)
				// However making multiple API calls may see scaling issues with large amounts of data
				foreach($xml->tenant as $t) {
					$arrayResult[] = $this->getOrganization($t->id, false);
				}
				if (count($arrayResult) > 1) {
					return $arrayResult;
				}
				return $arrayResult[0];
			}
			$orgObj = new Organization(
				$xml->alias,
				$xml->id,
				$xml->parentId,
				$xml->tenantName,
				$xml->theme,
				$xml->tenantDesc,
				$xml->tenantFolderUri,
				$xml->tenantNote,
				$xml->tenantUri);
		} else {
			return false;
		}
		return $orgObj;
	}

	/** This function creates an organization on the server you must provide a
	 * built organization object to it as a parameter
	 *
	 * @param Organization $org - organization object to add
	 * @return boolean - based on success of request
	 * @throws Exception - if HTTP request doesn't signify success
	 */
	public function putOrganization(Organization $org) {
		$url = $this->restUrl . ORGANIZATION_BASE_URL;
		$xml = $org->asXML();
		if($this->prepAndSend($url, 201, 'PUT', $xml)) {
			return true;
		}
		return false;
	}

	/** Delete an organization
	 *
	 * @param Organization $org - organization object
	 * @return boolean - based on success of request
	 * @throws Exception - if HTTP request doesn't succeed
	 */
	public function deleteOrganization(Organization $org) {
		$url = $this->restUrl . ORGANIZATION_BASE_URL . '/' . $org;
		if($this->prepAndSend($url, 200, 'DELETE')) {
			return true;
		}
		return false;
	}

	/** Update an organisation
	 *
	 * It is suggested that you use the getOrganization function to retrieve an object to be updated
	 * then from there you can modify it using the set functions, and then provide it to this function
	 * to be udpated on the server side. Integrity checks are not made through this library, but
	 * any errors retrieved by the server do raise an Exception
	 *
	 * @param Organization $org - organisation object
	 * @return boolean - based on success of request
	 * @throws Exception - if HTTP request doesn't succeed
	 */
	public function postOrganization(Organization $org) {
		$url = $this->restUrl . ORGANIZATION_BASE_URL . '/' . $org->getId();
		if($this->prepAndSend($url, 200, 'POST', $org->asXML())) {
			return true;
		}
		return false;
	}

	/***> ROLE SERVICE <***/

	/** Retrieve existing roles
	 *
	 * Returns all roles that match $searchTerm (results can be >1). If you wish to retrieve all roles in a
	 * suborganization, set $searchTerm to an empty string and define the suborganization
	 * i.e: $jasperclient->getRoles('', 'organization_1');
	 *
	 * @param string $searchTerm - search the roles for matching values - returns multiple if multiple matches
	 * @param string $tenantId - if the role is part of an organization, be sure to add tenantId
	 * @return Role role - role object that represents the role
	 * @throws Exception - if http request doesn't succeed
	 */
	public function getRoles($searchTerm = null, $tenantId = null) {
		$result = array();
		$url = $this->restUrl . ROLE_BASE_URL;

		if ($searchTerm !== null) { $url .= '/' . $searchTerm; }
		if ($tenantId !== null) { $url .= PIPE . $tenantId; }

		if($data = $this->prepAndSend($url, 200, 'GET', null, true)) {
			$xml = new \SimpleXMLElement($data);
		} else {
			return false;
		}
		foreach ($xml->role as $role) {
			$tempRole = new Role($role->roleName,
					$role->tenantId,
					$role->externallyDefined);
			$result[] = $tempRole;
		}
		if(count($result) == 1) {
			return $result[0];
		}
		return $result;
	}

	/** Add a new role
	 *
	 * Provide a role object that represents the role you wish to add
	 *
	 * @param Role $role - role to add (1 at a time)
	 * @return boolean - based on success of function
	 * @throws Exception - if http request doesn't succeed
	 */
	public function putRole(Role $role) {
		$url = PROTOCOL . $this->hostname . ':' . $this->port . $this->baseUrl . BASE_REST_URL . ROLE_BASE_URL;
		if($this->prepAndSend($url, 201, 'PUT', $role->asXML())) {
			return true;
		}
		return false;
	}

	/** Remove a role currently in existence
	 *
	 * Provide the role object of the role you wish to remove. Use getRole() to retrieve ROLEs
	 *
	 * @param string $roleName - Name of the role to DELETE
	 * @return boolean - based on success of function
	 * @throws Exception - if http request doesn't succeed
	 */
	public function deleteRole(Role $role) {
		$url = $this->restUrl . ROLE_BASE_URL . '/' . $role->getRoleName();
		$tenantId = $role->getTenantId();
		if ($tenantId !== null || $tenantId !== '') { $url .= PIPE . $tenantId; }
		if($this->prepAndSend($url, 200, 'DELETE')) {
			return true;
		}
		return false;
	}

	/** Update a role currently in existence
	 *
	 * Provide the Role object of the role you wish to change, then a string of the new name
	 * you wish to give the role. You can optionally provide a new tenantId if you wish to change
	 * that as well.
	 *
	 * @param Role $role - Role object to be changed
	 * @param string $oldName - previous name for the role
	 * @return boolean - based on success of function
	 * @throws Exception - if http request does not succeed
	 */
	public function postRole(Role $role, $oldName = null) {
		$url = $this->restUrl . ROLE_BASE_URL . '/' . $oldName;
		if ($role->getTenantId() !== '' && $role->getTenantId() !== null) {
			$url .= PIPE . $role->getTenantId();
		}

		if($this->prepAndSend($url, 200, 'POST', $role->asXML())) {
			return true;
		}
		return false;
	}

	/***> REPORT SERVICE <***/
	/**
	 * This function runs and retrieves the binary data of a report
	 *
	 * Note: This function utilizes the "rest_v2" service which was first released with JasperReports Server v4.7
	 *
	 * @param string $uri - URI for the report you wish to run
	 * @param string $format - The format you wish to receive the report in (default: pdf)
	 * @param string $page - Request a specific page
	 * @param array $inputControls - associative array of key => value for any input controls
	 * @return string - the binary data of the report to be handled by external functions
	 */
	public function runReport($uri, $format = 'pdf', $page = null, $inputControls = null) {
		$url = $this->restUrl2 . REPORTS_BASE_URL . $uri . '.' . $format;
		if(!(empty($page) && empty($inputControls))) {
			// The following line creates the arguments for the URL
			// the regex is to remove the numerical index markers in the URL
			$url .= '?' . preg_replace('/%5B([0-9]{1,})%5D/', null, http_build_query(array('page' => $page) + (array) $inputControls));
		}
		$binary = $this->prepAndSend($url, 200, 'GET', null, true);
		return $binary;
	}

	/***> REPOSITORY SERVICE <***/

	/**
	 * This function retrieves the Resources from the server. It returns an array consisting of ResourceDescriptor objects that represnt the data.
	 *
	 * @param string $uri
	 * @param string $query
	 * @param string $wsType
	 * @param string $recursive
	 * @param string $limit
	 * @return array<ResourceDescriptor>
	 */
	public function getRepository($uri = null, $query = null, $wsType = null, $recursive = null, $limit = null) {
		$url = $this->restUrl . '/resources';
		$suffix = http_build_query(array('q' => $query, 'type' => $wsType, 'recursive' => $recursive, 'limit' => $limit));
		$result = array();

		if(!empty($uri)) { $url .= $uri; }
		if (!empty($suffix)) { $url .= '?' . $suffix; }
		$data = $this->prepAndSend($url, 200, 'GET', null, true);
		$xml = new \SimpleXMLElement($data);
		foreach ($xml->resourceDescriptor as $rd) {
			$obj = ResourceDescriptor::createFromXML($rd->asXML());
			$result[] = $obj;
		}
		return $result;
	}

	/** This function retrieves a resource descriptor for a specified resource at $path on the server.
	 * If you wish to supply information to the input controls you can supply the data to the $p and $pl arguments
	 *
	 * @param string $path
	 * @param boolean $fileData - set to true if you wish to recieve the binary data of the resource (i.e: with images)
	 * @param string $ic_get_query_data - the datasource to query
	 * @param string $p - single select parameters | example: array(parameter_name, value)
	 * @param string $pl - multi select parameters | example: array(parameter_name, array(value1, value2, value3))
	 * @return Jasper\ResourceDescriptor
	 */
	public function getResource($path, $fileData = false, $ic_get_query_data = null, $p = null, $pl = null) {
		$url = $this->restUrl . '/resource' . $path;
		$suffix = ($fileData) ? http_build_query(array('fileData' => 'true')) : null;
		$suffix .= http_build_query(array('IC_GET_QUERY_DATA' => $ic_get_query_data));
		if (!empty($p)) { $suffix .= http_build_query($p); }
		if (!empty($pl)) {
			$param = array_shift($pl);
			if(!empty($suffix)) { $suffix .= '&'; }
			// http_build_query will take the numerical array and transfer the index keys ([0], [1], etc) into text
			// for the URL. This is undesireable in this scenario, so we use a regular expression to remove the indicies
			$suffix .= preg_replace('/%5B([0-9]{1,})%5D/', null, http_build_query(array('PL_' . $param => $pl)));
		}
		if (!empty($suffix)) { $url .= '?' . $suffix; }
		$data = $this->prepAndSend($url, 200, 'GET', null, true);
		if ($fileData === true) {
			return $data;
		} else {
		return ResourceDescriptor::createFromXML($data);
		}
	}

	/** Upload a new resource to the repository
	 *  first create a ResourceDescriptor object
	 * @param string $path
	 * @param ResourceDescriptor $rd - ResourceDescriptor object that relates to the resource being uploaded
	 * @param string $file - File path to file being uploaded
	 * @return
	 */
	public function putResource($path, ResourceDescriptor $rd, $file = null) {
		$url = $this->restUrl . '/resource' . $path;
		$statusCode = null;
		if (!empty($file)) {
			$data = $this->multipartRequestSend($url, 201, 'PUT_MP', $rd->toXML(), array($rd->getUriString(), $file), true);
			$statusCode = $data[0];
		} else {
			$data = $this->prepAndSend($url, 201, 'PUT', $rd->toXML(), null, true);
			if ($data) { return true; }
		}
		// Note: the prepAndSend function handles the following error checking within itself, however with a multipart request
		// status code 100 is sometimes returned for more data, when handling through this function, the final status code is returned
		// and can be properly validated
		if ($statusCode !== 201) {
			throw new RESTRequestException('Unexpected HTTP code returned: ' . $statusCode);
		} else {
			return true;
		}
		return false;
	}

	/** Update a resource that is already in existence by providing a new ResourceDescriptor defining the object at the URI provided
	 *
	 * @param string $path - The path to the resource you wish to change
	 * @param ResourceDescriptor $rd - a ResourceDescriptor object that correlates to the object you wish to modify (with the changes)
	 * @param string $file - full file path to the image you wish to upload
	 * @throws Exception - on failure
	 * @return boolean - based on success of function
	 */
	public function postResource($path, ResourceDescriptor $rd, $file = null) {
		$url = $this->restUrl . '/resource' . $path;
		$statusCode = null;
		if (!empty($file)) {
			$data = $this->multipartRequestSend($url, 200, 'POST_MP', $rd->toXML(), array($rd->getUriString(), $file), true);
			$statusCode = $data[0];
		} else {
			$data = $this->prepAndSend($url, 200, 'POST', $rd->toXML(), null, true);
			if($data) { return true; }
		}
		if ($statusCode !== 200) {
			throw new RESTRequestException('Unexpected HTTP code returned: ' . $statusCode);
		} else {
			return true;
		}
		return false;
	}

	/** This function deletes a resource
	 *  will only succeed if certain requirments are met. See "Web Services Guide" to see these requirements.
	 *
	 * @param string $path - path to resource to be deleted
	 * @return boolean
	 */
	public function deleteResource($path) {
		$url = $this->restUrl . '/resource' . $path;
		$result = $this->prepAndSend($url, 200, 'DELETE');
		return $result;
	}

	/***> JOB/JOBSUMMARY SERVICE <***/

	/** Retrieve scheduled Jobs (Using RESTv2)
	 * Note: This function implements the rest_v2 protocol, freshly available in JasperReports v4.7
	 *
	 * You can search either by URI or Name. If you are searching by job name, set the second argument to true.
	 *
	 * @param string $query - your search term
	 * @param boolean $name - true = search by Report Name | false = search by report URI (default when unset)
	 * @return \Jasper\JobSummary|NULL
	 * @throws Exception - if HTTP status is not 200. This includes if there is NO CONTENT (204)
	 */
	public function getJobs($query = null, $searchByName = false) {
		$url = $this->restUrl2 . '/jobs';
		if (!empty($query)) {
			$suffix = ($searchByName) ? http_build_query(array('label' => $query)) : http_build_query(array('reportUnitURI' => $query));
			$url .= '?' . $suffix;
		}
		$data = $this->prepAndSend($url, 200, 'GET', null, true);
		if (!empty($data)) {
			$xml = new \SimpleXMLElement($data);
			foreach($xml->jobsummary as $job) {
				$result[] = new JobSummary($job->id, $job->label, $job->reportUnitURI, $job->version, $job->owner,
										    $job->state->value, $job->state->nextFireTime, $job->state->previousFireTime);
			}
		}
		return $result;
	}

	/** Request a job object from server by JobID
	 *  JobID can be found using getId() from an array of jobs returned by the getJobs function
	 *
	 * @param int|string $id - the ID of the job you wish to know more about
	 * @return Job object
	 */
	public function getJob($id) {
		$url = $this->restUrl2 . '/jobs/' . $id;
		$data = $this->prepAndSend($url, 200, 'GET', null, true);
		$result = Job::createFromXML($data);
		return $result;
	}

	/** Delete a scheduled task
	 * This function will delete a job that is scheduled. You must supply the Job's ID to this function to delete it.
	 *
	 *
	 * @param int|string $id - can be retrieved using getId() on a Job or JobSummary object
	 * @return boolean - based on success of function
	 */
	public function deleteJob($id) {
		$url = $this->restUrl . '/job/' . $id;
		$data = $this->prepAndSend($url, 200, 'DELETE');
		if ($data) { return true; }
		return false;
	}

	/** Get the State of a Job
	 * This function returns an array with two values. 'nextFireTime' and 'value'
	 *
	 * @param int|string $id - can be retrieved using getId() on a Job or JobSummary object
	 * @return unknown
	 */
	public function getJobState($id) {
		$url = $this->restUrl2 . '/jobs/' . $id . '/state';
		$data = $this->prepAndSend($url, 200, 'GET', null, true);
		$us = new \XML_Unserializer();
		$us->unserialize($data);
		$result = $us->getUnserializedData();
		return $result;
	}

	/** Create the request body for a PAUSE or RESUME job function
	 *
	 * @param array|null $jobs
	 * @return string|null - Returns the request body
	 */
	protected function setupJobList($jobs = null) {
		$body = new \SimpleXMLElement('<jobIdList></jobIdList>');
		$req_body = $body->asXML();
		if (!empty($jobs)) {
			if(is_array($jobs)) {
				foreach($jobs as $job) {
					$body->addChild('jobId', $job);
				}
				$req_body = preg_replace('/<\?xml(.*)\?>/', '', $body->asXML());
			} else {
				$body->addChild('jobId', $jobs);
				$req_body = $body->asXML();
			}
		}
		return $req_body;
	}

	/** Pause a job, all jobs, or multiple jobs
	 *
	 * @param string|array|int|null $jobsToStop - int|string for one job (i.e: '40393'), or an array of jobIds, leave null for all jobs
	 * @return boolean - based on success of function
	 */
	public function pauseJob($jobsToStop = null) {
		$url = $this->restUrl2 . '/jobs/pause';
		$body = $this->setupJobList($jobsToStop);
		$data = $this->prepAndSend($url, 200, 'POST', $body);
		if ($data) { return true; }
		return false;
	}

	/** Resume a job, all jobs, or multiple jobs
	 *
	 * @param string|array|int|null $jobsToResume - int|string for one job (i.e: '40393'), or an array of jobIds, leave null for all jobs
	 * @return boolean - based on success of function
	 */
	public function resumeJob($jobsToResume = null) {
		$url = $this->restUrl2 . '/jobs/resume';
		$body = $this->setupJobList($jobsToResume);
		$data = $this->prepAndSend($url, 200, 'POST', $body);
		if ($data) { return true; }
		return false;
	}

	/** Place a new job on the server
	 *
	 *  Create a fully defined ResourceDescriptor and use this function to place it on the server. `id` does not need to be set as the server
	 *  will assign a unique id to the object. This function returns a corresponding new Job object complete with the assigned id
	 *
	 * @param Job $job
	 * @return int - this function returns the ID for the Job object you just created
	 */
	public function putJob(Job $job) {
		$url = $this->restUrl2 . '/jobs';
		$data = $this->prepAndSend($url, 200, 'PUT', $job->toXML(), true); // For some reason PUT returns 200, this may change to 201 in the future
		$result = Job::createFromXML($data);
		return $result->id;
	}

	/** Update a job
	 *
	 * After grabbing a job using getJob() you can modify the values of the job object so it represents the changes you wish to make. Then
	 * using this function you can update the job definition on the server
	 *
	 * @param Job $job - Job object representing updated job -- `id` must match ID of old job
	 * @return boolean - based on success of function
	 */
	public function postJob(Job $job) {
		$url = $this->restUrl2 . '/jobs/' . $job->id;
		$data = $this->prepAndSend($url, 200, 'POST', $job->toXML(), true);
		return $data;
	}

	/** Retrieve permissions about a URI
	 * Your result will always be an array of 0 or more items
	 *
	 * @param string $uri
	 * @return array<Permission>
	 */
	public function getPermissions($uri) {
		$url = $this->restUrl . '/permission' . $uri;
		$data = $this->prepAndSend($url, 200, 'GET', null, true);
		return Permission::createFromXML($data);
	}

	/** PUT/POST Permissions
	 * This function updates the permissions for a URI
	 *
	 * @param string $uri
	 * @param array|Permission $permissions
	 * @return boolean
	 */
	public function updatePermissions($uri, $permissions) {
		$url = $this->restUrl . '/permission' . $uri;
		if(is_array($permissions)) {
			$body = Permission::createXMLFromArray($permissions);
		} else {
			$body = $permissions->toXML();
		}
		$data = $this->prepAndSend($url, 200, 'PUT', $body);	// PUT and POST are synonyms for this service and thus both return 200
		if ($data) { return true; }
		return false;
	}

	/** Remove a preexisting permission
	 *  this function will remove an existing permission.
	 *  Simply provide the permission object you wish to delete. (use getPermissions to fetch existing permissions)
	 *
	 * @param Permission - object correlating to permission to be deleted.
	 * @return boolean - based on success of function
	 */
	public function deletePermission(Permission $perm) {
		$url = $this->restUrl . '/permission' . $perm->getUri() . '?';
		// Discover what type of object we are working with
		// and set the URL arguments accordingly
		$recipient = $perm->getPermissionRecipient();

		if($recipient instanceof User) {
			$url .= http_build_query(array('users' => $recipient->getUsername()));
		} elseif ($recipient instanceof Role) {
			$url .= http_build_query(array('roles' => $recipient->getRoleName()));
		} else {
			throw RESTRequestException('Unacceptable permissionRecipient in Permission object');
		}
		$data = $this->prepAndSend($url, 200, 'DELETE', null);
		return $data;
	}

	/**
	 * Using this function you can request the report options for a report.
	 *
	 * @param string $uri
	 * @return Array<\Jasper\ReportOptions>
	 */
	public function getReportOptions($uri) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options';
		$data = $this->prepAndSend($url, 200, 'GET', null, true, 'application/json', 'application/json');
		return ReportOptions::createFromJSON($data);
	}

	/**
	 * This function will request teh possible values and data behind all the input controls of a report
	 * @param string $uri
	 * @return Array<\Jasper\InputOptions>
	 */
	public function getReportInputControls($uri) {
		$url = $this->restUrl2 . '/reports' . $uri . '/inputControls/values';
		$data = $this->prepAndSend($url, 200, 'GET', null, true, 'application/json', 'application/json');
		return InputOptions::createFromJSON($data);
	}

	/**
	 * Update or Create new Report Options. The argument $controlOptions must be an array in the following form:
	 * array('key' => array('value1', 'value2'), 'key2' => array('value1-2', 'value2-2'))
	 * Note that even when there is only one value, it must be encapsulated within an array.
	 *
	 * @param string $uri
	 * @param Array<string> $controlOptions
	 * @param string $label
	 * @param string $overwrite
	 * @return boolean
	 */
	public function updateReportOptions($uri, $controlOptions, $label, $overwrite) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options';
		$url .= '?' . http_build_query(array('label' => utf8_encode($label), 'overwrite' => $overwrite));
		$body = json_encode($controlOptions);
		$data = $this->prepAndSend($url, 200, 'POST', $body, false, 'application/json', 'application/json');
		return $data;
	}

	/**
	 * Remove a pre-existing report options. Provide the URI and Label of the report options you wish to remove.
	 * @param string $uri
	 * @param string $optionsLabel
	 * @return boolean
	 */
	public function deleteReportOptions($uri, $optionsLabel) {
		$url = $this->restUrl2 . '/reports' . $uri . '/options/' . $optionsLabel;
		$data = $this->prepAndSend($url, 200, 'DELETE', null, false);
		return $data;
	}

} // End Client

class RESTRequestException extends \Exception {

	public function __construct($message) {
		$this->message = $message;
	}

}

?>