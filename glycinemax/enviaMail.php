<?php
/**
* 
*/
class enviaMail 
{
	private $repEmail;
	
	function __construct()
	{
		$this->repEmail='luciano.zapata314@gmail.com';
	}


	function enviaHtml($htnl,$subject, $to)
	{
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <'.$this->repEmail.'>' . "\r\n";
		$destino=implode(',', $to);
                return mail($destino,$headers, $subject, $htnl);
	}
	
}

?>