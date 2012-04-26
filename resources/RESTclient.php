<?php
/**
 * @copyright Copyright (c) 2011
 * @author Mariano Luna
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
// Define Type Constants
	$jasperserver_url = "http://localhost:8080/jasperserver/services/";

	$jasperserver_url = "http://localhost:8080/jasperserver-pro/services/";

	$SCHEDULING_WS_URI =  $jasperserver_url . "ReportScheduler?wsdl";
        $webservices_uri = $jasperserver_url . "repository";

	$namespace = "http://www.jaspersoft.com/namespaces/php";


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
	
	$JS_IC_Type = array(
		1 => "IC_TYPE_BOOLEAN",
		2 => "IC_TYPE_SINGLE_VALUE",
		3 => "IC_TYPE_SINGLE_SELECT_LIST_OF_VALUES",
		4 => "IC_TYPE_SINGLE_SELECT_QUERY",
		5 => "IC_TYPE_MULTI_VALUE",
		6 => "IC_TYPE_MULTI_SELECT_LIST_OF_VALUES",
		7 => "IC_TYPE_MULTI_SELECT_QUERY",
	);

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
	define("RUN_OUTPUT_IMAGES_URI","IMAGES_URI");
	define("RUN_OUTPUT_PAGE","PAGE");

function RenderInputControl($ICResource, $dsUri) {				// Get Input Control resource
	global $JS_IC_Type;
	
	$html = '';
	$inputControlData = array();
	$inputControlData['NAME'] = (string) $ICResource['name'];
	$inputControlData['URI'] = (string) $ICResource['uriString'];
	foreach ($ICResource->resourceProperty as $inputControl) {
		switch ($inputControl['name']) {
			case 'PROP_INPUTCONTROL_TYPE':
				$inputControlData['type'] = $JS_IC_Type[(int) $inputControl->value];
			break;
			default:
				if (!empty($inputControl->value)) {
					$inputControlData[(string) $inputControl['name']] =  (string) $inputControl->value;
				} else {
					$inputControlData[(string) $inputControl['name']] = $inputControl;	
				}
		}
	}
	/*
	$screen .= "<hr> <strong>" . $contents->label . "</strong> (Input Control) <br> 
				- Type: ". $inputControlData['type'] . " <br>
				- Mandatory: ". $inputControlData['PROP_INPUTCONTROL_IS_MANDATORY'] . " <br>
				- Visible: ". $inputControlData['PROP_INPUTCONTROL_IS_VISIBLE'] . " <br>
				- URI: ". $inputControlData['URI'] . " <br>
				- (Note: this sample does not render input controls)";
	$screen .= "<hr><pre>" . htmlentities(print_r($inputControlData, true)) . "</pre><hr>";
	$screen .= "<hr><pre>" . htmlentities(print_r($contents->asXML(), true)) . "</pre><hr>";
	 * 
	 * 
	 */
	
	if ($inputControlData['PROP_INPUTCONTROL_IS_MANDATORY'] == 'true') {
		$mandatory = true;
		$attr = "";
	} else {
		$mandatory = false;
		$attr = "";
	}	
	
	$html .= "<hr> <strong>" . $ICResource->label . ": ";
	$html .= ($mandatory) ? "(*)" : "";
	$html .= "</strong> ";
	switch ($inputControlData['type']) {
		case 'IC_TYPE_SINGLE_SELECT_QUERY':
			$html .= makeSelectArray('PARAM_S_' . $inputControlData['NAME'], '', RestGetInputControl($inputControlData, $dsUri), "", $attr, !$mandatory);
			break;
		case 'IC_TYPE_BOOLEAN':
			$html .= '<input type="checkbox" value="true" name="PARAM_S_'. $inputControlData['NAME'] . '" >';
			break;
		case 'IC_TYPE_SINGLE_VALUE':
			$html .= '<input type="text" name="PARAM_S_'. $inputControlData['NAME'] . '" >';
			break;
		case 'IC_TYPE_MULTI_SELECT_QUERY':
			$html .= makeSelectArray('PARAM_M_' . $inputControlData['NAME'] . "[]", '', RestGetInputControl($inputControlData, $dsUri), "", $attr . " MULTIPLE");
			break;
		default:
			$html .= '<strong>Input Control Rendering for ' . $inputControlData['type'] . ' Not Implemented</strong>';
			break;
	}

	return $html;
}

function RestGetInputControl($inputControl, $DataSource) {
	//GET http://localhost:8080/jasperserver/rest/resource
	// /reports/samples/Cascading_multi_select_report_files/ 
	// Cascading_state_multi_select?IC_GET_QUERY_DATA=/datasources/JServerJNDIDS&PL_Country_multi_select=USA&PL_Country_multi_select=Mexico
	$result = array();
	$WSRest = new PestXML(JS_WS_URL);
	// Set auth Header
	$WSRest->curl_opts[CURLOPT_COOKIE] = $_SESSION["JSCookie"] ;
	try {		
		$resource = $WSRest->get('resource' . $inputControl['URI'] . '?IC_GET_QUERY_DATA=' . $DataSource );
		foreach ($resource->resourceProperty as $property) {
			if ($property['name'] == 'PROP_QUERY_DATA') {
				foreach ($property->resourceProperty as $querydata) {
					$display ='';
					foreach ($querydata->resourceProperty as $displaycolumns) {
						$display .= (string) $displaycolumns->value . ' ';
					}
					$result[(string) $querydata->value] = $display;
				}
			}
		}
	} catch (Exception $e) {
    	$result['ERROR'] =  "Exception: <pre>" .  $e->getMessage() . "</pre>";
	}

	return $result;
}
?>
