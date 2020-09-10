<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';
//$response = array(); 
if($_SERVER['REQUEST_METHOD']=='POST') { 
    $data = json_decode(file_get_contents('php://input'), true);
    //var_dump($data); 
    //var_dump($_POST);  
    //var_dump($_GET); 
    if(isset($data['userId']) and isset($data['phone'])) { 
        $db = new DbOperations(); 
        //var_dump($_REQUEST['lat']);
        //var_dump($_REQUEST['lng']); 
        $r = $db->getMyCurrent($data['userId'],$data['phone']);
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