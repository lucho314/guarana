<?php

// PHP DB Control Panel
//
//Developed by: 	Humayun Shabbir Bhutta
//Email:			humayun_sa@hotmail.com
//website:			hm.munirbrothers.net
//Location:			Pakistan
//
function checklogin()
{
	session_start();
	if(!isset($_SESSION['adminok']))
		header("location: login.php");
}
?>
