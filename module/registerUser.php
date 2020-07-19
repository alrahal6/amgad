<?php 
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array(); 

/*$message = urlencode("Scheduled at ".$new_centername." and ".$address." and ".$center_mobile);
$apiURL = "http://103.16.101.52:8080/bulksms/bulksms?username=abc-def&password=abc123&type=0&dlr=1&destination=".$conturyCode.$client_mobile."&source=ABC&message=".$message;
*/
function sendOtp($otp,$phone) {     
    $url = 'http://212.0.129.229/bulksms/webacc.aspx?' . http_build_query([
        'user' => "carp",
        'pwd' => "100472",
        'smstext' => "YourOTP:".$otp,
        'Sender' => "Carpoolee",
        'Nums' => "249".$phone
    ]);
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
 