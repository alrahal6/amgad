<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';
$response = array(); 
//$isSuccessful = false;
if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    //var_dump($data);
    if(isset($data['userId']) and isset($data['srcLat']) and isset($data['srcLng'])
        and isset($data['destLat']) and isset($data['destLng']) 
        and isset($data['tripDistance']) and isset($data['startTime']) ) { 
        $db = new DbOperations(); 
        if($r = $db->createPost($data)) {
            $response = $r; 
            //$isSuccessful = true;
            if($data['selectorFlag'] != 5 && $data['selectorFlag'] != 6) { 
                $lat = $data['srcLat'];
                $lng = $data['srcLng'];
                $dLat = $data['destLat'];
                $dLng = $data['destLng'];
                exec("php alert.php $lat $lng $dLat $dLng > /dev/null &"); 
            }
        } 
    } 
		//echo json_encode($array);
}
echo json_encode($response);

/*$status = 0;
if($isSuccessful) {
    if($data['selectorFlag'] == 5) {
        $data['selectorFlag'] = 2;
        $status = 1;
    }
    
    if($data['selectorFlag'] == 6) {
        $status = 1;
    }
    if($status == 0) {
        $db->sendAlertNotification($data); 
    }
}*/