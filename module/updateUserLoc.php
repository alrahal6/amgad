<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') {
	if(
		isset($_POST['id']) and 
	    isset($_POST['lat']) and
	    isset($_POST['lng']) and 
	    isset($_POST['ipAddr']))
		{ 
		$db = new DbOperations(); 
		$result = $db->updateUserLoc($_POST['id'],$_POST['lat'],$_POST['lng'],$_POST['ipAddr']); 
		/*if($result == 1){
			$response['error'] = false; 
			$response['message'] = "Location Updated successfully";
		}elseif($result == 2){
			$response['error'] = true; 
			$response['message'] = "Some error occurred please try again";			
		}elseif($result == 0){
			$response['error'] = true; 
			$response['message'] = "User not exist";						
		}*/
		
		if($result == 1){
		    $response['error'] = false;
		    $response['message'] = "Location Updated successfully";
		}elseif($result == 2){
		    $response['error'] = true;
		    $response['message'] = "Some error occurred please try again";
		}

	}else{
		$response['error'] = true; 
		$response['message'] = "Required fields are missing";
	}
} else {
	$response['error'] = true; 
	$response['message'] = "Invalid Request";
}
echo json_encode($response); 