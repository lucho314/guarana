<?php
/* require("aut_verifica.inc.php");
  $nivel_acceso=3;
  if ($nivel_acceso <= $_SESSION['usuario_nivel']){
  header ("Location: empresas.php");
  exit;
  } */

$tip = '';

include_once('html_sup.php');
include_once('lib/funciones.php');
?>



<?php
################# TOMO LOS DATOS ASOCIADOS AL TIPO DE REPORTE DE LA 
################# TABLA CORRESPONDIENTE Y LOS ASIGNO A SENDOS ARRAY
$tipo_de_reporte = $_POST['tipo_de_reporte'];
$tipo_de_reporte_id = $_POST['tipo_de_reporte_id'];

################  CONSULTO EN LA DB LOS CAMPOS DEL REPORTE
# Explicación: En la tabla campos_reportes guardo el nombre de la tabla a reportar,
# la cantidad de campos (esto me permitirá obtener los nombres de los campos), 
# la cantidad de tipos (Este corresponde a la cantidad de SQL que tendré cargados
# en la tabla resultado_reportes, los tipos de campo (que son los textos que se
# muestran en el formulario para elegir el tipo de reporte), los campos de posibles 
# reportes (menos los de fecha), los sql de los select correspondientes a cada campo,
# una bandera (con_campo) que si está en 1 indica que se deben mostrar campos fechas en
# el formulario y los casos, que son los javascript para activar o desactivar los campos

$sql_rep = "SELECT * FROM campos_reportes WHERE descripcion = '$tipo_de_reporte'";
$q_rep = mysql_query($sql_rep);

################  RECORRO EL ARRAY Y ASIGNO LOS VALORES A LAS VARIABLES.
while ($r_rep = mysql_fetch_array($q_rep)) {
    $id = $r_rep['id'];
    $descripcion = $r_rep['descripcion'];
    $cantidad_campos = $r_rep['cantidad_campos'];
    $cantidad_tipos = $r_rep['cantidad_tipos'];
    $tipo[1] = $r_rep['tipo_1'];
    $tipo[2] = $r_rep['tipo_2'];
    $tipo[3] = $r_rep['tipo_3'];
    $tipo[4] = $r_rep['tipo_4'];
    $tipo[5] = $r_rep['tipo_5'];
    $tipo[6] = $r_rep['tipo_6'];
    $tipo[7] = $r_rep['tipo_7'];
    $tipo[8] = $r_rep['tipo_8'];
    $tipo[9] = $r_rep['tipo_9'];
    $tipo[10] = $r_rep['tipo_10'];
    $campo[1] = $r_rep['campo_1'];
    $campo[2] = $r_rep['campo_2'];
    $campo[3] = $r_rep['campo_3'];
    $campo[4] = $r_rep['campo_4'];
    $campo[5] = $r_rep['campo_5'];
    $campo[6] = $r_rep['campo_6'];
    $campo[7] = $r_rep['campo_7'];
    $campo[8] = $r_rep['campo_8'];
    $campo[9] = $r_rep['campo_9'];
    $campo[10] = $r_rep['campo_10'];
    $sql[1] = $r_rep['sql_1'];
    $sql[2] = $r_rep['sql_2'];
    $sql[3] = $r_rep['sql_3'];
    $sql[4] = $r_rep['sql_4'];
    $sql[5] = $r_rep['sql_5'];
    $sql[6] = $r_rep['sql_6'];
    $sql[7] = $r_rep['sql_7'];
    $sql[8] = $r_rep['sql_8'];
    $sql[9] = $r_rep['sql_9'];
    $sql[10] = $r_rep['sql_10'];
    $con_fecha = $r_rep['con_fecha'];
    $caso[1] = $r_rep['caso_1'];
    $caso[2] = $r_rep['caso_2'];
    $caso[3] = $r_rep['caso_3'];
    $caso[4] = $r_rep['caso_4'];
    $caso[5] = $r_rep['caso_5'];
    $caso[6] = $r_rep['caso_6'];
    $caso[7] = $r_rep['caso_7'];
    $caso[8] = $r_rep['caso_8'];
    $caso[9] = $r_rep['caso_9'];
    $caso[10] = $r_rep['caso_10'];
}

################  CONSULTO EN LA DB LOS CAMPOS DEL RESULTADO DE REPORTE
# Explicación: En la tabla resultado_reportes se guardan las consultas SQL para 
# cada tipo de reporte y el nombre de la tabla a reportar. 

$sql_res = "SELECT * FROM resultado_reportes WHERE campos_reporte_id = '$id'";
$q_res = mysql_query($sql_res);

################  RECORRO EL ARRAY Y ASIGNO LOS VALORES A LAS VARIABLES.
# Explicación: Para poder utilizar las variables y que sea genérico, las asigno a 
# elementos de un array. De esa forma, y usando "eval" puedo hacer que las variables
# contenidas en la cadena se comporten como tales
while ($r_res = mysql_fetch_array($q_res)) {
    $id_res = $r_res['id'];
    $desc_res_c = $r_res['descripcion'];
    $campos_a_mostrar = $r_res['campos_a_mostrar'];
    $sql_r[1] = $r_res['sql_1'];
    $sql_r[2] = $r_res['sql_2'];
    $sql_r[3] = $r_res['sql_3'];
    $sql_r[4] = $r_res['sql_4'];
    $sql_r[5] = $r_res['sql_5'];
    $sql_r[6] = $r_res['sql_6'];
    $sql_r[7] = $r_res['sql_7'];
    $sql_r[8] = $r_res['sql_8'];
    $sql_r[9] = $r_res['sql_9'];
    $sql_r[10] = $r_res['sql_10'];
}


#################  TOMO LAS VARIABLES DE FECHA 
# (si no hay, quedara vacia)
$fecha_inicio = LimpiarXSS($_POST['fecha_inicio']);
$fecha_fin = LimpiarXSS($_POST['fecha_fin']);


#################  TRANSFORMO LAS FECHAS A FORMATO USA 
# Explicación: en la base de datos las fechas se guardan con formato aaaa-mm-dd
# y el usuario las maneja con formato dd-mm-aaaa. Lo que hace esta función es
# transformar las variables para que estén en el formato entendible por el usuario.

$fecha_inicio = date_transform_usa($fecha_inicio);
$fecha_fin = date_transform_usa($fecha_fin);

#################  TOMO EL RESTO DE LAS VARIABLES
# Explicación: Tomo los valores de las variables que vienen desde el formulario
# anterior. Como a priori no conozco el nombre de las variables, las guardo en 
# un array. Si además el nombre termina en _id, tomo el valor de la descripcion
# para ese campo y lo asigno en otro array con el mismo orden.

for ($i = 1; $i <= $cantidad_campos; $i++) {
    $auxiliar = $campo[$i];
    $variable[$i] = $_POST["$auxiliar"];
    $posterior = substr($auxiliar, -3);
    $cadena = $auxiliar;
    $largo_pre = strlen($cadena) - strlen($posterior);
    $desc_camp = substr($cadena, 0, $largo_pre);
    $nombre_tabla = $desc_camp . 's';
    if ($posterior == '_id') {
        $descr[$i] = DevuelveValor($variable[$i], 'descripcion', $nombre_tabla, 'id');
    } else {
        $descr[$i] = $variable[$i];
    }
//    echo $i." - ".$auxiliar.": ".$variable[$i]." Descripcion: ".$descr[$i]."<br>";
}

################# NOMBRES DE LOS CAMPOS
# Los nombres de los campos se guardan, separados por coma, en el campo campos_a_mostrar
# de la tabla resultado_reportes. Con el comando explode, separo los campos y los
# asigno a los elementos de un array.
$columnas_visibles = explode(",", $campos_a_mostrar);

################# NRO ELEMENTOS ARRAY
# Como después debo recorrer el array para generar los nombres de los campos y 
# mostrar los valores, cuento la cantidad de "columnas_visibles"
$cant_columnas_visibles = count($columnas_visibles);


############Obtengo las columnas sumables de la tabla########
#En el sql_10 se encuentra el numero de columna que se suman
#separadas por coma (,)

$columnas_sumables = explode(',', $sql_r[10]);
echo "<script> columnas_sumables=" . json_encode($columnas_sumables) . ";</script>";


#################  DEFINO ALGUNOS TEXTOS PARA MOSTRAR
$rango = "<br> desde el $fecha_inicio hasta el $fecha_fin";

################# ASIGNO EL WHERE DE LA CONSULTA
# Según tipo_de_reporte_id, tomo el dato del array y le aplico la función eval
# para que los códigos que empiecen con $ sean considerados variables.

$where = $sql_r[$tipo_de_reporte_id];
eval("\$where = \"$where\";");

$head = "
<html>
        <head>
        <title>
            Reporte
        </title>
       
        </head>
<body bgcolor=\"#ffffff\">
<div style=\"margin:30px;\">
";

$html .= "<table border=\"1\" class=\"table table-striped table-responsive\" id=\"tabla\"> <thead>";
$html .= "<tr>";


################# NOMBRES DE COLUMNA
# Recorro el array con los nombres de las columnas y genero el html de las mismas

for ($i = 0; $i <= $cant_columnas_visibles - 1; $i++) {
    $columna_mostrar = strtoupper($columnas_visibles[$i]);
    $largo_cad = strlen($columna_mostrar) - 2;
    $desc_col = substr($columna_mostrar, 2, $largo_cad);
    $desc_col = str_replace('_', ' ', $desc_col);

    $html .= "<td align=\"center\"><b>$desc_col</b></td>";
}

$html .= "</tr> </thead> </tbody>";


################ NOMBRE DE TABLA
# Verifico si el nombre de tabla termina en _detalles. Si es así, planteo una 
# consulta INNER JOIN para tomar datos de maestro


$pos = substr($descripcion, -9);
$caden = $descripcion;
$largo_pr = strlen($caden) - strlen($pos);
$desc_cam = substr($caden, 0, $largo_pr);

if ($pos == '_detalles') {

    $tabla_maestro = $desc_cam . '_maestros';

    $sql = "SELECT DISTINCT $campos_a_mostrar "
            . "FROM `$desc_res_c` as a "
            . "INNER JOIN $tabla_maestro as b "
            . " $where ";
} else {

############### CONSULTA SQL
# Realizo la consulta SQL usando el WHERE del array y 
    $sql = "SELECT $campos_a_mostrar "
            . "FROM `$desc_res_c` as a "
            . " $where ";
}




//echo $sql;

$result = mysql_query($sql) or trigger_error(mysql_error());
while ($row = mysql_fetch_array($result)) {

    foreach ($row AS $key => $value) {
        $row[$key] = stripslashes($value);
    }

    $html .= "<tr>";


    for ($c = 0; $c <= $cant_columnas_visibles - 1; $c++) {

        $auxiliar = $columnas_visibles[$c];
        //echo "campo: ".$auxiliar."<br>";
        $posterior = substr($auxiliar, -3);
        $cadena = $auxiliar;
        $largo_pre = strlen($cadena) - strlen($posterior);
        $desc_camp = substr($cadena, 0, $largo_pre);

        //echo $posterior.' - '.$cadena.' - '.$largo_pre.' - '.$desc_camp.'<br>'; 






        if ($posterior == '_id') {
            $largo_pos = strlen($cadena) - 5;
            $desc_cam = substr($cadena, 2, $largo_pos);
            $nombre_tabla = $desc_cam . 's';
            $descr[$c] = DevuelveValor($row[$c], 'descripcion', $nombre_tabla, 'id');
        } else {
            $nombre_tabla = $desc_camp . 's';
            $descr[$c] = $row[$c];
        }


        $html .= "<td valign='top' align='center'>" . $descr[$c] . "</td>";
    }

    $html .= "</tr>";
}
if ($tipo_de_reporte !== 'medios') {
    $html.="<tr><td>TOTAL</td>";
    for ($i = 1; $i < $cant_columnas_visibles; $i++) {
        $html.="<td></td>";
    }
    $html .= "</tr> ";
}
$html .= "</tbody></table>";

echo "<h2>$titulo</h2><br><br>";
echo $html;

$foot = "</div></body></html>";
?>
<script type="text/javascript" language="JavaScript">
    $(document).ready(function () {

        $("#tabla").dataTable({
            "bSort": false,
            "bPaginate": false,
            "bFilter": false,
            dom: 'Bfrtip',
            buttons: [
               'pdf','excel'

            ],
            "language": {
                "url": "DataTables-1.10.12/media/Spanish.json"
            }
        });

    });
</script> 

<?php
include_once('html_inf.php');
?>
<script>

<?php if ($tipo_de_reporte !== 'medios'): ?>
    $(function () {

        for (var i = 0; i < columnas_sumables.length; i++)
        {
            SumarColumna('tabla', columnas_sumables[i]);
        }

    })
<?php    endif;?>
    function SumarColumna(grilla, columna) {

        var resultVal = 0.0;

        $("#" + grilla + " tbody tr").not(':first').not(':last').each(
                function () {

                    var celdaValor = $(this).find('td:eq(' + columna + ')');

                    if (celdaValor.val() != null)
                        resultVal += parseFloat(celdaValor.html().replace(',', '.'));

                } //function

        ) //each

        // alert(resultVal.toFixed(2).toString().replace('.',','));

        $("#" + grilla + " tbody tr:last td:eq(" + columna + ")").html(resultVal.toFixed(2).toString().replace('.', ','));

    } 
</script>