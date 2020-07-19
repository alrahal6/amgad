<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';
$response = array(); 
if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    //var_dump($data);
    if(isset($data['userId']) and isset($data['lat']) and isset($data['lng']) 
        and isset($data['distance']) ) { 
        $db = new DbOperations(); 
        $r = $db->getNearDrivers($data['userId'],$data['lat'],$data['lng'],$data['distance']);
        //echo "after response";
        //var_dump($response);
        if($r == 1) {
            //$response['error'] = true;
            //$response['message'] = "No Nearby Drivers found!";
            /*$response[0] =
            array(
                'userId' => '0',
                'distance' => '0',
                'lat' => '00.00',
                'lng' => '00.00', 
            );*/ 
        }  else { 
            $response = $r;  
        } 
		//echo json_encode($array); 
	} else {
	    /*$response[0] =
	    array(
	        'userId' => '0',
	        'distance' => '0',
	        'lat' => '00.00',
	        'lng' => '00.00',
	    );*/
	}
} /*else {
        $response[0] =
            array(
                
                'driverId' => '0',
                'distance' => '0',
                'lat' => '00.00',
                'lng' => '00.00',
            ); 
}*/ 
echo json_encode($response);