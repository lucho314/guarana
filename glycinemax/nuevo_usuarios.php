<?php
$tip = '';
include_once('html_sup.php');
include_once('lib/connect_mysql.php');
$query="SELECT id,descripcion FROM afiliados";
$resultado=mysql_query($query);
?>


<div class="panel panel-primary" id="panel">
	<div class="panel-heading">
		<h3 class="panel-title">Formulario de carga de datos: <b>USUARIO</b></h3>
 	</div> 
 	<div class="panel-body">
 	<form action="usuario.php" method="POST">
 		<table id="scaffold" class="table table-bordered table-striped" cellpadding="2" cellspacing="0" border="0" width="80%">
 			<tr>
 				 <td width="250" align="right" class="tabla_descripcion">
 				  	<b>Nombre usuario:</b>
                 </td>
                 <td class="tabla_nombre_usuario">
                    <input class="mayuscula" required="required" type="text" name="nombre_usuario" id="descripcion" size="35" />
                 </td>
            </tr>
             <tr>
 				 <td width="250" align="right" class="tabla_descripcion">
 				  	<b>Contraseña:(En mayusculas)</b>
                 </td>
                 <td class="tabla_clave">
                    <input class="mayuscula" required="required" type="text" name="clave" id="descripcion" size="35"/>
                   </td>

            </tr>
            <tr>
 				 <td width="250" align="right" class="tabla_descripcion">
 				  	<b>Afiliado:</b>
                 </td>
                 <td class="tabla_clave">
                    <select name="afiliado_id" class="js-example-basic-single validar-select">
                    	<?php while ($afi = mysql_fetch_array($resultado)):?>
                    		<option value="<?=$afi[0] ?>"><?=$afi[1] ?></option>
                    	<?php endwhile;?>
                    </select>
                   </td>

                   
            </tr>
            <tr>
 				 <td width="250" align="right" class="tabla_descripcion">
                 </td>
                 <td>
                    <button type="submit">Agregar Registro</button>
                  </td>
                   
                   
            </tr>
            </table>
       </form>
	</div>
</div>

