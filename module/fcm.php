<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['userId']) and isset($data['token'])){
		$db = new DbOperations(); 
		if($tok = $db->saveToken($data['userId'],$data['token'])) {
		    //$user = $db->getUserByPhone($_POST['phone']);
		    $response = $data;
		    /*$response['userName'] = $tok['userName']; 
		    $response['id'] = $data['id'];
			$response['phone'] = $user['phone'];
			$response['password'] = "";
			$response['vehicleType'] = $user['vehicleType'];*/ 
			//$db->insertLogin($user['id']);
		} 
	} 
} 
echo json_encode($response);  