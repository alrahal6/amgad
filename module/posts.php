<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';
$response = array(); 
if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    //var_dump($data);
    if(isset($data['userId']) and isset($data['srcLat']) and isset($data['srcLng'])
        and isset($data['destLat']) and isset($data['destLng']) 
        and isset($data['tripDistance']) and isset($data['startTime']) ) { 
        $db = new DbOperations(); 
        if($r = $db->createPost($data)) {
            $response = $r; 
        } 
    }
		//echo json_encode($array);
}
echo json_encode($response);