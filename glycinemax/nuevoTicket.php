<?php
$tip = '';
include_once('lib/conect_mysql.php');
include_once('html_sup.php');
include("scaffold.php");

$afiliadoId = 1; //$_SESSION['usuario_id'];
$sqlFamiliar = 'SELECT id,descripcion FROM familiars WHERE afiliado_id=' . $afiliadoId;
$familiares = mysql_query($sqlFamiliar);
$sqlCelularAfiliado = "SELECT id, nro_telefono FROM celulars WHERE afiliado_id=$afiliadoId";
$celularesAfiliado = mysql_query($sqlCelularAfiliado);

/*
  $sqlClasificacion="SELECT id,descripcion FROM clasificacions";
  $clasificaciones=mysql_query($sqlClasificacion);

  $sqlTipo="SELECT id,descripcion FROM tipos";
  $tipos=mysql_query($sqlTipo);
 */
?>
<div class="panel panel-primary" id="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Formulario de carga de datos: <strong>Ticket</strong></h3>
    </div>
    <div class="panel-body">
        <form action="procesarTicket.php" method="post" id="nuevoTicket">
            <table class="table table-bordered table-striped" cellpadding="2" cellspacing="0" border="0" width="80%">
                <tr>
                    <td align="right" width="200">
                        <label>Clasificacion:</label>
                    </td>
                    <td>
                        <select class="js-example-basic-single" name="clasificacion" id="clasificacion">
                            <?php
                            while ($row = mysql_fetch_array($clasificaciones)) {
                                echo "<option value=$row[0]> $row[1]</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr >
                    <td align="right" width="200">
                        <label>Tipo:</label>
                    </td>
                    <td>
                        <select class="js-example-basic-single" name="tipo" id="tipo">
                            <?php
                            while ($row = mysql_fetch_array($$tipos)) {
                                echo "<option value=$row[0]> $row[1]</option>";
                            }
                            ?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <td align="right" width="200">
                        <label>Familiar:</label>
                    </td>
                    <td>
                        <select class="js-example-basic-single" name="familiarId" id="familiarId">
<?php
while ($row = mysql_fetch_array($familiares)) {
    echo "<option value=$row[0]> $row[1]</option>";
}
?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <td align="right" width="200">
                        <label>Celular:</label>
                    </td>
                    <td>
                        <select class="js-example-basic-single" name="celularId" id="celularId">
<?php
while ($row = mysql_fetch_array($celularesAfiliado)) {
    echo "<option value=$row[0]> $row[1]</option>";
}
?>
                        </select>

                    </td>
                </tr>
                <tr>
                    <td align="right" width="200">
                        <label>Detalle:</label>
                    </td>
                    <td>
                        <textarea rows="4" cols="50" name="detalle" id="detalle"></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="right" width="200">

                    </td>
                    <td>
                        <input type="submit" name="Aceptar" value="Aceptar">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php
include_once('html_inf.php');
?>

