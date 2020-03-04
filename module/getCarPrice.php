<?php 
require_once '../includes/DbOperations.php';
//$response = array(); 
if($_SERVER['REQUEST_METHOD']=='POST') {

        $db = new DbOperations(); 
        $r = $db->getAllCarPrice(); 
        if($r == 1) {
            $response['allCarPrice'][0] =
            array(
                'baseFare'      => 0,
                'kmFare'        => 0,
                'minsFare'      => 0,
                'carName'       => "na",
                'numberOfSeats' => 0,
                'minDistance'   => 0
            );   
        }  else { 
            $response = $r;  
        } 
		//echo json_encode($array);
} else {
        $response['allCarPrice'][0] =
            array(
                'baseFare'      => 0,
                'kmFare'        => 0,
                'minsFare'      => 0,
                'carName'       => "na",
                'numberOfSeats' => 0,
                'minDistance'   => 0
            ); 
}
//$response['error'] = false;
//$response['id'] = 1;
//$response['phone'] = '0912394658';
echo json_encode($response);