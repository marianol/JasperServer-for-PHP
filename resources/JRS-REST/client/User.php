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

/* Jasper\User class
 * this class represents Users from the JasperServer and contains data that is
 * accesible via the user service in the REST API.
 *
 * author: gbacon
 * date: 06/06/2012
 */
class User {

	public $username;
	public $password;
	public $emailAddress;
	public $fullName;
	public $tenantId;
	public $roles = array();
	public $enabled;
	public $externallyDefined;
	public $previousPasswordChangeTime;

	/* Constructor
	 *
	 * This constructor can be used to populate a User object from scratch
	 * any settings not set at construction can be configured using the SET methods below
	 */
	public function __construct(
		$username = null,
		$password = null,
		$emailAddress = null,
		$fullName = null,
		$tenantId = null,
		// $roles = null,
		$enabled = null,
		$externallyDefined = null,
		$previousPasswordChangeTime = null)
	{
		// These values are checked for content and set to null otherwise
		// this way the XML_Serializer object will not include empty values
		// when creating XML. This prevents HTTP 400 errors from occuring
		$this->username = (!empty($username)) ? (string) $username : null;
		$this->password = (!empty($password)) ? (string) $password : null;
		$this->emailAddress = (!empty($emailAddress)) ? (string) $emailAddress : null;
		$this->fullName = (!empty($fullName)) ? (string) $fullName : null;
		$this->tenantId = (!empty($tenantId)) ? (string) $tenantId : null;
		// $this->roles = (!empty($roles)) ? (array) $roles : null;
		$this->enabled = (!empty($enabled)) ? (string) $enabled : null;
		$this->externallyDefined = (!empty($externallyDefined)) ? (string) $externallyDefined : null;
		$this->previousPasswordChangeTime = (!empty($previousPasswordChangeTime)) ? (string) $previousPasswordChangeTime : null;

	}

	/* configDefaults is used internally to setup default values if they're unset
	 * <externallyDefined> is set to false on default
	 * and the previousPasswordChangeTime is set if it is blank. (assuming new user)
	 */
	protected function configDefaults()
	{

		if($this->externallyDefined == null) {
			$this->externallyDefined = 'false';
		}


		if ($this->previousPasswordChangeTime == null && $this->password !== null) {
			$now = new \DateTime();
			$this->previousPasswordChangeTime = $now->format('Y-m-d\TH:i:sP');
		}
	}


	/* Get/Set
	 *
	 */
	public function getEnabled() { return $this->enabled; }
	public function getExternallyDefined() { return $this->externallyDefined; }
	public function getFullName() { return $this->fullName; }
	public function getPassword() { return $this->password; }
	public function getPreviousPasswordChangeTime() { return $this->previousPasswordChangeTime; }
	public function getRoles() { return $this->roles; }
	public function getTenantId() { return $this->tenantId; }
	public function getUsername() { return $this->username; }
	public function getEmailAddress() { return $this->emailAddress; }

	public function setEnabled($enabled) { $this->enabled = $enabled; }
	public function setExternallyDefined($externallyDefined) { $this->externallyDefined = $externallyDefined; }
	public function setFullname($fullName) { $this->fullName = $fullName; }

	public function addRole(Role $role) {
		$this->roles[] = $role;
	}

	public function delRole(Role $role) {
		$data_changed = false;
		for($i = 0; ($i < count($this->roles)) || ($i == -1); $i++) {
			if ($this->roles[$i]->getRoleName() == $role->getRoleName()
					&& $this->roles[$i]->getTenantId() == $role->getTenantId()) {
				unset($this->roles[$i]);
				$data_changed = true;
				$i = -1;
			}
		}
		if($data_changed) { return true; }
		return false;
	}

	/* setPassword automatically sets the previousPasswordChangeTime value
	 * when setting a new password
	 */
	public function setPassword($password) {
		$now = new \DateTime();
		$this->password = $password;
		$this->previousPasswordChangeTime = $now->format('Y-m-d\TH:i:sP');
	}

	public function setTenantId($tenantId) { $this->tenantId = $tenantId; }
	public function setUsername($username) { $this->username = $username; }
	public function setEmailAddress($emailAddress) { $this->emailAddress = $emailAddress; }

	public function toXML() {
		$result = new \SimpleXMLElement('<user></user>');
		foreach($this as $k => $v) {
			if ($k !== 'roles' && (!empty($v))) {
				$result->addChild($k, $v);
			}
		}
		if (!empty($this->roles)) {

			foreach($this->roles as $r) {
				$node = $result->addChild('roles');
				$role_name = $r->getRoleName();
				$externally_defined = $r->getExternallyDefined();
				$tenant_id = $r->getTenantId();
				if (!empty($role_name)) {
					$node->addChild('roleName', $role_name);
				}
				if (!empty($externally_defined)) {
					$node->addChild('externallyDefined', $externally_defined);
				}
				if (!empty($tenant_id)) {
					$node->addChild('tenantId', $tenant_id);
				}
			}
		}
		return preg_replace('/\<\?xml(.*)\?\>/', '', $result->asXML());	// Remove the XML prolog
	}

// 	// Serialize object
// 	public function asXML() {
// 		$seri_opt = array(
// 			'indent' => '     ',
// 			'rootName' => 'user',
// 			'defaultTagName' => 'roles',
// 			'ignoreNull' => true
// 			);
// 		$seri = new \XML_Serializer($seri_opt);
// 		$res = $seri->serialize($this);
// 		if ($res === true) {
// 			return $seri->getSerializedData();
// 		} else {
// 			return false;
// 		}
// 	}

	public function __toString() {
		return htmlentities($this->asXML());
	}
}
?>