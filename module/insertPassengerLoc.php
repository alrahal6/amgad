<?php 
require_once '../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') { 
	if(
	    isset($_POST['id']) and 
	    isset($_POST['lat']) and
	    isset($_POST['lng']))
		{ 
		$db = new DbOperations(); 
		$result = $db->insertPassengerLoc($_POST['id'],$_POST['lat'],$_POST['lng']);
		if($result == 1){
		    $response['error'] = false;
		    $response['message'] = "Location Inserted successfully";
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
