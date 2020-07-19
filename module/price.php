<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

function calculatePrice($distance,$flag) {
    $np = 0;
    //$dist = value.distance;
    if($distance > 3) {
        $np = ((($distance - 3) * 17.6) + 70 + 30 );
    } else {
        $np = 70 + 30;
    }
    return $np; 
}

if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    //var_dump($data);
    if(isset($data['price']) and isset($data['distance']) and isset($data['flag'])) {
        $response['price'] = calculatePrice($data['distance'],$data['flag']);
        $response['distance'] = $data['distance'];
        $response['flag'] = $data['flag'];
	} 
	
} 
echo json_encode($response);  