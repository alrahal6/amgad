<?php 
//$row["formated_start_time"] = date("H:i",strtotime($row["start_time"]));
//$row["formated_end_time"] = date("H:i",strtotime($row["end_time"]));

$date = "10-03-2021 9:25:52 AM";
$today = new DateTime();
//$tDate = strtotime($today);
$date = strtotime($date);
echo date('H:i:s', $date);
echo "</br>";
$todayDt = date("Y-m-d"); 
echo $todayDt;
echo "</br>";
//echo date('Y-m-d', $tDate);
echo $today->format('Y-m-d H:i:s');
echo "</br>";
echo date("Y-m-d")." ".$today->format('H:i:s');