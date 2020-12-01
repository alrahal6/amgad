<?php
require_once dirname(__DIR__).'/includes/DbOperations.php';

$response = array();

if($_SERVER['REQUEST_METHOD']=='POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if(isset($data['fUserId']) and isset($data['tUserId']))
    {
        $db = new DbOperations();
        if($db->sendPush($data['tUserId'],$data)) {
            $response = $data;
        }
    }
}
echo json_encode($response); 