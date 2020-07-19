<?php
//$message = urlencode("YourOTP:9129");
//echo $message;

/*$msg = urlencode("carp&pwd=100472&smstext=YourOTP:9129&Sender=Carpoolee&Nums=249912356729");
echo $msg;*/

$url = 'http://212.0.129.229/bulksms/webacc.aspx?' . http_build_query([
    'user' => "carp",
    'pwd' => "100472",
    'smstext' => "YourOTP:9129",
    'Sender' => "Carpoolee",
    'Nums' => "249912356729"
]);

echo $url;