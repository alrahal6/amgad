<?php 
require_once '../includes/DbOperations.php';

$response = array(); 

function sendOtp($otp,$phone) {
    $url = "http://212.0.129.229/bulksms/webacc.aspx?user=carp&pwd=100472&smstext=YourOTP:".$otp
    ."&Sender=Carpoolee&Nums=249".$phone;
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    $phoneList = curl_exec($cURLConnection);
    //curl_exec($cURLConnection);
    curl_close($cURLConnection);
    echo json_decode($phoneList);
}

function genOtp() {
    $generator = "1357902468";
    $result = "";
    for ($i = 1; $i <= 6; $i++) {
        $result .= substr($generator, (rand()%(strlen($generator))), 1);
    }
    return $result;
}

if($_SERVER['REQUEST_METHOD']=='POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $data['password'] = genOtp();
	if(isset($data['userName']) and isset($data['phone']))
		{ 
		$db = new DbOperations();
		$result = $db->createUser($data['userName'],$data['phone'],$data['password']); 
		if($result) {
		    $data['id'] = $result;
		    $response = $data; 
		    sendOtp($data['password'],$data['phone']); 
			//$response['error'] = false; 
			//$response['message'] = "User registered successfully";
		} elseif($result == null) {
			//$response['error'] = true; 
			//$response['message'] = "Some error occurred please try again";			
		} 
	} /*else {
		$response['error'] = true; 
		$response['message'] = "Required fields are missing";
	}*/
} /*else {
	$response['error'] = true; 
	$response['message'] = "Invalid Request";
}*/
echo json_encode($response); 
 