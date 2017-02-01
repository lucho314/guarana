<?php
include("scaffold.php");
include_once('html_sup_min.php');
$apertura = '';
$apertura = $_GET['apertura'];
$tip = '';

  
$tabla = $_REQUEST['tabla'];
if (empty($_POST['variablecontrolposnavegacion'])) {
    $_POST['variablecontrolposnavegacion'] = "new";
}




$posterior = substr($tabla, -3);
$largo_pre = strlen($tabla) - strlen($posterior);
$nombre = substr($tabla, 0, $largo_pre);
$tablas=$nombre."s";


if($_POST['variablecontrolposnavegacion']==='search')
{
    echo "hola";
    new Scaffold('editable', $tablas, 100, array(), array(''), array(), array(), array('D', 'E', 'B', 'N'));
}

if ($_POST['variablecontrolposnavegacion'] === 'create'):
    $descripcion = $_POST['descripcion'];
    $sql_orden = "SELECT AUTO_INCREMENT FROM information_schema.TABLES
  WHERE TABLE_SCHEMA =  'arabidopsis'
  AND TABLE_NAME =  '$tablas'";
    $query = mysql_query($sql_orden);

    while ($r = mysql_fetch_array($query)) {
        $id = $r[0];
    }


    new Scaffold('editable', $tablas, 0, array(''), array(''), array(), array(), array('D', 'E', 'B', 'N')
    );
    ?>

    <script>
        $(function () {
            $("#<?= $tabla ?>", window.opener.document).append("<option value=<?= $id ?> selected><?= $descripcion ?></option>");
            $('#select2-<?= $tabla ?>-container', window.opener.document).text('<?= $descripcion ?>');
            window.close();
        })
    </script>
    <?php
else:
    if (is_null($apertura)) {

        new Scaffold('editable', $tablas, 0, array(''), array(''), array(), array(), array('D', 'E', 'B', 'N')
        );
    } else {
        if ($apertura === 'wizard') {
            echo "<script>"
            . "$(function(){ $('#wizard').show(); }) "
            . "</script>";
        } else {
            $provincia_id = htmlentities($_POST['provincia_id']);
            
            new Scaffold(
                    "editable", $tablas, 30, array('descripcion'), array(), // Campos a ocultar en el formulario
                    array(), // Campos relacionados
                    array(), // Campos a ocultar del maestro en el detalle
                    array('D', 'E', 'B', 'N'), array(), array('localidad_id'), // Campos a ocultar del maestro en el detalle
                    array('provincia_id'), '0', '1'
            );
        }
    }
endif;
?>



<script>

    $(function () {
        $("#crear_").attr('action', 'formulario_nuevo_ajax.php?tabla=<?= $tabla ?>');
    })
</script> 
<div id='wizard' style="display: none">
    <h3>Paso 1 - Nuevo <?= $nombre?></h3>
    <hr>
    <form action="formulario_nuevo_ajax.php?tabla=<?= $tabla?>&apertura=scaffold&editable=<?= $editable?>" name="orden" method="POST"  autocomplete="off" width='80%'>
        <table class="table table-bordered table-striped">
            <tr>
                <td align="right" valign="top" width="150">
                    Elija Pais:
                </td>
                <td>
                    <select class="js-example-basic-single" name="pais_id" id="pais_id"> 
                        <option selected value=''>Elija un Pais</option>  <!--pone valor por defecto-->
                        <?php
                        $sql = "SELECT id, descripcion
                    FROM paiss
                    ORDER BY descripcion";
                        $query = mysql_query($sql);
                        while ($result_query = mysql_fetch_array($query))//recorre el array donde guarde consulta
                            echo "<option value='$result_query[0]'>$result_query[1]</option\n>"; //muestra resultado de la consulta
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right" valign="top" width="150">
                    Seleccione la Provincia: 
                </td>

                <td>
                    <select class="js-example-basic-single" name="provincia_id" id="provincia_id"> 
                        <option selected value=''>Seleccione la Provincia</option>  <!--pone valor por defecto-->
                        <?php
                        $sql = "SELECT id, descripcion
                    FROM provincias
                    WHERE pais_id = '$pais_id' 
                    ORDER BY descripcion";
                        $query = mysql_query($sql);
                        while ($result_query = mysql_fetch_array($query))//recorre el array donde guarde consulta
                            echo "<option value='$result_query[0]'>$result_query[0] - $result_query[1], $result_query[2]</option\n>"; //muestra resultado de la consulta
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right" valign="top" width="150">

                </td>
                <td align="center">
                    <input type="submit" value="Siguiente -->"></td>
            </tr>
        </table>
    </form>
</div>
<br><br><br><br><br><br><br><br>


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
</script>