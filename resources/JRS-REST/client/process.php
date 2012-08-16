<?php
	include('client/JasperClient.php');
	$jc = new Jasper\JasperClient('localhost', 8080, 'jasperadmin', 'jasperadmin');
	
	if(isset($_POST)) {
		print_r($_POST);
		switch($_POST['func']) {
			case 'create':
				createNewUser($_POST);
			default:
				echo "WHAT?";
				return;
		}
	}
	
	function createNewUser($data) {
		$newUser = new Jasper\User($data['username'], $data['password'], $data['email'], 'New User', 'organization_1', array('externallyDefined' => 'false', 'role' => 'ROLE_USER'), 'true', 'false');
		$jc->putUsers($newUser);
	}
?>