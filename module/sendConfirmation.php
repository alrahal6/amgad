<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST') {
    $data = json_decode(file_get_contents('php://input'), true); 
    var_dump($data);
    //if(isset($data[0]))
		//{ 
		$db = new DbOperations();
		//$db->logToDb("sd","1233","asd");
		//$db->saveNotification("hello","2");
		if($db->sendConfirmation($data)) {
		    $response = $data[0]; 
		}
	//} 
} 
echo json_encode($response);  