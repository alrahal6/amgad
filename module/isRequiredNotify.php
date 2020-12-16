<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';
$response = array(); 
if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true); 
    //var_dump($data);
    //$response = array("tripId" => $data['tripId']);
    if(isset($data['userId'])) { 
        $db = new DbOperations(); 
        if($r = $db->isRequired($data)) { 
            $response = $data;  
        } 
    }
		//echo json_encode($array); 
}
echo json_encode($response); 