<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') {
	if(isset($_POST['id']))
		{ 
		$db = new DbOperations(); 
		$result = $db->updateDriverBack($_POST['id']);   
		if($result == 1){
		    $response['error'] = false;
		    $response['message'] = "Status Updated successfully";
		} elseif($result == 2){
		    $response['error'] = true;
		    $response['message'] = "Some error occurred please try again";
		}

	} else{
		$response['error'] = true; 
		$response['message'] = "Required fields are missing";
	}
} else {
	$response['error'] = true; 
	$response['message'] = "Invalid Request";
}
echo json_encode($response); 