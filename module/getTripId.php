<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array();

if($_SERVER['REQUEST_METHOD']=='POST') {
    //var_dump($data);
    $data = json_decode(file_get_contents('php://input'), true);
    //var_dump($data);
    if(
        isset($data['userId']) and
        isset($data['lat']) and
        isset($data['lng']) and
        isset($data['driverId'])
      )
    {
        $db = new DbOperations();
        $result = $db->createNewTrip($data['userId'],$data['lat'],$data['lng'],
            $data['driverId'],$data['pickupAddress'],$data['destLat'],$data['destLng'],
            $data['destAddress'],$data['distance'],$data['duration'],$data['price']); 
        if($result > 0) {
            $response['error'] = false;
            $response['message'] = $result; 
        } else {
            $response['error'] = true;
            $response['message'] = "Some error occurred please try again";
        }
        
    } else {
        $response['error'] = true;
        $response['message'] = "Required fields are missing"; 
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid Request";
}
echo json_encode($response);  