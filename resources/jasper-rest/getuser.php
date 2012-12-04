<?php
	require('client/JasperClient.php');
	$jc = new Jasper\JasperClient('localhost', 8080, 'jasperadmin', 'jasperadmin', '/jasperserver-pro', 'organization_1');
	$users = $jc->getUsers();
	echo json_encode($users);
?>	