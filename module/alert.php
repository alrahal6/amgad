<?php 

//var_dump($argv);
require_once dirname(__DIR__).'/includes/DbOperations.php';
echo $argv[1]."\n";
echo $argv[2]."\n";
echo $argv[3]."\n";
echo $argv[4]."\n";

sendAlert();

function getDb() {
    return new mysqli("localhost","root","Prem@2512", "Amgad");
    //return $db->connect();
}

function sendAlert() {
    
    //$db = new DbOperations(); 
    
    //$db->sendNow();
    // @TODO
    // get matching captains who are requested for alert
    // send alert 

    $flag = 4;
    $fuser = 1;
    $users[] = 2;
    $d['mFlag'] = $flag;
    //$amount += (int) $d['price'];
    $token = getToken(2);
    //$token = "cX8giXqmGvU:APA91bHZWi70smGhT2U01qR23iDt9CyfvWzFmUetIsrYtuGRdSNM_MgRmmaxZKPjaqej3JGDJeOTXVlfMyS-l5q2RuV4dov6jAkwLMwkpH57SGjtWRb6ZMBT8-d5G0-0ASI-D6W-ZRb2";
    if($token) {
        push_notification_android($token,$d);
    }
    //$this->updateStatus($users,$fuser, $flag,1);
    //$this->saveNotification(implode(",", $users), $flag,$fuser);
    return true;
}

function getToken($userId) {
    $sql = "SELECT token FROM UserToken WHERE userId ='".$userId."'";
    $db = new mysqli("localhost","root","Prem@2512", "Amgad");
    if($result = mysqli_query($db, $sql)) {
        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return $row['token'];
        }
    }
    return 0;
}

function push_notification_android($token,$data) {
    $url = 'https://fcm.googleapis.com/fcm/send';
    $api_key = 'AAAACwnoBsk:APA91bHsnwtllsY0LZgtDhhrzzfOPiRGw231kJAPgWQdN2Q6DBjrtF80l7I358Z1Bkf4BYgl0_LUu_5ocZEm4WUdGqdL38kA88TXCJ7SlBR1_iv9_bgPo9Cbx81CXQS9LiqLdtpetmT4';
    $arrayToSend = array('to' => $token, 'data' => $data,'priority'=>'high');
    $headers = array(
        'Content-Type:application/json',
        'Authorization:key='.$api_key
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrayToSend));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
    }
    curl_close($ch);
    return $result;
}
?>