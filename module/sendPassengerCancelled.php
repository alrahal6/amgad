<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') {
    $data = json_decode(file_get_contents('php://input'), true); 
    if(isset($data[0]))
		{ 
		$db = new DbOperations();
		if($db->sendPassengerancelled($data)) {
		    $response = array(
		        'flag'    => 1,
		        'message' => "successfully updated"
		    );
		} else {
		    $response = array(
		        'flag'    => 2,
		        'message' => "Sorry! Unable to update"
		    );
		}
	} 
} 
echo json_encode($response); 