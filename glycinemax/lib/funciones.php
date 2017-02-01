<?php
####################################################  LIBRER�A DE FUNCIONES  ############################################
#  .lib/funciones.php
#  Contiene las funciones necesarias para trabajar con la base de datos del sistema de stock
#  Se trata de una librer�a GPL.
#  Autor: Walter R. Elias  Copyright 2005 - Oro Verde Digital.
#########################################################################################################################

function MuestraPost(){
    
foreach($_POST as $nombre_campo => $valor){
   $dato  = "\$" . $nombre_campo . "='" . $valor . "';";
   $asignacion  .= $dato."<BR>";
} 
return $asignacion;
}



function sin_acentos($string){
	return strtr($string,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ',
'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
}


	function date_transform_usa($fecha_lat){
            //Parseo el $value para ver si es una fecha.
            $value = str_replace("'",'',$fecha_lat);


            
            $guion1 = substr($value, 2,-7);
            $guion2 = substr($value, 5,-4);
            $largo  = strlen($value);

            $verificador = $guion1.$largo.$guion2;

            if (($verificador == '-10-')) {
                
                $dia = substr($value, 0, 2);
                $mes = substr($value, 3, 2);
                $ano = substr($value, 6, 4);
                $value = "'".$ano . '-' . $mes . '-' . $dia."'";

            }
            $value = str_replace("'",'',$value);

            return $value;                        
        }
        
        /**
	* Transforma la fecua
	*
	* string $fecha_lat		// Fecha en formato latino
	* 
	*
	*/
	function date_transform_lat($fecha_usa){
            //Parseo el $value para ver si es una fecha.
            $value = str_replace("'",'',$fecha_usa);
            $guion1 = substr($value, 4,-5);
            $guion2 = substr($value, 7,-2);
            $largo  = strlen($value);

            $verificador = $guion1.$largo.$guion2;

            if (($verificador == '-10-')) {
                
                $dia = substr($value, 8, 2);
                $mes = substr($value, 5, 2);
                $ano = substr($value, 0, 4);
                $value = "'".$dia . '-' . $mes . '-' . $ano."'";

            }
            $value = str_replace("'",'',$value);
            return $value;                        
        }        
           
  
    //calculo los dias de vencimiento y dependiendo de los que quedan, cambio el color de fondo de la celda
  function suma_fechas($fecha,$ndias)
            
{
            
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
            
              list($ano,$mes,$dia)=split("-", $fecha);
            
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
            
              list($ano,$mes,$dia)=split("-",$fecha);
        $nueva = mktime(0,0,0, $mes,$dia,$ano) + $ndias * 24 * 60 * 60;
        $nuevafecha=date("Y-m-d",$nueva);
            
      return ($nuevafecha);  
            
}


/******************************************************/
/* Funcion paginar
 * actual:          Pagina actual
 * total:           Total de registros
 * por_pagina:      Registros por pagina
 * enlace:          Texto del enlace
 * Devuelve un texto que representa la paginacion
 */
function paginar($actual, $total, $por_pagina, $enlace) {
  $total_paginas = ceil($total/$por_pagina);
  $anterior = $actual - 1;
  $posterior = $actual + 1;
  if ($actual>1)
    $texto = "<a href=\"$enlace$anterior\">&laquo;</a> ";
  else
    $texto = "<b>&laquo;</b> ";
  for ($i=1; $i<$actual; $i++)
    $texto .= "<a href=\"$enlace$i\">$i</a> ";
  $texto .= "<b>$actual</b> ";
  for ($i=$actual+1; $i<=$total_paginas; $i++)
    $texto .= "<a href=\"$enlace$i\">$i</a> ";
  if ($actual<$total_paginas)
    $texto .= "<a href=\"$enlace$posterior\">&raquo;</a>";
  else
    $texto .= "<b>&raquo;</b>";
  return $texto;
}


include_once("connect_mysql.php");

function abecedario($enlace){
  $html = "";
  for ($i="A";$i!="AA";$i++)
  {
    $html.= "<a href=\"$enlace$i\">$i</a> ";
  }
  return $html;
}

function VerificaRegla($dato,$id,$campo_id,$tabla)
{

if (!empty($dato))
	{
	//Cuento el total de registros y creo el id_
	$contar="SELECT MAX($campo_id) FROM $tabla";
	$resultado=mysql_query($contar);
	$total_registros=mysql_fetch_array($resultado); //Cuenta el total de registros de la tabla categoria.
	$id=($total_registros[0]+1);  //al total le suma uno (este ser� el id del nueva categoria)



	//Guardo los datos en la tabla categoria
	$sql = "INSERT INTO $tabla VALUES ('$id','$dato')";
	mysql_query($sql);
		}
return($id);
}
//----------------------------------------------------------------------------------------------------------------------

// Funci�n que busca un valor de un dato en la tabla, recupera el ID y lo devuelve.
// $evento es el nombre del campo y $variable es el valor. $id_evento es el nombre del campo ID.

function DevuelveId($id_evento,$evento,$variable) {  //Recibe el nombre de los campos id y el valor y la variable que corresponde a la condici�n.
$query1 = mysql_query("SELECT $id_evento FROM $evento WHERE $evento='$variable'");
list($id_evento)=mysql_fetch_row($query1);
return $id_evento;
}

//----------------------------------------------------------------------------------------------------------------------


function DevuelveIdUsuario($variable) {
$query1 = mysql_query("SELECT ID FROM usuarios WHERE usuario='$variable'");
list($id_usuario)=mysql_fetch_row($query1);
return $id_usuario;
}


function DevuelveValor($id,$campo,$tabla,$id_campo) {
$query2=mysql_query("SELECT $campo FROM $tabla WHERE $id_campo='$id'");
list($resultado)=mysql_fetch_row($query2);
return $resultado;
}

function DevuelveComentario($campo,$tabla) {
$query2=mysql_query("SELECT column_comment FROM information_schema.COLUMNS WHERE table_name='$tabla' AND column_name = '$campo'");
list($resultado)=mysql_fetch_row($query2);
return $resultado;
}

function DevuelveValorDos($condicion1,$condicion2,$campo,$tabla,$c_condicion1,$c_condicion2) {
$query2=mysql_query("SELECT $campo FROM $tabla WHERE $c_condicion1='$condicion1' AND $c_condicion2='$condicion2'");
list($resultado)=mysql_fetch_row($query2);
return $resultado;
}

function DevuelveValorTres($condicion1,$condicion2,$condicion3,$campo,$tabla,$c_condicion1,$c_condicion2,$c_condicion3) {
$query2=mysql_query("SELECT $campo FROM $tabla WHERE $c_condicion1='$condicion1' AND $c_condicion2='$condicion2' AND $c_condicion3='$condicion3'");
list($resultado)=mysql_fetch_row($query2);
return $resultado;
}

function AsignarValor($variable) {
if ($variable="") $variable="0";
else $variable="1";
return $variable;
}

////////////////////////////////////////////
//USUARIOS ACTIVOS
//Calcula el numero de usuarios activos
////////////////////////////////////////////

function usuarios_activos()
{
   //permitimos el uso de la variable portadora del numero ip en nuestra funcion
   global $REMOTE_ADDR;

   //asignamos un nombre memotecnico a la variable
   $ip = $REMOTE_ADDR;
   //definimos el momento actual
   $ahora = time();

   //conectamos a la base de datos
   //Usad vuestros propios parametros!!
   $conn = mysql_connect($host,$user,$password);
   mysql_select_db($db,$conn);

   //actualizamos la tabla
   //borrando los registros de las ip inactivas (24 minutos)
   $limite = $ahora-24*60;
   $ssql = "delete from control_ip where fecha < ".$limite;
   mysql_query($ssql);

   //miramos si el ip del visitante existe en nuestra tabla
   $ssql = "select ip, fecha from control_ip where ip = '$ip'";
   $result = mysql_query($ssql);

   //si existe actualizamos el campo fecha
   if (mysql_num_rows($result) != 0) $ssql = "update control_ip set fecha = ".$ahora." where ip = '$ip'";
   //si no existe insertamos el registro correspondiente a la nueva sesion
   else $ssql = "insert into control_ip (ip, fecha) values ('$ip', $ahora)";

   //ejecutamos la sentencia sql
   mysql_query($ssql);

   //calculamos el numero de sesiones
   $ssql = "select ip from control_ip";
   $result = mysql_query($ssql);
   $usuarios = mysql_num_rows($result);

   //liberamos memoria
   mysql_free_result($result);

   //devolvemos el resultado
   return $usuarios;
}

function LimpiarXSS($variable){
$variable_limpia = htmlspecialchars($variable);
$variable_limpia = htmlentities($variable_limpia);
return $variable_limpia;
}

function formatearconsulta($consulta, $titulos)
{
$html   = "";
$i      = 0;
$j      = 0;
$campos = mysql_num_fields($consulta);
$html   = '<table border="0"><tr>';
if (empty($titulos))
{
	while ($i < mysql_num_fields($consulta))
	{
		$meta = mysql_fetch_field($consulta, $i);
		if (!$meta)
		{
			echo "No hay informacion disponible <br />\n";
			exit;
		}
		$html.='<td><strong>'.$meta->name.'</strong></td>';
		$i++;
	}
	$html .= '</tr>';
}
else;
{
	for($i=0;$i<sizeof($titulos); $i++)
	{
		$html.='<td><strong>'.$titulos[$i].'</strong></td>';
	}
$html .= '</tr>';
}

while($row=mysql_fetch_array($consulta,MYSQL_BOTH))
{
        if ($j & 1) $html .= '<tr bgcolor="lightGray">'; else $html .= '<tr bgcolor="white">';
        for ($i=0;$i<=$campos;$i++)
        {
        $html.='<td>'.$row[$i].'</td>';
        }
        $html.='</tr>';
        $j++;
}
$html.='</table>';
return $html;
}

function formatearconsultaABM($consulta, $titulos){
$html   = "";
$i      = 0;
$j      = 0;
$campos = mysql_num_fields($consulta);
$html   = '<table border="0"><tr>';

if (empty($titulos))
{
	while ($i < mysql_num_fields($consulta))
	{
		$meta = mysql_fetch_field($consulta, $i);
		if (!$meta)
		{
			echo "No hay informacion disponible <br />\n";
			exit;
		}
		$html.='<td><strong>'.$meta->name.'</strong></td>';
		$i++;
	}
	$html .= '</tr>';
}
else;
{
	for($i=0;$i<sizeof($titulos);$i++)
	{
		$html.='<td><strong>'.$titulos[$i].'</strong></td>';
	}
$html .= '</tr>';
}
while($row=mysql_fetch_array($consulta,MYSQL_BOTH))
{
        if ($j & 1) $html .= '<tr bgcolor="lightGray">'; else $html .= '<tr bgcolor="white">';
        for ($i=0;$i<=$campos;$i++)
        {
        $html.='<td>'.$row[$i].'</td>';
        }
	$html.='<td><a href="formulario_alta_usuario.php?id='.$row[0].'".>Editar</a></td>';
        $html.='<td><a href="formulario_borrar_usuario.php?id='.$row[0].'">Borrar</a></td>';
        $j++;
}
$html.='</table>';
return $html;
}





function Cerrar()
{
mysql_close();
}
?>