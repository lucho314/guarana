<?php
$tip = '';
$msg = $_GET['mensaje'];
include_once('html_sup.php');
include("scaffold.php");

echo $msg;
$bandera = 0;
?>

  

<!--Una tabla por menú con restricción de acceso según el nivel de usuario.-->
<?php if (in_array('GESTION', $modulos)): $bandera++; ?>

    <div class="panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><strong><?= build_friendly_names('GESTIÓN') ?></strong></h3>
        </div>
        <div class="panel-body">
            <table align="center" class="table table-responsive">
                <tr>
                    <td align="center"> 
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-usd"></span>
                                <?= build_friendly_names('Movimientos') ?>  &nbsp<span class="caret"></span>
                                </font>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <form id="movimientos" style="display:inline" method="post" action="/movimientos.php" name="newrecord_">
                                    <input type="hidden" value="new" name="variablecontrolposnavegacion"></form>
                                <li><a href="javascript:$('#movimientos').submit()"><?= build_friendly_names('Alta') ?></a></li>
                                <li><a href="movimientos.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>    
                        </div>
                    </td>
                    <td align="center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-oil"></span>
                                <?= build_friendly_names('Proveedores') ?> &nbsp<span class="caret"></span>
                                </font>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="formulario_ciudad_paso_1.php?tabla_scaffold=proveedors"><?= build_friendly_names('Alta') ?></a></li>
                                <li><a href="proveedors.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>
                        </div>
                    </td>
                    <td align="center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-user"></span>
                                <?= build_friendly_names('Colaboradores') ?>  &nbsp<span class="caret"></span>
                                </font>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="formulario_ciudad_paso_1.php?tabla_scaffold=colaboradors"><?= build_friendly_names('Alta') ?></a></li>
                                <li><a href="colaboradors.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <a href="productos.php">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle"  style="text-shadow: black 3px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-apple"></span>
                                <?= build_friendly_names('Productos') ?>
                                </font>
                            </button>
                        </a>
                    </td>
                    <td align="center">
                        <a href="stocks.php">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle"  style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-duplicate"></span>
                                <?= build_friendly_names('Stock') ?>
                                </font>
                            </button>
                        </a>
                    </td>
                    <td align="center">
                        <a href="rubros.php">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle"  style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-briefcase"></span>
                                <?= build_friendly_names('Rubros') ?>
                                </font>
                            </button>
                        </a>
                    </td>                
                </tr>
            </table>     
        </div>
    </div>
<?php endif; ?>

<?php if (in_array('EVENTOS', $modulos)): $bandera++; ?>
    <div class="panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><strong><?= build_friendly_names('EVENTOS') ?></strong></h3>
        </div>
        <div class="panel-body">
            <table align="center" class="table table-responsive">
                <tr>
                    <td align="center">

                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-blackboard"></span>
                                <?= build_friendly_names('Salones') ?> &nbsp<span class="caret"></span>
                                </font>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <form id="newrecord_movimiento" style="display:inline" method="post" action="/salons.php" name="newrecord_">
                                    <input type="hidden" value="new" name="variablecontrolposnavegacion"></form>
                                <li><a href="formulario_ciudad_paso_1.php?tabla_scaffold=salons"><?= build_friendly_names('Alta') ?></a></li>
                                <li><a href="salons.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>    
                        </div>
                    </td>
                    <td align="center">

                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-header"></span>
                                <?= build_friendly_names('Alojamiento') ?> &nbsp<span class="caret"></span>
                                </font> 
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="formulario_ciudad_paso_1.php?tabla_scaffold=alojamientos"><?= build_friendly_names('Alta') ?></a></li>
                                <li><a href="alojamientos.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>  
                        </div>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-calendar"></span>
                                <?= build_friendly_names('Eventos') ?> &nbsp<span class="caret"></span>
                                </font>
                            </button>
                            <ul class="dropdown-menu" role="menu">

                                <li><a href="formulario_ciudad_paso_1.php?tabla_scaffold=eventos"><?= build_friendly_names('Nuevo evento') ?></a></li>
                                <li><a href="eventos.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>    
                        </div>


                    </td>
                </tr>
            </table>     
        </div>
    </div>
<?php endif; ?>

<?php if (in_array('MEDIOS', $modulos)): $bandera++; ?>
    <div class="panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><strong><?= build_friendly_names('MEDIOS') ?></strong></h3>
        </div>
        <div class="panel-body">
            <table align="center" class="table table-responsive">
                <tr>
                    <td align="center">
                        <a href="publicidad_maestros.php">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-bookmark"></span>
                                <?= build_friendly_names('Publicidad') ?>
                                </font>
                            </button>
                        </a>
                    </td>
                    <td align="center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-facetime-video"></span>
                                <?= build_friendly_names('Medios') ?> &nbsp<span class="caret"></span>
                                </font>
                            </button>
                            <ul class="dropdown-menu" role="menu">

                                <li><a href="formulario_ciudad_paso_1.php?tabla_scaffold=medios"><?= build_friendly_names('Nuevo Medio') ?></a></li>
                                <li><a href="medios.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
            </table>     
        </div>
    </div>
<?php endif; ?>
<?php if (in_array('VIAJES', $modulos)): $bandera++; ?>
    <div class="panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><strong><?= build_friendly_names('VIAJES') ?></strong></h3>
        </div>
        <div class="panel-body">
            <table align="center" class="table table-responsive">
                <tr>
                    <td align="center">
 
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-plane"></span>
                                <?= build_friendly_names('Viajes') ?> &nbsp<span class="caret"></span>
                                </font>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <form id="newrecord_movimiento" style="display:inline" method="post" action="/salons.php" name="newrecord_">
                                    <input type="hidden" value="new" name="variablecontrolposnavegacion"></form>
                                <li><a href="formulario_ciudad_paso_1.php?tabla_scaffold=viajes"><?= build_friendly_names('Alta') ?></a></li>
                                <li><a href="viajes.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>    
                        </div>
                    </td>
                    <td align="center">

                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-tag"></span>
                                <?= build_friendly_names('Viaticos y movilidad') ?> &nbsp<span class="caret"></span>
                                </font> 
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <form id="viaticos" style="display:inline" method="post" action="/viaticos_y_movilidad.php" name="newrecord_">
                                    <input type="hidden" value="new" name="variablecontrolposnavegacion"></form>
                                <li><a href="javascript:$('#viaticos').submit()"><?= build_friendly_names('Alta') ?></a></li>
                                <li><a href="viaticos_y_movilidad.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>  
                        </div>
                    </td>
                    <td align="center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary btn-lg dropdown-toggle" data-toggle="dropdown" style="text-shadow: black 5px 3px 3px;"> 
                                <font color="#ffffff">        
                                <span style="font-size: 40px;" class="glyphicon glyphicon-shopping-cart"></span>
                                <?= build_friendly_names('Gastos') ?> &nbsp<span class="caret"></span>
                                </font>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <form id="gastosccomprobantes" style="display:inline" method="post" action="/gastos_de_viaje.php" name="newrecord_">
                                    <input type="hidden" value="new" name="variablecontrolposnavegacion"></form>
                                <li><a href="javascript:$('#gastosccomprobantes').submit()"><?= build_friendly_names('Alta') ?></a></li>
                                <li><a href="gastos_de_viaje.php"><?= build_friendly_names('Listado') ?></a></li>
                            </ul>    
                        </div>


                    </td>
                </tr>
            </table>     
        </div>
    </div>
    <?php
endif;
if ($bandera === 0) {
    echo "No posee ningun m&oacute;dulo activo para su usuario, contacte al administrador.";
}
?>


<?php
include_once('html_inf.php');
?>
&
