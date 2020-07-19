<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') {
    $data = json_decode(file_get_contents('php://input'), true); 
    if(isset($data[0])) { 
		$db = new DbOperations();
		if($db->checkBeforeConfirm($data)) { 
		    $response = array("message" => "Success","flag" => 1);
		} else {
		    $response = array("message" => "Already users confirmed by other captain","flag" => 2);
		}
	}
} 
echo json_encode($response);  