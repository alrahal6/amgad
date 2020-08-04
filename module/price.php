<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

function calculatePrice($distance,$flag) {
    $np = 0;
    //$dist = value.distance;
    switch ($flag) {
        case "1":
            $np = getTaxiPrice($distance); 
            break;
        case "2":
            $np = getPoolPrice($distance);
            break;
        case "3":
            $np = 0; 
            break;
        case "4":
            $np = getTaxiPrice($distance); 
            break;
        case "5":
            $np = getPoolPrice($distance);
            break;
        case "6":
            $np = 0;
            break;
        default:
            $np = 0; 
    }
    /*if($distance > 3) {
        $np = ((($distance - 3) * 17.6) + 70 + 30 );
    } else {
        $np = 70 + 30;
    }*/ 
    return $np; 
}

function getTaxiPrice($distance) {
    $np = 0;
    if($distance > 3) {
        $np = ((($distance - 3) * 17.6) + 70 + 30 );
    } else {
        $np = 70 + 30;
    }
    return $np;
}

function getPoolPrice($distance) {
    return ($distance * 11) + 16.5; 
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