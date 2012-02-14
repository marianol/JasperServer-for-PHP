<?php
/*
 * 
LICENSE AND COPYRIGHT NOTIFICATION
==================================

The Proof of Concept deliverable(s) are (c) 2011 Jaspersoft Corporation - All rights reserved. 
Jaspersoft grants to you a non-exclusive, non-transferable, royalty-free license to use the deliverable(s) pursuant to 
the applicable evaluation license agreement (or, if you later purchase a subscription, the applicable subscription 
agreement) relating to the Jaspersoft software product at issue. 

The Jaspersoft Sales department provides the Proof of Concept deliverable(s) "AS IS" and WITHOUT A WARRANTY OF ANY KIND. 
It is not covered by any Jaspersoft Support agreement or included in any Professional Services offering. 
At the discretion of the head of the Jaspersoft Professional Services team, support, maintenance and enhancements may be 
available for such deliverable(s) as "Time for Hire": http://www.jaspersoft.com/time-for-hire.

 */
	$jasperserver_url = "http://localhost:8080/jasperserver/services/";

	$jasperserver_url = "http://localhost:8080/jasperserver-pro/services/";

	$SCHEDULING_WS_URI =  $jasperserver_url . "ReportScheduler?wsdl";
        $webservices_uri = $jasperserver_url . "repository";

	$namespace = "http://www.jaspersoft.com/namespaces/php";

	require_once('SOAP/Client.php');

	define("TYPE_FOLDER","folder");
	define("TYPE_REPORTUNIT","reportUnit");
	define("TYPE_DATASOURCE","datasource");
	define("TYPE_DATASOURCE_JDBC","jdbc");
	define("TYPE_DATASOURCE_JNDI","jndi");
	define("TYPE_DATASOURCE_BEAN","bean");
	define("TYPE_IMAGE","img");
	define("TYPE_FONT","font");
	define("TYPE_JRXML","jrxml");
	define("TYPE_CLASS_JAR","jar");
	define("TYPE_RESOURCE_BUNDLE","prop");
	define("TYPE_REFERENCE","reference");
	define("TYPE_INPUT_CONTROL","inputControl");
	define("TYPE_DATA_TYPE","dataType");
	define("TYPE_OLAP_MONDRIAN_CONNECTION","olapMondrianCon");
	define("TYPE_OLAP_XMLA_CONNECTION","olapXmlaCon");
	define("TYPE_MONDRIAN_SCHEMA","olapMondrianSchema");
	define("TYPE_XMLA_CONNTCTION","xmlaConntction");
	define("TYPE_UNKNOW","unknow");
	define("TYPE_LOV","lov"); // List of values...
	define("TYPE_QUERY","query"); // List of values...

	/**
	 * These constants are copied here from DataType for facility
	 */
	define("DT_TYPE_TEXT",1);
	define("DT_TYPE_NUMBER",2);
	define("DT_TYPE_DATE",3);
	define("DT_TYPE_DATE_TIME",4);

	/**
	 * These constants are copied here from InputControl for facility
	 */
	define("IC_TYPE_BOOLEAN",1);
	define("IC_TYPE_SINGLE_VALUE",2);
	define("IC_TYPE_SINGLE_SELECT_LIST_OF_VALUES",3);
	define("IC_TYPE_SINGLE_SELECT_QUERY",4);
	define("IC_TYPE_MULTI_VALUE",5);
	define("IC_TYPE_MULTI_SELECT_LIST_OF_VALUES",6);
	define("IC_TYPE_MULTI_SELECT_QUERY",7);


	define("PROP_VERSION","PROP_VERSION");
	define("PROP_PARENT_FOLDER","PROP_PARENT_FOLDER");
	define("PROP_RESOURCE_TYPE","PROP_RESOURCE_TYPE");
	define("PROP_CREATION_DATE","PROP_CREATION_DATE");

	// File resource properties
	define("PROP_FILERESOURCE_HAS_DATA","PROP_HAS_DATA");
	define("PROP_FILERESOURCE_IS_REFERENCE","PROP_IS_REFERENCE");
	define("PROP_FILERESOURCE_REFERENCE_URI","PROP_REFERENCE_URI");
	define("PROP_FILERESOURCE_WSTYPE","PROP_WSTYPE");

	// Datasource properties
	define("PROP_DATASOURCE_DRIVER_CLASS","PROP_DATASOURCE_DRIVER_CLASS");
	define("PROP_DATASOURCE_CONNECTION_URL","PROP_DATASOURCE_CONNECTION_URL");
	define("PROP_DATASOURCE_USERNAME","PROP_DATASOURCE_USERNAME");
	define("PROP_DATASOURCE_PASSWORD","PROP_DATASOURCE_PASSWORD");
	define("PROP_DATASOURCE_JNDI_NAME","PROP_DATASOURCE_JNDI_NAME");
	define("PROP_DATASOURCE_BEAN_NAME","PROP_DATASOURCE_BEAN_NAME");
	define("PROP_DATASOURCE_BEAN_METHOD","PROP_DATASOURCE_BEAN_METHOD");


	// ReportUnit resource properties
	define("PROP_RU_DATASOURCE_TYPE","PROP_RU_DATASOURCE_TYPE");
	define("PROP_RU_IS_MAIN_REPORT","PROP_RU_IS_MAIN_REPORT");

	// DataType resource properties
	define("PROP_DATATYPE_STRICT_MAX","PROP_DATATYPE_STRICT_MAX");
	define("PROP_DATATYPE_STRICT_MIN","PROP_DATATYPE_STRICT_MIN");
	define("PROP_DATATYPE_MIN_VALUE","PROP_DATATYPE_MIN_VALUE");
	define("PROP_DATATYPE_MAX_VALUE","PROP_DATATYPE_MAX_VALUE");
	define("PROP_DATATYPE_PATTERN","PROP_DATATYPE_PATTERN");
	define("PROP_DATATYPE_TYPE","PROP_DATATYPE_TYPE");

	 // ListOfValues resource properties
	define("PROP_LOV","PROP_LOV");
	define("PROP_LOV_LABEL","PROP_LOV_LABEL");
	define("PROP_LOV_VALUE","PROP_LOV_VALUE");


	// InputControl resource properties
	define("PROP_INPUTCONTROL_TYPE","PROP_INPUTCONTROL_TYPE");
	define("PROP_INPUTCONTROL_IS_MANDATORY","PROP_INPUTCONTROL_IS_MANDATORY");
	define("PROP_INPUTCONTROL_IS_READONLY","PROP_INPUTCONTROL_IS_READONLY");

	// SQL resource properties
	define("PROP_QUERY","PROP_QUERY");
	define("PROP_QUERY_VISIBLE_COLUMNS","PROP_QUERY_VISIBLE_COLUMNS");
	define("PROP_QUERY_VISIBLE_COLUMN_NAME","PROP_QUERY_VISIBLE_COLUMN_NAME");
	define("PROP_QUERY_VALUE_COLUMN","PROP_QUERY_VALUE_COLUMN");
	define("PROP_QUERY_LANGUAGE","PROP_QUERY_LANGUAGE");


	// SQL resource properties
	define("PROP_QUERY_DATA","PROP_QUERY_DATA");
	define("PROP_QUERY_DATA_ROW","PROP_QUERY_DATA_ROW");
	define("PROP_QUERY_DATA_ROW_COLUMN","PROP_QUERY_DATA_ROW_COLUMN");


	define("MODIFY_REPORTUNIT","MODIFY_REPORTUNIT_URI");
	define("CREATE_REPORTUNIT","CREATE_REPORTUNIT_BOOLEAN");
	define("LIST_DATASOURCES","LIST_DATASOURCES");
	define("IC_GET_QUERY_DATA","IC_GET_QUERY_DATA");

	define("VALUE_TRUE","true");
	define("VALUE_FALSE","false");

	define("RUN_OUTPUT_FORMAT","RUN_OUTPUT_FORMAT");
	define("RUN_OUTPUT_FORMAT_PDF","PDF");
	define("RUN_OUTPUT_FORMAT_JRPRINT","JRPRINT");
	define("RUN_OUTPUT_FORMAT_HTML","HTML");
	define("RUN_OUTPUT_FORMAT_XLS","XLS");
	define("RUN_OUTPUT_FORMAT_XML","XML");
	define("RUN_OUTPUT_FORMAT_CSV","CSV");
	define("RUN_OUTPUT_FORMAT_RTF","RTF");
	define("RUN_OUTPUT_FORMAT_SWF","SWF");
	define("RUN_OUTPUT_IMAGES_URI","IMAGES_URI");
	define("RUN_OUTPUT_PAGE","PAGE");

	// ws_checkUsername try to list a void URL. If no WS error occurs, the credentials are fine
	function ws_checkUsername($username, $password)
	{
		$connection_params = array("user" => $username, "pass" => $password);
		$info = new SOAP_client($GLOBALS["webservices_uri"], false, false, $connection_params);

		$op_xml = "<request operationName=\"list\"><resourceDescriptor name=\"\" wsType=\"folder\" uriString=\"\" isNew=\"false\">".
		"<label></label></resourceDescriptor></request>";

		$params = array("request" => $op_xml );
		$response = $info->call("list",$params,array('namespace' => $GLOBALS["namespace"]));

		return $response;
	}

	function ws_list($uri, $args = array())
	{
		global $_SESSION;

		$connection_params = array("user" => $_SESSION["username"], "pass" => $_SESSION["password"]);
		$info = new SOAP_client($GLOBALS["webservices_uri"], false, false, $connection_params);

		$op_xml = "<request operationName=\"list\">";

		if (is_array ($args))
		{
			$keys = array_keys($args);
			foreach ($keys AS $key)
			{
				$op_xml .="<argument name=\"$key\">".$args[$key]."</argument>";
			}
		}

		$op_xml .="<resourceDescriptor name=\"$uri\" wsType=\"folder\" uriString=\"$uri\" isNew=\"false\">".
		"<label></label></resourceDescriptor></request>";

		$params = array("request" => $op_xml );
		$response = $info->call("list",$params,array('namespace' => $GLOBALS["namespace"]));

		return $response;
	}

	function ws_get($uri, $args = array())
	{
		global $_SESSION;

		$connection_params = array("user" => $_SESSION["username"], "pass" => $_SESSION["password"]);
		$info = new SOAP_client($GLOBALS["webservices_uri"], false, false, $connection_params);

		$op_xml = "<request operationName=\"get\">";

		if (is_array ($args))
		{
			$keys = array_keys($args);
			foreach ($keys AS $key)
			{
				$op_xml .="<argument name=\"$key\">".$args[$key]."</argument>";
			}
		}

		$op_xml .= "<resourceDescriptor name=\"$uri\" wsType=\"reportUnit\" uriString=\"$uri\" isNew=\"false\">".
		"<label></label></resourceDescriptor></request>";

		$params = array("request" => $op_xml );
		$response = $info->call("get",$params,array('namespace' => $GLOBALS["namespace"]));

		return $response;
	}




	function ws_runReport($uri, $report_params, $output_params, &$attachments , $multiselect_params)
	{
		global $_SESSION;
		$max_execution_time = 120; // 2 mins.

		$connection_params = array("user" => $_SESSION["username"], "pass" => $_SESSION["password"], "timeout" => $max_execution_time);
		$info = new SOAP_client($GLOBALS["webservices_uri"], false, false, $connection_params);


	/*
	//$v =  new SOAP_Attachment('test','application/octet',"c:\client_file.png");
	//$methodValue = new SOAP_Value('request', 'this is my request', array($v));
	//$av=array($v);
	*/

		$op_xml = "<request operationName=\"runReport\">";

		if (is_array ($output_params))
		{
			$keys = array_keys($output_params);
			foreach ($keys AS $key)
			{
				$op_xml .="<argument name=\"$key\">".$output_params[$key]."</argument>\n";
			}
		}


		//$op_xml .="<argument name=\"USE_DIME_ATTACHMENTS\"><![CDATA[1]]></argument>\n";

		$op_xml .="<resourceDescriptor name=\"\" wsType=\"reportUnit\" uriString=\"$uri\" isNew=\"false\">\n".
		"<label></label>\n";


		// Add parameters...
		if (is_array ($report_params))
		{
			$keys = array_keys($report_params);
			foreach ($keys AS $key)
			{
				if (is_array($report_params[$key]) ) {
					// this is a multiselect param all the list item
					foreach ($report_params[$key] as $number => $value) {
						
						$op_xml .="<parameter name=\"$key\" isListItem=\"true\" ><![CDATA[".$value."]]></parameter>\n";
					}
					
				} else {
					$op_xml .="<parameter name=\"$key\"><![CDATA[".$report_params[$key]."]]></parameter>\n";
				}
			}
		}

		$op_xml .="</resourceDescriptor></request>";
		//return $op_xml;

		$params = array("request" => $op_xml );

		$response = $info->call("runReport",$params,array('namespace' => "http://www.jaspersoft.com/client"));

		$attachments = $info->_soap_transport->attachments;

		return $response;
	}



	// ********** XML related functions *******************
	function getOperationResult($operationResult)
	{
		$domDocument = new DOMDocument();
	 	$domDocument->loadXML($operationResult);

	 	$operationResultValues = array();

	 	foreach( $domDocument->childNodes AS $ChildNode )
	   	{
	       		if ( $ChildNode->nodeName != '#text' )
	       		{

	           		if ($ChildNode->nodeName == "operationResult")
	           		{
	           			foreach( $ChildNode->childNodes AS $ChildChildNode )
	   				{

	   					if ( $ChildChildNode->nodeName == 'returnCode' )
	       					{
	       						$operationResultValues['returnCode'] = $ChildChildNode->nodeValue;
	           				}
	           				else if ( $ChildChildNode->nodeName == 'returnMessage' )
	       					{
	       						$operationResultValues['returnMessage'] = $ChildChildNode->nodeValue;
	           				}
	           			}
	           		}
	           	}
	        }

	        return $operationResultValues;
	}


	function getResourceDescriptors($operationResult)
	{
		$domDocument = new DOMDocument();
	 	$domDocument->loadXML($operationResult);

	 	$folders = array();
	 	$count = 0;

	 	foreach( $domDocument->childNodes AS $ChildNode )
	   	{
	       		if ( $ChildNode->nodeName != '#text' )
	       		{

	           		if ($ChildNode->nodeName == "operationResult")
	           		{
	           			foreach( $ChildNode->childNodes AS $ChildChildNode )
	   				{

	   					if ( $ChildChildNode->nodeName == 'resourceDescriptor' )
	       					{
	       						$resourceDescriptor = readResourceDescriptor($ChildChildNode);
	   						$folders[ $count ] = $resourceDescriptor;
	           					$count++;
	           				}
	           			}
	           		}

	       		}
	   	}

	   	return $folders;
	}

	function readResourceDescriptor($node)
	{
		$resourceDescriptor = array();

		$resourceDescriptor['name'] = $node->getAttributeNode("name")->value;
	        $resourceDescriptor['uri'] =  $node->getAttributeNode("uriString")->value;
	        $resourceDescriptor['type'] = $node->getAttributeNode("wsType")->value;

		$resourceProperties = array();
		$subResources = array();
		$parameters = array();

		// Read subelements...
		foreach( $node->childNodes AS $ChildNode )
	   	{
	   		if ( $ChildNode->nodeName == 'label' )
			{
				$resourceDescriptor['label'] = 	$ChildNode->nodeValue;
			}
			else if ( $ChildNode->nodeName == 'description' )
			{
				$resourceDescriptor['description'] = 	$ChildNode->nodeValue;
			}
			else if ( $ChildNode->nodeName == 'creationDate' )
			{
				$resourceDescriptor['creationDate'] = 	$ChildNode->nodeValue / 1000;
			}
			else if ( $ChildNode->nodeName == 'resourceProperty' )
			{
				//$resourceDescriptor['resourceProperty'] = $ChildChildNode->nodeValue;
				// read properties...
				$resourceProperty =addReadResourceProperty($ChildNode );
				$resourceProperties[ $resourceProperty["name"] ] = $resourceProperty;
			}
			else if ( $ChildNode->nodeName == 'resourceDescriptor' )
			{
				array_push( $subResources, readResourceDescriptor($ChildNode));
			}
			else if ( $ChildNode->nodeName == 'parameter' )
			{
				$parameters[ $ChildNode->getAttributeNode("name")->value ] =  $ChildNode->nodeValue;
			}
		}

		$resourceDescriptor['properties'] = $resourceProperties;
		$resourceDescriptor['resources'] = $subResources;
		$resourceDescriptor['parameters'] = $parameters;


		return $resourceDescriptor;
	}

	function addReadResourceProperty($node)
	{
		$resourceProperty = array();

		$resourceProperty['name'] = $node->getAttributeNode("name")->value;

		$resourceProperties = array();

		// Read subelements...
		foreach( $node->childNodes AS $ChildNode )
	   	{
	   		if ( $ChildNode->nodeName == 'value' )
			{
				$resourceProperty['value'] = $ChildNode->nodeValue;
			}
			else if ( $ChildNode->nodeName == 'resourceProperty' )
			{
				//$resourceDescriptor['resourceProperty'] = $ChildChildNode->nodeValue;
				// read properties...
				array_push( $resourceProperties, addReadResourceProperty($ChildNode ) );
			}
		}

		$resourceProperty['properties'] = $resourceProperties;

		return $resourceProperty;
	}


	// Sample to put something on the server
	function ws_put()
	{
		global $_SESSION;

		$connection_params = array("user" => $_SESSION["username"], "pass" => $_SESSION["password"]);

		$fh = fopen("c:\\myimage.gif", "rb");
		$data = fread($fh, filesize("c:\\myimage.gif"));
		fclose($fh);

		$attachment = array("body"=>$data, "content_type"=>"application/octet-stream", "cid"=>"123456");

		$info = new SOAP_client($GLOBALS["webservices_uri"], false, false, $connection_params);
		$info->_options['attachments'] = 'Dime';
		$info->_attachments = array( $attachment );

		$op_xml = "<request operationName=\"put\">";
		$op_xml .="<resourceDescriptor name=\"JRLogo3\" wsType=\"img\" uriString=\"/images/JRLogo3\" isNew=\"true\"><label>JR logo PHP</label>";
		$op_xml .="<description>JR logo</description><resourceProperty name=\"PROP_HAS_DATA\"> <value><![CDATA[true]]></value></resourceProperty>";
		$op_xml .="<resourceProperty name=\"PROP_PARENT_FOLDER\"><value><![CDATA[/images]]></value></resourceProperty></resourceDescriptor></request>";

		$params = array("request" => $op_xml);
		$response = $info->call("put",$params,array('namespace' => $GLOBALS["namespace"]));

		return $response;

	}

?>
