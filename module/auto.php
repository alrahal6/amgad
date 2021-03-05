<?php 

require_once dirname(__DIR__).'/includes/DbOperations.php';
$db = new DbOperations(); 
$db->getAndInsertAllPost(); 
// @todo 

// get current day

// get all list with relevent to day 


/* 
  SELECT `id`, `tripId`, `newTime`, `newPrice`, `entryTime`, 
  `dropDownId`, `dropDownVal`, `newSeats`, `sun`, `mon`, `tue`, 
  `wed`, `thu`, `fri`, `sat` FROM `RepeatRegular` WHERE 1
 */ 

// if day == sun 
// $today =  


// change start time and end time 

// insert into posts table 

