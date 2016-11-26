<?php

include '../lib/variables.php';
$url = "$ip/controls/switches?submit=set&starter=1";

for($i=0;$i<50;$i++){
$page = file_get_contents($url);
}