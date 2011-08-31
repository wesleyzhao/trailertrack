<?php
$address=$_GET['address'];
$address = urlencode($address); 
$url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$address."&sensor=false";  
$data = file_get_contents($url);      
print $data;
?>