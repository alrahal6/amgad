<?php 
require_once '../includes/DbOperations.php';
//$response = array(); 
if($_SERVER['REQUEST_METHOD']=='POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['userId'])) { 
        //echo "Inside"; 
        $db = new DbOperations(); 
        $r = $db->getTripList($data['userId']);  
        if($r == 1) {
            $response['allTrips'][0] =
                array(
                    'pickupAddress' => "nan",
                    'distance'      => 0,
                    'duration'      => 0,
                    'price'         => 0
                );
        }  else { 
            $response = $r;   
        } 
    }
		//echo json_encode($array);
} else {
    $response['allTrips'][0] =
        array(
            'pickupAddress' => "nap",
            'distance'      => 0,
            'duration'      => 0,
            'price'         => 0
        );  
}
//$response['error'] = false;
//$response['id'] = 1;
//$response['phone'] = '0912394658';
echo json_encode($response); 