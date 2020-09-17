<?php


//require dirname(__FILE__).'/storage/vendor/autoload.php';
//require dirname(__FILE__) . '/al-rahal6-953b14ab8110.json';


//use Google\Cloud\Storage\StorageClient; 

class DbOperations
{

    private $con;
    private $storage;
    
    private $CONF = 1; 
    private $CANCEL = 2;  
    private $STARTED = 3;  
    //private $bucket = "https://console.cloud.google.com/storage/browser/al-rahal6.appspot.com/";
    //private $bucket = "gs://al-rahal6.appspot.com/newfile.txt";
    //private $bucket = "";

    function __construct()
    {
        date_default_timezone_set('UTC');
        //echo  __DIR__ . '/al-rahal6-953b14ab8110.json';
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect(); 
        $this->con = $db->connect();
        
        //$this->storage->registerStreamWrapper();
    }
    
    public function createUser($name,$phone,$otp)
    { 
        $vehicle = "50";
        if ($res = $this->getUserByPhone($phone)) {
            //var_dump($res["id"]); 
            $updt = $this->con->prepare("UPDATE `Users` 
            SET `userName` = ?, `phone` = ? ,`password` = ? ,`vehicleType` = ? 
            WHERE `Users`.`id` = ?");
            $updt->bind_param("sissi",$name,$phone,$otp,$vehicle,$res["id"]);
            //$stmt->
            if ($updt->execute()) {
                return $res["id"]; 
            }
            return null; 
            //} else {
            //  return 2;
            //}
            //}
            //return $res["id"]; 
        } else {
            $i = NULL;
            //$password = md5($pass);
            $stmt = $this->con->prepare("INSERT INTO `Users` (`id`,`userName`,`phone`,`password`,`vehicleType`) 
                VALUES (?, ?, ?, ?,?);");
            $stmt->bind_param("ssiss",$i,$name, $phone, $otp,$vehicle);  
            if ($stmt->execute()) {
                return $stmt->insert_id; 
            } else {
                return null;
            }
        }
    }
    
    private function push_notification_android($token,$data) {
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
    
    public function sendPush($userId,$data) {
        $token = $this->getToken($userId);
        if($token) {
            $this->push_notification_android($token,$data);
        } else {
            return false;
        }
        //return true;
    }
    
    public function updateStatus($users,$fuser,$flag)
    {
        // todo update status
        $sql = "update `UserPosts`
         set status = ".$flag." , captainId = ".$fuser." where userId in (".implode(',',$users).") ";
         //$this->removeUserLoc($id);
         if(mysqli_query($this->con, $sql)) {
             return true;
         }
         return null;
    }
    
    public function logToDb($users,$phone,$trip) {
        $i = NULL;
        /*$stmt = $this->con->prepare("INSERT INTO `Notifications` 
(`id`, `users`, `phone`, `tripFlag`, `savedDtTime`)
                VALUES (?, ?, ?, ?,?);");
        $stmt->bind_param("sssss",$i,$users, $phone, $trip,now());
        if ($stmt->execute()) {
            return $stmt->insert_id;
        } else {
            return null;
        }*/
        //echo "ssd";
        $sql = "INSERT INTO `Notifications` (`id`, `users`, `phone`, `fromUser`, `savedDtTime`)
                VALUES (null, '".$users."', '".$phone."','".$trip."',now())";
        //echo $sql;
        //exit;
        if(mysqli_query($this->con, $sql)) {
            return true;
        } else {
            return null;
        }
    }
    
    public function logCall($userId,$phone,$tripId)
    {
        //$a = $userId." - ".$phone." - ".$tripId." - ".date("Y-m-d h:i:sa");
        return $this->logToDb($userId, $phone, $tripId);
        //return true; 
    }
    
    private function saveNotification($users,$flag,$fuser) {
       // $a = $users." - ".$flag." - ".date("Y-m-d h:i:sa");
        return $this->logToDb($users, $fuser, $flag);
        //file_put_contents("newfile.txt", $a);
        //return true;
    }
    
    public function sendGeneral($data,$flag) {
        $users = array();
        $amount = 0;
        foreach ($data as $d) {
            $fuser = $d['fUserId']; 
            $users[] = $d['tUserId']; 
            $d['mFlag'] = $flag;
            $amount += (int) $d['price'];
            $token = $this->getToken($d['tUserId']);
            if($token) {
                $this->push_notification_android($token,$d);
            }
        }
        $this->updateStatus($users,$fuser, $flag);
        $this->saveNotification(implode(",", $users), $flag,$fuser);
        if($flag == "13") {
            $this->deductComission($fuser,$amount,3);
        }
        return true; 
    }
    
    private function deductComission($user,$amount,$comission) {
        $amt = ($amount * $comission)/100;
        $sql = "UPDATE `Users` SET `vehicleType` = vehicleType - ".$amt." WHERE `Users`.`id` = '".$user."' ";
        /* $sql = "UPDATE `DriverCurrentLocation` SET  `ipAddr` = '".$ipAddr."', `lat` = '".$lat."', `lng` = '".$lng."'
                WHERE `DriverCurrentLocation`.`userId` = '".$id."'"; */
        if(mysqli_query($this->con, $sql)) {
            return 1;
        } else {
            return 2;
        }
        
    }
    
    public function sendStarted($data) {
        return $this->sendGeneral($data,"12");
    }
    
    public function sendCompleted($data) {
        return $this->sendGeneral($data,"13");
    }
    
    public function sendCancelled($data) {
        return $this->sendGeneral($data,"8");
    }
    
    public function sendPassengerancelled($data) {
        return true; 
        //return false;
        //return $this->sendGeneral($data,"4");
    }
    
    public function sendConfirmation($data) {
       return $this->sendGeneral($data,"6");
    }
    
    private function isAvailable($id) {
        $sql = "SELECT id FROM UserPosts WHERE userId = '".$id."' and status = 0 ";
        if($result = mysqli_query($this->con, $sql)) {
            if(mysqli_num_rows($result) > 0) {
                //return mysqli_fetch_assoc($result);
                return true;
            }
            return false;
        }
        return false; 
    } 
    
    public function checkBeforeConfirm($data) { 
        foreach ($data as $d) {
            if(!$this->isAvailable($d['tUserId'])) {
                return null;       
            }
        }
        return true; 
    }
    
    public function getToken($userId) { 
        $sql = "SELECT token FROM UserToken WHERE userId ='".$userId."'";
        if($result = mysqli_query($this->con, $sql)) {
            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                return $row['token'];
            }
        }
        return 0; 
    }
    
    public function getUserByPhone($phone)
    {
        $sql = "SELECT id FROM Users WHERE phone ='".$phone."'";
         if($result = mysqli_query($this->con, $sql)) {
             if(mysqli_num_rows($result) > 0) {
                 return mysqli_fetch_assoc($result);
             }
         }
         return 0; 
    } 
    
    public function saveToken($userId,$token)
    {
        $sql = "DELETE from `UserToken`
                WHERE `UserToken`.`userId` = '".$userId."'";
        //$this->removeUserLoc($id);
        if(mysqli_query($this->con, $sql)) {
            $i = "";
            $stmt = $this->con->prepare("INSERT INTO `UserToken` 
             (`id`,`userId`,`token`) VALUES (?, ?, ?);");
            $stmt->bind_param("iss",$i,$userId, $token); 
            if ($stmt->execute()) {
                return $stmt->insert_id;
            } 
            return null; 
        } else {
            return null;
        }
    }
     
    public function isValidUser($phone,$pass)
    {
        $sql = "SELECT * FROM Users WHERE phone ='".$phone."' and password = '".$pass."'";
        if($result = mysqli_query($this->con, $sql)) {
            if(mysqli_num_rows($result) > 0) {
                return mysqli_fetch_assoc($result);
            }
        }
        return 0; 
        /*$stmt = $this->con->prepare("SELECT id FROM users WHERE phone = ? and password = ?");
        $stmt->bind_param("ii", $phone,$pass);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;*/
    }
    
    public function insertUserLoc($id)
    {
        $lat = 0.0;
        $lng = 0.0;
        if ($this->isDriverIdExist($id)) {
            return 0;
        } else {
            //$password = md5($pass);
            $sql = "INSERT INTO `DriverCurrentLocation` (`userId`, `lat`, `lng`)
                VALUES ('".$id."', '".$lat."', '".$lng."')";
            //echo $sql;
            //exit;
            if(mysqli_query($this->con, $sql)) {
                return 1;
            } else {
                return 2;
            }
            /*$stmt = $this->con->prepare("INSERT INTO `DriverCurrentLocation` (`userId`,`lat`,`lng`) VALUES (NULL, ?, ?, ?, ?)");
            $stmt->bind_param("idd",$id,$lat,$lng);
            //$stmt->
            if ($stmt->execute()) {
                return 1;
            } else {
                return 2;
            }*/
        }
    }
    
    public function insertPassengerLoc($id,$lat,$lng)
    {
        $sql = "INSERT INTO `PassengerPickupLocation` (`id`,`userId`, `lat`, `lng`) 
                VALUES (NULL, '".$id."', '".$lat."', '".$lng."')";
        //echo $sql;
        //exit; 
        if(mysqli_query($this->con, $sql)){
            return 1;
        } else {
            return 2; 
        }
        //$stmt = $this->con->prepare("INSERT INTO `PassengerPickupLocation` (`id`,`userId`, `lat`, `lng`) VALUES (NULL, ?, ?, ?)");
        //$stmt->bind_param("idd",$id,$lat,$lng);
        //$stmt->
        //if ($stmt->execute()) {
            //return 1;
        //} else {
            //return 2;
        //}
        //}
    }
    
    public function updateUserLoc($id,$lat,$lng,$ipAddr)
    {
        $sql = "UPDATE `DriverCurrentLocation` SET  `ipAddr` = '".$ipAddr."', `lat` = '".$lat."', `lng` = '".$lng."' 
                WHERE `DriverCurrentLocation`.`userId` = '".$id."'";
        if(mysqli_query($this->con, $sql)){
            return 1;
        } else {
            return 2;
        }
           // $stmt = $this->con->prepare("UPDATE `DriverCurrentLocation` SET `lat` = ?, `lng` = ? WHERE `DriverCurrentLocation`.`id` = ?");
            //$stmt->bind_param("ddi",$lat,$lng,$id); 
            //$stmt->
            //if ($stmt->execute()) {
              //  return 1;
            //} else {
              //  return 2;
            //}
        //}
    }
    
    public function createNewTrip($userId,$lat,$lng,$driverId,$pickupAddress,$destLat,$destLng,
        $destAddress,$distance,$duration,$price)
    {   
        $mySqlI = $this->con;
        $sql = "INSERT INTO `Trip` (`id`, `customerId`, `pickupLat`, `pickupLng`, 
               `pickupAddress`, `dropLat`, `dropLng`, `dropAddress`, `distance`, `duration`, 
               `price`, `driverId`, `requestTime`, `pickupTime`, `startTime`, `endTime`) 
                VALUES (NULL, '".$userId."', '".$lat."', '".$lng."', 
                '".$pickupAddress."','".$destLat."','".$destLng."','".$destAddress."',
                '".$distance."','".$duration."','".$price."', 
                '".$driverId."', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 
                CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)"; 
        if(mysqli_query($mySqlI, $sql)){
            return mysqli_insert_id($mySqlI); 
            // return 1;
        } else {
            return 0; 
        }  
    }
    
    public function updateIsOnReq($userId,$reqRes,$tripId)
    {
        //$mySqlI = $this->con; 
        $upd = "UPDATE `DriverTripRequest` SET  `requestResponseId` = '".$reqRes."'
                WHERE `driverId` = '".$userId."' and `tripId` = '".$tripId."' ";
        mysqli_query($this->con, $upd); 
        if($reqRes == 13 || $reqRes == 2) {
            $sql = "UPDATE `DriverCurrentLocation` SET  `isOnRequest` = '0'
                WHERE `DriverCurrentLocation`.`userId` = '".$userId."'";
            if(mysqli_query($this->con, $sql)) {
                return 1;
            } else {
                return 2;
            }
        }
        return 1; 
        
    }
    
    public function updateDriverBack($userId)
    {
        $sql = "UPDATE `DriverCurrentLocation` SET  `isOnRequest` = '0'
                WHERE `DriverCurrentLocation`.`userId` = '".$userId."'";
        if(mysqli_query($this->con, $sql)) {
            return 1;
        } else {
            return 2;
        }
        return 2;
    }
    
    private function getDriverBack($userId) {
        $sql = "UPDATE `DriverCurrentLocation` SET  `isOnRequest` = '0'
                WHERE `DriverCurrentLocation`.`userId` = '".$userId."'";
        if(mysqli_query($this->con, $sql)) {
            return 1;
        } else {
            return 2;
        }
    }
    
    public function updateIsOnReqPass($userId,$reqRes,$tripId)
    {
        return 1; 
        $mySqlI = $this->con; 
        $upd = "UPDATE `DriverTripRequest` SET  `requestResponseId` = '".$reqRes."'
                WHERE `driverId` = '".$userId."' and `tripId` = '".$tripId."' ";
        mysqli_query($mySqlI, $upd);
        $sql = "UPDATE `DriverCurrentLocation` SET  `isOnRequest` = '0'
                WHERE `DriverCurrentLocation`.`userId` = '".$userId."'";
        if(mysqli_query($mySqlI, $sql)) {
            return 1;
        } else {
            return 2; 
        }
        
    }
    
    public function removeUserLoc($id)
    {
        $sql = "DELETE from `DriverCurrentLocation` 
                WHERE `DriverCurrentLocation`.`userId` = '".$id."'"; 
        if(mysqli_query($this->con, $sql)) {
            return 1;
        } else {
            return 2;
        }
        // $stmt = $this->con->prepare("UPDATE `DriverCurrentLocation` SET `lat` = ?, `lng` = ? WHERE `DriverCurrentLocation`.`id` = ?");
        //$stmt->bind_param("ddi",$lat,$lng,$id);
        //$stmt->
        //if ($stmt->execute()) {
        //  return 1;
            //} else {
            //  return 2;
            //}
            //}
    }
    
    public function removeUserLogin($id)
    {
        $sql = "DELETE from `UserLogin`
                WHERE `UserLogin`.`userId` = '".$id."'";
        $this->removeUserLoc($id); 
        if(mysqli_query($this->con, $sql)) {
            return 1;
        } else {
            return 2;
        }
        // $stmt = $this->con->prepare("UPDATE `DriverCurrentLocation` SET `lat` = ?, `lng` = ? WHERE `DriverCurrentLocation`.`id` = ?");
        //$stmt->bind_param("ddi",$lat,$lng,$id);
        //$stmt->
        //if ($stmt->execute()) {
        //  return 1;
        //} else {
        //  return 2;
            //}
            //}
    }
        
    public function userLogin($phone,$pass,$isDriver = 0)
    {
        //echo "ok";
        //exit; 
        //echo $phone." - ".$pass; 
        //$password = md5($pass); 
        $mysqli = $this->con;
        $mysqli->begin_transaction();
        
        $query = sprintf("SELECT id,vehicleType FROM Users WHERE phone = '%s' AND password = '%s' and id 
         not in (select userId from UserLogin d inner join Users u on u.id  = d.userId 
         where u.phone = '%s' AND u.password = '%s' )",
            $mysqli->real_escape_string($phone),
            $mysqli->real_escape_string($pass), 
            $mysqli->real_escape_string($phone),
            $mysqli->real_escape_string($pass)
         );
        //echo $query; 
        $result = $mysqli->query($query); 
        //$result = mysqli_query($mysqli, $query);
        //$row_cnt = $result->num_rows;
        
        //printf("Result set has %d rows.\n", $row_cnt);
        /*$stmt = $mysqli->prepare("SELECT id FROM Users WHERE phone = ? AND password = ? and id 
         not in (select userId from UserLogin d inner join Users u on u.id  = d.userId 
         where u.phone = ? AND u.password = ? )");
        $stmt->bind_param("isis",$phone,$pass,$phone,$pass); 
        $stmt->execute(); 
        $stmt->store_result(); */ 
        
        //var_dump($row);
        //return $stmt->num_rows > 0;
        //echo "ok upto here before";
        if ($result->num_rows > 0)  { 
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            //var_dump($row);
            $id = $row["id"]; 
            
            if($isDriver && $row["vehicleType"] == 0) {
                return 0;
            } else {
                //exit;
                /*while($row = $stmt->fetch_array()) {
                    $id = $row["id"];  
                }*/ 
                $sql = "INSERT INTO `UserLogin` (`userId`,`timeLoggedIn`)
                    VALUES ('".$id."', now())"; 
                //echo $sql;
                //exit;
                $mysqli->query($sql);
                //if(mysqli_query($mysqli, $sql)){
                $mysqli->commit();
                return 1; 
            }
                //return 1;
            //} 
            //$this->insertLogin($user['id']);
            //$mysqli->close();
        } else {
            $mysqli->rollback();
            //$mysqli->close();
            return 0;
        }
        return 1; 
    }
     
    public function insertLogin($id)
    {
        $sql = "INSERT INTO `UserLoginArchive` (`userId`,`timeLoggedIn`)
                VALUES ('".$id."', now())";
        //echo $sql;
        //exit;
        if(mysqli_query($this->con, $sql)){
            return 1;
        } else {
            return 2; 
        }
    }
    
    public function getMyHistory($userId,$phone) { 
        try {
            $mysqli = $this->con;
            $query = sprintf("SELECT id,userId,srcLat,srcLng,destLat, 
                destLng,tripDistance,startTime,endTime,sourceAddress,destinationAddress, 
                phone,seats,dropDownId,dropDownVal,price,selectorFlag,name, 
                status,notes FROM UserPostsHistory where
                    userId = '%s' 
                    ORDER BY id ASC",
                $mysqli->real_escape_string($userId)
                );
            //echo $query;
            $result = $mysqli->query($query);
            if (!$result) {
                return 1;
            }
            if($result->num_rows == 0) {
                return 1;
            }
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $array[$i++] = array(                    
                    "fUserId" => $row['userId'],
                    "tUserId" => $row['userId'],
                    "mFlag" => $row['status'],
                    "tripId" => $row['id'],
                    "distance" => $row['tripDistance'],
                    "price" => $row['price'],
                    "mTripTime" => (new DateTime($row['startTime']))->format('c'),
                    "phone" => $row['phone'],
                    "name" => $row['name'],
                    "fAddress" => $row['sourceAddress'],
                    "tAddress" => $row['destinationAddress'],
                    "note" => $row['notes'],
                    "fLat" => $row['srcLat'],
                    "fLng" => $row['srcLng'],
                    "tLat" => $row['destLat'],
                    "tLng" => $row['destLng']
                );
                
            }
            $mysqli->close();
            return $array;
            //return $response;
        } catch (Exception $e) {
            //$mysqli->rollback();
            return 1;
        }
    }
    
    public function getMyCurrent($userId,$phone) {
        try {
            $mysqli = $this->con;
            $query = sprintf("SELECT u.id,userId,srcLat,srcLng,destLat, 
            destLng,tripDistance,startTime,endTime,sourceAddress,destinationAddress, 
            u.phone,seats,dropDownId,dropDownVal,price,selectorFlag,name, 
            status,notes,s.phone as cphone FROM UserPosts u left join Users s on s.id = u.captainId               
            where userId = '%s' ORDER BY u.id DESC LIMIT 1",
                $mysqli->real_escape_string($userId)
            );
            //echo $query;
            //exit;
            $result = $mysqli->query($query);
            if (!$result) {
                return 1;
            }
            if($result->num_rows == 0) {
                return 1;
            }
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $array = array(
                    "fUserId" => $row['userId'],
                    "tUserId" => $row['userId'],
                    "mFlag" => $row['status'],
                    "tripId" => $row['id'],
                    "distance" => $row['tripDistance'],
                    "price" => $row['price'],
                    "mTripTime" => (new DateTime($row['startTime']))->format('c'),
                    "phone" => $row['phone'],
                    "name" => $row['name'],
                    "fAddress" => $row['sourceAddress'],
                    "tAddress" => $row['destinationAddress'],
                    "note" => $row['notes'],
                    "fLat" => $row['srcLat'],
                    "fLng" => $row['srcLng'],
                    "tLat" => $row['destLat'],
                    "tLng" => $row['destLng'],
                    "captain" => $row['cphone']
                );
                
            }
            $mysqli->close();
            return $array;
            //return $response;
        } catch (Exception $e) {
            //$mysqli->rollback();
            return 1;
        }
    }
    
    public function getMyAccount($userId,$phone) {
        //return 100;
        try {
            $mysqli = $this->con;
            $query = sprintf("SELECT * FROM Users where
                    phone = '%s'
                    ORDER BY id DESC LIMIT 1",
                $mysqli->real_escape_string($phone)
                );
            //echo $query;
            $result = $mysqli->query($query);
            if (!$result) {
                return 1;
            }
            if($result->num_rows == 0) {
                return 1;
            }
            $i = 0;
            //$row = mysqli_fetch_row($result);
            while ($row = mysqli_fetch_assoc($result)) {
                $array = array(
                    "phone" => $row['phone'],
                    "amount" => $row['vehicleType']
                );
            }
            $mysqli->close();
            return $array;
            //return $response;
        } catch (Exception $e) {
            //$mysqli->rollback();
            return 1;
        }
    }
    
    public function getNearPassLst($pLat,$pLng,$nTime)
    {
        
        try {
            $mysqli = $this->con;
            $radius = 3;
            $query = sprintf("SELECT id,price,seats,name,dropDownVal,phone,
            userId,sourceAddress,destinationAddress,tripDistance,
            (6371 * ACOS(COS(RADIANS( '%s' )) * COS(RADIANS(srcLat)) * COS(RADIANS(srcLng) - RADIANS( '%s' ))
                + SIN(RADIANS( '%s' )) * SIN(RADIANS(srcLat)))) as distance,
                    startTime,
                    endTime,srcLat,srcLng,
                    destLat,destLng FROM UserPosts where
                    status = 0 and startTime >= '%s' HAVING distance <= '%s'
                    ORDER BY tripDistance DESC LIMIT 20",
                $mysqli->real_escape_string($pLat),
                $mysqli->real_escape_string($pLng),
                $mysqli->real_escape_string($pLat),
                $mysqli->real_escape_string($nTime),
                $mysqli->real_escape_string($radius)
                );
            //echo $query;
            $result = $mysqli->query($query);
            if (!$result) {
                return 1;
            }
            if($result->num_rows == 0) {
                return 1;
            }
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $array[$i++] = array(
                    "nearImage" => "0",
                    "nearAmount" => $row['price'],
                    "nearSeats" => $row['seats'],
                    "nearGender"=> $row['dropDownVal'],
                    "nearFrom" => $row['sourceAddress'],
                    "nearTo" => $row['destinationAddress'],
                    "nearDistance" => $row['tripDistance'],
                    "nearTime" => date_format(date_create($row['startTime']),"d-M-Y H:i"),
                    "fromLat" => $row['srcLat'],
                    "fromLng" => $row['srcLng'],
                    "toLat" => $row['destLat'],
                    "toLng" => $row['destLng']
                );
                
            }
            $mysqli->close();
            return $array;
            /*$response = array (
                'success' => true,
                'passengers' => $array
            ); */
            return $response;
        } catch (Exception $e) {
            //$mysqli->rollback();
            return 1;
        }
    }
    
    public function getNearDriversLst($pLat,$pLng)
    {
         
        try {
            $mysqli = $this->con;
            //$mysqli->begin_transaction();
            $radius = 2;
            $query = sprintf("SELECT userId, lat, lng,
                ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') )
                + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance
                FROM DriverCurrentLocation d
                inner join Users u on u.id = d.userId
                inner join VehicleType v on v.id = u.vehicleType
                where isTerminated = 0 and isOnRequest = 0 
                HAVING distance < '%s' ORDER BY distance LIMIT 0 , 10",
                $mysqli->real_escape_string($pLat),
                $mysqli->real_escape_string($pLng),
                $mysqli->real_escape_string($pLat),
                $mysqli->real_escape_string($radius)
                );
            $result = $mysqli->query($query);
            if (!$result) {
                return 1;
                //die("Invalid query: " . mysql_error());
            }
            if($result->num_rows == 0) {
                return 1;
            }
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                /*$ins = "INSERT INTO `DriverTripRequest` (`tripId`,`driverId`,`requestResponseId`)
                VALUES ('".$tripId."','".$row['userId']."','0')";
                mysqli_query($mysqli, $ins);
                // update selected
                $sql = "UPDATE `DriverCurrentLocation` SET  `isOnRequest` = '1'
                WHERE `DriverCurrentLocation`.`userId` = '".$row['userId']."'";
                mysqli_query($mysqli, $sql);*/
                $array[$i++] =
                array(
                    'lat'     => $row['lat'],
                    'lon'     => $row['lng'],
                );
                
            }
            //$mysqli->commit();
            $mysqli->close();
            $response = array (
                'success' => true,
                'cars' =>
                array ( 0 =>
                    array ('name' => 'Amgad',
                        'icon' => 'https://amgad-sd.com',
                        'contacts' => NULL,
                        'drivers' => $array,
                    ),
                ),
            );  
            return $response;
        } catch (Exception $e) {
            //$mysqli->rollback();
            return 1;
        }
        /*
          array (
                            0 => array ('lat' => 15.555051,'lon' => 32.572834,),
                            1 => array ('lat' => 15.561705,'lon' => 32.571156,),
                            2 => array ('lat' => 15.556979,'lon' => 32.566174,),
                            3 => array ('lat' => 15.549846,'lon' => 32.569576,),
                            4 => array ('lat' => 15.555420,'lon' => 32.576023,),
                            5 => array ('lat' => 15.560001,'lon' => 32.576374,),),
         */
    }
    /*reqData.lat,reqData.lng,reqData.lat,
     tableName,ar,radius
     $query = sprintf("SELECT userId, lat, lng,
     ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') )
     + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance
     FROM DriverCurrentLocation d
     inner join Users u on u.id = d.userId
     inner join VehicleType v on v.id = u.vehicleType
     where isTerminated = 0 and isOnRequest = 0 and typeName = '%s' and
     userId not in %s HAVING distance < '%s' ORDER BY distance LIMIT 0 , 1",*/
    public function getNearDrivers($userId,$pLat,$pLng,$distance) 
    {   
        try {
            $array = array();
            $mysqli = $this->con;
            $mysqli->begin_transaction();
            $radius = 13;
            $myQuery = sprintf("SELECT userId, lat, lng,
                ( 6371 * acos( cos( radians( '%s' ) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians( '%s' ) ) 
                + sin( radians( '%s' ) ) * sin( radians( lat ) ) ) ) AS distance 
                FROM DriverCurrentLocation d 
                inner join Users u on u.id = d.userId 
                inner join VehicleType v on v.id = u.vehicleType  
                where isTerminated = 0 and isOnRequest = 0 and  
                userId != %s  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 10",
                $mysqli->real_escape_string($pLat),
                $mysqli->real_escape_string($pLng),
                $mysqli->real_escape_string($pLat),
                $mysqli->real_escape_string($userId),
                $mysqli->real_escape_string($radius)
            ); 
            //echo $query; 
            //$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            //$txt = "John Doe\n";
            //fwrite($myfile, $query);
            //$txt = "Jane Doe\n";
            //fwrite($myfile, $txt);
            //fclose($myfile);
            //var_dump($myQuery);
            //exit;
            $result = $mysqli->query($myQuery);
            if (!$result) {
                return 1;
                //die("Invalid query: " . mysql_error());
            }
            if($result->num_rows == 0) {
                return 1;
            }
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) { 
                /*$ins = "INSERT INTO `DriverTripRequest` (`tripId`,`driverId`,`requestResponseId`)
                VALUES ('".$tripId."','".$row['userId']."','0')";
                mysqli_query($mysqli, $ins);  
                // update selected  
                $sql = "UPDATE `DriverCurrentLocation` SET  `isOnRequest` = '1' 
                WHERE `DriverCurrentLocation`.`userId` = '".$row['userId']."'";
                mysqli_query($mysqli, $sql); */
                $array[$i++] =
                array( 
                    'userId'   => $row['userId'],
                    'distance' => $row['distance'],
                    'lat'      => $row['lat'],
                    'lng'      => $row['lng'],
                );
            }
            $mysqli->commit();
            $mysqli->close();
            return $array; 
        } catch (Exception $e) { 
            $mysqli->rollback(); 
            return 1; 
        }
    }
    
    public function createPost($data)
    {
        try {
            $array = array();
            $startTime = new DateTime($data['startTime']);
            $today = new DateTime();
            $status = 0;
            //$now = $today->format('Y-m-d H:i:s');
            // PASSENGER_TAXI_ONLY = 1;
            // PASSENGER_SHARE_ONLY = 2;
            // PASSENGER_ANY = 3;
            // CAPTAIN_TAXI_ONLY = 4;
            // CAPTAIN_SHARE_ONLY = 5;
            // CAPTAIN_ANY = 6;
            $mysqli = $this->con;
            $mysqli->begin_transaction();
            if($data['selectorFlag'] == 5) {
                $data['selectorFlag'] = 2;
                $status = 1;
            }
            
            if($data['selectorFlag'] == 6) {
                $status = 1;
            }
            
            $sql = "INSERT INTO `UserPosts` (`id`, `userId`, `srcLat`, `srcLng`, `destLat`, 
            `destLng`, `tripDistance`, `startTime`, `endTime`, `sourceAddress`, `destinationAddress`, 
            `phone`, `seats`, `dropDownId`, `dropDownVal`, `price`, `selectorFlag`, `name`, `status`,`notes`)
             VALUES (NULL,'".$data['userId']."','".$data['srcLat']."','".$data['srcLng']."','".$data['destLat']."'
             ,'".$data['destLng']."','".$data['tripDistance']."','".$startTime->format('Y-m-d H:i:s')."','".$today->format('Y-m-d H:i:s')."'
             ,'".$data['sourceAddress']."','".$data['destinationAddress']."','".$data['phone']."','".$data['seats']."'
             ,'".$data['dropDownId']."','".$data['dropDownVal']."','".$data['price']."','".$data['selectorFlag']."'
             ,'".$data['name']."',".$status.",'".$data['notes']."')"; 
            //echo $sql;
            //exit;
            //$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            //$txt = "John Doe\n";
            //fwrite($myfile, $sql);
            //$txt = "Jane Doe\n";
            //fwrite($myfile, $txt);
            //fclose($myfile);
            if(mysqli_query($this->con, $sql)) {
                //return 1;
            $x = $data['tripDistance'];
            //"PT15M"
            $sDate = new DateTime($data['startTime']);
            $sDate->add(new DateInterval('PT30M'));
            $d = $sDate->format('Y-m-d H:i:s');
            //date("Y/m/d H:i:s", strtotime("+30 minutes", $t));
            $v = date('Y-m-d H:i:s',strtotime('-30 minutes',strtotime($data['startTime'])));
            //$eDate = new DateTime($data['startTime']);
            //$eDate->add(new DateInterval('PT60M'));
            //$v = $eDate->format('Y-m-d H:i:s');
            //$v = date("Y-m-d H:i:s",strtotime("-60 minutes",$data['startTime']));
            //$d = date("Y-m-d H:i:s",strtotime("+30 minutes",$data['startTime']));
            //echo $data['startTime'];
            //echo $v."<br/>";
            //echo $d."<br/>";
            $flg = $data['selectorFlag'];
            $selector = $flg;
            if($flg == 3) {
                $selector = 6;
            } else if($flg == 6) {
                $selector = 3;
            }
            $dd = $data['dropDownId']; 
            $distance = round($x * .4);
            $myQuery = sprintf(" SELECT id,price,seats,name,dropDownVal,phone,
            userId,sourceAddress,destinationAddress,tripDistance,
            (6371 * ACOS(COS(RADIANS( '%s' )) * COS(RADIANS(srcLat)) * COS(RADIANS(srcLng) - RADIANS( '%s' )) 
            + SIN(RADIANS( '%s' )) * SIN(RADIANS(srcLat)))) as srcDistDiff, 
            (6371 * ACOS(COS(RADIANS( '%s' )) * COS(RADIANS(destLat)) * COS(RADIANS(destLng) - RADIANS( '%s' )) 
            + SIN(RADIANS( '%s' )) * SIN(RADIANS(destLat)))) as destDistDiff,
            startTime,
            endTime,srcLat,srcLng,
            destLat,destLng FROM UserPosts where userId != %s and selectorFlag = %s and dropDownId = %s 
            and status = 0  and ( startTime > '%s' and startTime < '%s') HAVING (srcDistDiff < '%s' and destDistDiff < '%s') 
            ORDER BY tripDistance DESC LIMIT 20 ",
                $mysqli->real_escape_string($data['srcLat']), 
                $mysqli->real_escape_string($data['srcLng']),
                $mysqli->real_escape_string($data['srcLat']),
                $mysqli->real_escape_string($data['destLat']),
                $mysqli->real_escape_string($data['destLng']),
                $mysqli->real_escape_string($data['destLat']),
                $mysqli->real_escape_string($data['userId']),
                $mysqli->real_escape_string($selector),
                $mysqli->real_escape_string($dd),
                $mysqli->real_escape_string($v),
                $mysqli->real_escape_string($d),
                $mysqli->real_escape_string($distance),
                $mysqli->real_escape_string($distance)
            );  
            //echo $myQuery;
            //$myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
            //$txt = "John Doe\n";
            //fwrite($myfile, $sql);
            //$txt = "Jane Doe\n";
            //fwrite($myfile, $myQuery); 
            //fclose($myfile);
            //exit;
            $result = $mysqli->query($myQuery);
            $mysqli->commit();
            if (!$result) {
                return null;
                //die("Invalid query: " . mysql_error());
            }
            if($result->num_rows == 0) {
                return null;
            }
            $i = 0;
            //$row = mysqli_fetch_assoc($result);
            while ($row = mysqli_fetch_assoc($result)) {
                /*$ins = "INSERT INTO `DriverTripRequest` (`tripId`,`driverId`,`requestResponseId`)
                VALUES ('".$tripId."','".$row['userId']."','0')";
                mysqli_query($mysqli, $ins);
                // update selected
                $sql = "UPDATE `DriverCurrentLocation` SET  `isOnRequest` = '1'
                WHERE `DriverCurrentLocation`.`userId` = '".$row['userId']."'";
                mysqli_query($mysqli, $sql);*/
                //"startTime" => date_format(date_create($row['startTime']),"Y-m-d"),
                //"endTime" => date_format(date_create($row['endTime']),"Y-m-d"),
                
                
                $array[$i++] = array(
                    "id" => $row['id'],
                    "price" => $row['price'],
                    "seats" => $row['seats'],
                    "name" => $row['name'],
                    "dropDownVal"=> $row['dropDownVal'],
                    "phone" => $row['phone'],
                    "userId" => $row['userId'],
                    "sourceAddress" => $row['sourceAddress'],
                    "destinationAddress" => $row['destinationAddress'],
                    "tripDistance" => $row['tripDistance'],
                    "srcDistDiff" => $row['srcDistDiff'],
                    "destDistDiff" => $row['destDistDiff'],
                    "startTime" => (new DateTime($row['startTime']))->format('c'),
                    "endTime" => (new DateTime($row['endTime']))->format('c'),
                    "srcLat" => $row['srcLat'],
                    "srcLng" => $row['srcLng'],
                    "destLat" => $row['destLat'],
                    "destLng" => $row['destLng']
                );
            }
            
            $mysqli->close();
            //return $row;
            return $array;
            } else {
                return null;
            } 
        } catch (Exception $e) {
            $mysqli->rollback();
            return null;
        }
    }
    
    public function  getAllCarPrice()
    {
        try {
            $mysqli = $this->con;
            $query = "SELECT minDistance,numberOfSeats,VehicleTypeId,beseFare,minuteFare,typeName,kmFare
                     FROM `VehicleType` v inner join `Price` p 
                      on p.VehicleTypeId = v.id";
            $result = $mysqli->query($query);
            if (!$result) {
                return 1;
                //die("Invalid query: " . mysql_error());
            }
            if($result->num_rows == 0) {
                return 1;
            }
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) { 
                $array['allCarPrice'][$i++] =
                array(
                    'baseFare'      => $row['beseFare'],
                    'kmFare'        => $row['kmFare'],
                    'minsFare'      => $row['minuteFare'],
                    'carName'       => $row['typeName'],
                    'numberOfSeats' => $row['numberOfSeats'],
                    'minDistance'   => $row['minDistance']
                );
            }
            $mysqli->close(); 
            return $array;
        } catch (Exception $e) { 
            return 1;
        }
    }
    
    public function  getTripList($userId)
    {
        try {
            //$userId = 6; 
            $mysqli = $this->con;
            $query = "SELECT id,pickupAddress,distance,duration,price
                     FROM `Trip` where customerId = '".$userId."' "; 
            //echo $query; 
            $result = $mysqli->query($query); 
            if (!$result) {
                return 1;
                //die("Invalid query: " . mysql_error());
            }
            if($result->num_rows == 0) {
                return 1;
            }
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $array['allTrips'][$i++] =
                array(
                    'pickupAddress' => $row['pickupAddress'],
                    'distance'      => $row['distance'],
                    'duration'      => $row['duration'],
                    'price'         => $row['price']
                );
            }
            $mysqli->close();
            return $array;
        } catch (Exception $e) {
            return 1;
        }
    }
    
    public function insertDriverRequestBuffer($id)
    {
        $sql = "INSERT INTO `UserLoginArchive` (`userId`,`timeLoggedIn`)
                VALUES ('".$id."', now())";
        //echo $sql;
        //exit;
        if(mysqli_query($this->con, $sql)) {
            return 1;
        } else {
            return 2;
        } 
    }
    
    private function isUserExist($phone)
    {
        $stmt = $this->con->prepare("SELECT id FROM DriverCurrentLocation WHERE phone = ? ");
        $stmt->bind_param("i", $phone); 
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    private function isDriverIdExist($id)
    {
        $stmt = $this->con->prepare("SELECT userId FROM DriverCurrentLocation WHERE userId = ? ");
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}
