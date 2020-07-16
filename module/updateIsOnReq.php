<?php 
require_once '../includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') {
	if(isset($_POST['id']) and isset($_POST['reqRes']) and isset($_POST['tripId']))
		{ 
		$db = new DbOperations(); 
		$result = $db->updateIsOnReq($_POST['id'],$_POST['reqRes'],$_POST['tripId']);   
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