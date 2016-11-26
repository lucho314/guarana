<?php
include '../lib/variables.php';
$altura =$_POST['altura'];
$velocidad = $_POST['velocidad'];
$runway = $_POST['runway'];

$url = "$ip/controls/switches?submit=set&starter=1";
//$page = file_get_contents($url); 
file_get_contents($ip."position?submit=set&altitude-ft[0]=$altura"); 
file_get_contents($ip."velocities?submit=set&airspeed-kt==$velocidad"); 
file_get_contents($ip."sim/atc?submit=set&runway%5B0%5D=$runway");  
file_get_contents($ip."/sim/freeze?submit=set&clock=true");

