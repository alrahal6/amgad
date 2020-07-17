<?php
/*require("phpsqlsearch_dbinfo.php");*/
// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];
$radius = $_GET["radius"];
// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Opens a connection to a mySQL server
$mysqli = new mysqli("localhost", "root", "123456", "Amgad");
// Set the active mySQL database
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//echo $mysqli->real_escape_string($center_lat);
//exit; 
// Search the rows in the markers table
$query = sprintf("SELECT id, name, address, lat, lng, 
( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) 
+ sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance 
FROM markers HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20",
  $mysqli->real_escape_string($center_lat),
  $mysqli->real_escape_string($center_lng),
  $mysqli->real_escape_string($center_lat),
  $mysqli->real_escape_string($radius));
//var_dump($query);
//exit; 
$result = $mysqli->query($query);
//$result = mysql_query($query);
if (!$result) {
  die("Invalid query: " . mysql_error());
}
header("Content-type: text/xml");
// Iterate through the rows, adding XML nodes for each
while ($row = mysqli_fetch_assoc($result)){
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("id", $row['id']);
  $newnode->setAttribute("name", $row['name']);
  $newnode->setAttribute("address", $row['address']);
  $newnode->setAttribute("lat", $row['lat']);
  $newnode->setAttribute("lng", $row['lng']);
  $newnode->setAttribute("distance", $row['distance']);
}
echo $dom->saveXML();

$mysqli->close();

/*
 //Make sure that it is a POST request.
 if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
 throw new Exception('Request method must be POST!');
 }
 
 //Make sure that the content type of the POST request has been set to application/json
 $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
 if(strcasecmp($contentType, 'application/json') != 0){
 throw new Exception('Content type must be: application/json');
 }
 
 //Receive the RAW post data.
 $content = trim(file_get_contents("php://input"));
 
 //Attempt to decode the incoming RAW post data from JSON.
 $decoded = json_decode($content, true);
 
 //If json_decode failed, the JSON is invalid.
 if(!is_array($decoded)){
 throw new Exception('Received content contained invalid JSON!');
 }
 */








