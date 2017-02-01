<?php
//require("aut_verifica.inc.php");
//$nivel_acceso=3;
//if ($nivel_acceso <= $_SESSION['usuario_nivel']){
//header ("Location: empresas.php");
//exit;
//}

$tip = '';

include_once('html_sup.php');

################  TOMO POR GET EL TIPO DE REPORTE (NOMBRE DE LA TABLA)
$tipo_de_reporte = $_GET['tipo_de_reporte'];
//echo "TIPO DE REPORTE: ".$tipo_de_reporte;
################  CONSULTO EN LA DB LOS CAMPOS DEL REPORTE
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
    //   $tipo[6]                 = $r_rep['tipo_6'];
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
    $sql_reporte[1] = $r_rep['sql_1'];
    $sql_reporte[2] = $r_rep['sql_2'];
    $sql_reporte[3] = $r_rep['sql_3'];
    $sql_reporte[4] = $r_rep['sql_4'];
    $sql_reporte[5] = $r_rep['sql_5'];
    $sql_reporte[6] = $r_rep['sql_6'];
    $sql_reporte[7] = $r_rep['sql_7'];
    $sql_reporte[8] = $r_rep['sql_8'];
    $sql_reporte[9] = $r_rep['sql_9'];
    $sql_reporte[10] = $r_rep['sql_10'];
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


##################  GENERO EL JAVASCRIPT QUE ACTIVARA O NO LOS CAMPOS
?>

<script type="text/javascript">

    function ActivarSelect()
    {
        var x = document.getElementById("tipo_de_reporte_id").selectedIndex;
        var y = document.getElementById("tipo_de_reporte_id").options;

        valor = y[x].value;

<?php
for ($i = 1; $i <= $cantidad_campos; $i++) {
    $varjs = "    var sel$i = document.getElementById(\"$campo[$i]\");\r\n";
    echo $varjs;
}
?>

        //Incializo los select

<?php
for ($i = 1; $i <= $cantidad_campos; $i++) {
    $varjs = "    sel$i.disabled = true;\r\n";
    echo $varjs;
}
?>


        switch (valor) {
<?php
for ($i = 1; $i <= $cantidad_tipos; $i++) {
    echo "case '$i':\r\n";
    echo $caso[$i] . "\r\n";
    echo "break;\r\n";
}
?>
            default:
<?php
for ($i = 1; $i <= $cantidad_campos; $i++) {
    $varjs = "        sel$i.disabled = true;\r\n";
    echo $varjs;
}
?>
        }

    }

</script>

<br>
<div class="panel panel-warning" style="width: 800px">
    <div class="panel-heading">
        <h3 class="panel-title">Elegir tipo de reporte y opciones de filtro</h3>
    </div>
    <div class="panel-body">
        <br>
        <form name="resumen" action="reportes_paso_2.php" method="post">
            <table table class="table table-bordered table-striped" width="500" border="0" cellpadding="10" cellspacing="0" bgcolor="#e0ffff">
                <tr>
                    <td align="right">Tipo de reporte</td>
                    <td align="left">
                        <select name="tipo_de_reporte_id" id="tipo_de_reporte_id" onchange="ActivarSelect();"> 
                            <option>Elija un tipo de reporte</option>
                            <?php
                            for ($i = 1; $i <= $cantidad_tipos; $i++) {
                                echo "\r\n              <option value=\"$i\">$tipo[$i]</option>";
                            }
                            ?>                 
                        </select>
                    </td>
                </tr> 

                <?php
                for ($i = 1; $i <= $cantidad_campos; $i++) {

                    ############# TOMO EL NOMBRE DEL CAMPO Y EL SUFIJO _id
                    $posterior = substr($campo[$i], -3);
                    $cadena = $campo[$i];
                    $largo_pre = strlen($cadena) - strlen($posterior);
                    $desc_camp = substr($cadena, 0, $largo_pre);
                    $desc_camp = ucwords(strtolower($desc_camp));


                    ############# Dependiendo del nombre del campo es lo que muestro
                    if ($campo[$i] == 'fecha_inicio') {
                        echo "    
                <tr>
                    <td align=\"right\">Seleccione fecha de inicio</td>
                    <td align=\"left\">
                        <input type=\"text\" name=\"fecha_inicio\" id=\"fecha_inicio\" class=\"datepicker\">                   
                    </td>
                </tr>";
                    } elseif ($campo[$i] == 'fecha_fin') {
                        echo "    
                <tr>
                    <td align=\"right\">Seleccione fecha de fin</td>
                    <td align=\"left\">
                    <input type=\"text\" name=\"fecha_fin\" id=\"fecha_fin\" class=\"datepicker\">    
                </td>
                </tr>";
                    } else {

                        echo "
            <tr>
                <td align=\"right\">
                $desc_camp: 
                </td>
                <td align=\"left\">
                        <select name=\"$campo[$i]\" id=\"$campo[$i]\"> 
                          <option selected value=''>Seleccione $desc_camp</option>\r\n ";

                        $sql_q = "$sql_reporte[$i]";
                        $query = mysql_query($sql_q);
                        while ($result_query = mysql_fetch_array($query))//recorre el array donde guarde consulta
                            echo "<option value='$result_query[0]'>$result_query[1]</option>\r\n"; //muestra resultado de la consulta

                        echo "                
                        </select>
                </td>
            </tr>
            ";
                    }
                }
                ?>




                <tr>
                    <td></td>

                    <td>
                        <input type="hidden" name="tipo_de_reporte" value="<?php echo $tipo_de_reporte; ?>">
                        <input type="submit" name="submit" value="Ver reporte"></td>
                </tr> 

            </table>
        </form>
    </div>
</div>
<?php
include_once('html_inf.php');
?>

<script>
    $('#pais_id').change(function () {
        var id = $(this).val();

        $.post('reporte_get_provincia.php', {id: id}, function (data) {
            $('#provincia_id').html(data);
        }, "html");
        $('#provincia_id').attr('disabled', false);
    });
    $('#provincia_id').change(function () {
        var id = $(this).val();

        $.post('reporte_get_localidad.php', {id: id}, function (data) {
            $('#localidad_id').html(data);
        }, "html");
        $('#localidad_id').attr('disabled', false);
    });

</script>