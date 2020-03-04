<?php 
require_once '../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['phone']) and isset($data['password'])){
		$db = new DbOperations(); 
		if($user = $db->isValidUser($data['phone'], $data['password'])) {
		    //$user = $db->getUserByPhone($_POST['phone']);
		    $response['userName'] = $user['userName']; 
			$response['id'] = $user['id'];
			$response['phone'] = $user['phone'];
			$response['password'] = "";
			$response['vehicleType'] = $user['vehicleType'];
			//$db->insertLogin($user['id']);
		} else {
			//$response['error'] = true; 
			//$response['message'] = "Invalid Login!";			
		}

	} /*else {
		//$response['error'] = true; 
		//$response['message'] = "Required fields are missing";
	}*/
}

echo json_encode($response); 