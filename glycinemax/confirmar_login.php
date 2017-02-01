<?php
    $lang = $_POST['lang'];
    
    if (!empty($lang)){
        setcookie("LangArabidopsis",$lang,time()+7776000);
    }else{
        $lang = $_COOKIE["LangArabidopsis"];
    }
    
    
require("aut_verifica.inc.php");
$nivel_acceso=3; // Nivel de acceso para esta p�gina.
// se chequea si el usuario tiene un nivel inferior
// al del nivel de acceso definido para esta p�gina.
// Si no es correcto, se mada a la p�gina que lo llamo con
// la variable de $error_login definida con el n� de error segun el array de
// aut_mensaje_error.inc.php
if ($nivel_acceso <= $_SESSION['usuario_nivel']){
header ("Location: index.php");
exit;
}
 //librerias a utilizar
 include_once("lib/connect_mysql.php");
 include_once("lib/funciones.php");
 //.....................

$usuario_nivel=$_SESSION['usuario_nivel'];
    
if ($usuario_nivel<=2) {header ("Location: menu_principal.php?mensaje=Se ha conectado correctamente al sistema.<br><br>");}
else header ("Location: index.php");
?>