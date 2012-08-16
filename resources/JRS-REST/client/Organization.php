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

/* Jasper\Organization class
 * this class represents Organizations from the JasperServer and contains data that is
 * accesible via the user service in the REST API.
 *
 * author: gbacon
 * date: 06/07/2012
 */
class Organization {

	public $alias;
	public $id;
	public $parentId;
	public $tenantName;
	public $theme;
	public $tenantDesc;
	public $tenantFolderUri;
	public $tenantNote;
	public $tenantUri;



	/* Constructor
	 *
	 * This constructor can be used to populate an Organization object from scratch
	 * any settings not set at construction can be configured using the SET methods below
	 */
	public function __construct(
		$alias = null,
		$id = null,
		$parentId = null,
		$tenantName = null,
		$theme = null,
		$tenantDesc = null,
		$tenantFolderUri = null,
		$tenantNote = null,
		$tenantUri = null)
	{
		// These values are checked for content and set to null otherwise
		// this way the XML_Serializer object will not include empty values
		// when creating XML. This prevents HTTP 400 errors from occuring
		$this->alias = (!empty($alias)) ? (string) $alias : null;
		$this->id = (!empty($id)) ? (string) $id : null;
		$this->parentId = (!empty($parentId)) ? (string) $parentId : null;
		$this->tenantName = (!empty($tenantName)) ? (string) $tenantName : null;
		$this->theme = (!empty($theme)) ? (string) $theme : null;
		$this->tenantDesc = (!empty($tenantDesc)) ? (string) $tenantDesc : null;
		$this->tenantFolderUri = (!empty($tenantFolderUri)) ? (string) $tenantFolderUri : null;
		$this->tenantNote = (!empty($tenantNote)) ? (string) $tenantNote : null;
		$this->tenantUri = (!empty($tenantUri)) ? (string) $tenantUri : null;


	}


	/* Get/Set
	 *
	 */
	public function getAlias() { return $this->alias; }
	public function getId() { return $this->id; }
	public function getParentId() { return $this->parentId; }
	public function getTenantName() { return $this->tenantName; }
	public function getTheme() { return $this->theme; }
	public function getTenantDesc() { return $this->tenantDesc; }
	public function getTenantFolderUri() { return $this->tenantFolderUri; }
	public function getTenantNote() { return $this->tenantNote; }
	public function getTenantUri() { return $this->tenantUri; }


	public function setAlias($alias) { $this->alias = $alias; }
	public function setId($id) { $this->id = $id; }
	public function setParentId($parentId) { $this->parentId = $parentId; }
	public function setTenantName($tenantName) { $this->tenantName = $tenantName; }
	public function setTheme($theme) { $this->theme = $theme; }
	public function setTenantDesc($tenantDesc) { $this->tenantDesc = $tenantDesc; }
	public function setTenantFolderUri($tenantFolderUri) { $this->tenantFolderUri = $tenantFolderUri; }
	public function setTenantNote($tenantNote) { $this->tenantNote = $tenantNote; }
	public function setTenantUri($tenantUri) { $this->tenantUri = $tenantUri; }


	public function asXML() {
		$seri_opt = array(
			'indent' => '     ',
			'rootName' => 'tenant',
			'ignoreNull' => true
			);
		$seri = new \XML_Serializer($seri_opt);
		$res = $seri->serialize($this);
		if ($res === true) {
			return $seri->getSerializedData();
		} else {
			return false;
		}
	}

	/**
	 * This toString method provides the ability to use the object as an argument in some of the
	 * client features. When it is appended to a URL, it will print the ID.
	 * @return string
	 */
	public function __toString() {
		return $this->id;
	}
}
?>