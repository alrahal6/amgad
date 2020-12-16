<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';
$response = array(); 
if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true); 
    //var_dump($data);
    //$response = array("tripId" => $data['tripId']);
    if(isset($data['tripId']) and isset($data['newTime']) and isset($data['newSeats'])) { 
        $db = new DbOperations(); 
        if($r = $db->repeatRegular($data)) {
            $data["newTime"] = (new DateTime($data['newTime']))->format('c');
            $response = array($data); 
        } 
    }
		//echo json_encode($array); 
}
echo json_encode($response);