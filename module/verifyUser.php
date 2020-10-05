<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['userId']) and isset($data['phone'])){
		//$db = new DbOperations(); 
		//if($user = $db->isValidUser($data['phone'], $data['password'])) {
		    //$user = $db->getUserByPhone($_POST['phone']);
		    
        /*if($data['userId'] == '1792') {
            $response['userPhone'] = $data['phone'];
            $response['userId'] = $data['userId'];
            $response['isAllowed'] = false;
            $response['message'] = "Blocked due to bad behaviour";
        }*/
		    
        if($data['selector'] == 0) {
            $response['userPhone'] = $data['phone'];
            $response['userId'] = $data['userId'];
            $response['isAllowed'] = false;
            $response['message'] = "Blocked due to non payment";
        } else {  
            if($data['userId'] != '1792') {
                $response = array(
                    'userPhone' => $data['phone'],
                    'userId' => $data['userId'],
                    'isAllowed' => true,
                    'message' => 'User Allowed'
                );
            }
        }
		    /*$response['userPhone'] = $data['phone']; 
			$response['userId'] = $data['userId'];
			$response['isAllowed'] = true;
			$response['message'] = "User Allowed";*/
			
			
			
			/*$response['userPhone'] = $data['userPhone'];
			$response['userId'] = $data['userId'];
			$response['isAllowed'] = false;
			$response['message'] = "Blocked due to non payment";*/
			
			
			//$response['password'] = "";
			//$response['vehicleType'] = $user['vehicleType'];
			//$db->insertLogin($user['id']);
		//} else {
			//$response['error'] = true; 
			//$response['message'] = "Invalid Login!";			
		//}

	} /*else {
		//$response['error'] = true; 
		//$response['message'] = "Required fields are missing";
	}*/
}

echo json_encode($response); 