<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';
//$response = array(); 
if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    //var_dump($data); 
    //var_dump($_POST);  
    //var_dump($_GET); 
    if(isset($data['fromLat']) and isset($data['fromLng'])) { 
        $db = new DbOperations(); 
        //var_dump($_REQUEST['lat']);
        //var_dump($_REQUEST['lng']); 
        $r = $db->getNearPassLst($data['fromLat'],$data['fromLng'],$data['nearTime']);
        if($r == 1) {
            $response = array (
                'success' => false,
                'cars' => 'nodriver'
            );
        }  else { 
            $response = $r;  
        } 
		//echo json_encode($array); 
	} else {
	    $response = array (
	        'success' => false,
	        'cars' => 'invalidarg'
	    );
	}
} else {
    $response = array (
        'success' => false,
        'cars' => 'notget'
    );
}  
echo json_encode($response); 