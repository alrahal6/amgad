<?php
$myfile = fopen("callLog.txt", "r") or die("Unable to open file!");
echo fread($myfile,filesize("callLog.txt"));
fclose($myfile);
?>