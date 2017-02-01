<?php
include_once('lib/connect_mysql.php');
include_once('lib/funciones.php');
$email=$_POST['email'];
$cuil=$_POST['cuil'];
$usuario=$_POST['usuario'];
$pass= md5($_POST['pass']);
$descripcion="afiliado - ".$cuil;
$afiliadoId= DevuelveValor($cuil, 'id', 'afiliados', 'cuil');


$sql="INSERT INTO `usuarios` VALUES (NULL, '$descripcion', '$usuario', '$pass', '$email', $afiliadoId, '0',2);";


mysql_query($sql);





$to = $email;
$subject = "Activacion de cuenta colaborador";
$message = "
<html>
<head>
<title>Activación cuenta afiliado</title>
</head>
<body>
<p>Haga click en el siguiente link para activar su cuenta</p>
<p>link: http://localhost/webs/glycinemax/index.php?cuil=$cuil</p>
</body>
</html>
";
// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <info@impulsodeunanuevavida.org>' . "\r\n";

mail($to,$subject,$message,$headers);
?>