<?php 
$ultimo_dia=  cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));
$inicio=  "01-". date("m")."-".date("Y");
$fin=  $ultimo_dia."-". date("m")."-".date("Y");

?>

<div align="center">
   <?php //if (in_array('GESTION', $modulos)): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle"
                    data-toggle="dropdown">
                <?= build_friendly_names('SALONES') ?> <span class="caret"></span>
            </button>

            <ul class="dropdown-menu" role="menu">
                <li><a href="salons.php"><?= build_friendly_names('Lista de salones') ?></a></li>
                <li><a href="reservas.php"><?= build_friendly_names('Reservas') ?></a></li>
  
            </ul>    
        </div>
    <?php //endif; ?>
    <?php //if (in_array('EVENTOS', $modulos)): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle"
                    data-toggle="dropdown">
                <?= build_friendly_names('AFILIADOS') ?> <span class="caret"></span>
            </button>

            <ul class="dropdown-menu" role="menu">
                <li><a href="afiliados.php"><?= build_friendly_names('Lista afiliados') ?></a></li>
                <li><a href="familiars.php"><?= build_friendly_names('Familiares') ?></a></li>
                <li><a href="celulars.php"><?= build_friendly_names('Celulares') ?></a></li>
                
            </ul>    
        </div>
    <?php// endif; ?>
    <?php //if (in_array('MEDIOS', $modulos)): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle"
                    data-toggle="dropdown">
                <?= build_friendly_names('REPORTES') ?> <span class="caret"></span>
            </button>

            <ul class="dropdown-menu" role="menu">
                <li><a href="formulario_ciudad_paso_1.php?tabla_scaffold=medios"><?= build_friendly_names('') ?></a></li>  
            </ul>    
        </div>
    <?php //endif; ?>

    <?php // if (in_array('CONFIGURACIONES', $modulos)): ?>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle"
                    data-toggle="dropdown">
                <?= build_friendly_names('CONFIGURACIONES') ?> <span class="caret"></span>
            </button>

            <ul class="dropdown-menu" role="menu">
                <li role="presentation" class="dropdown-header"><?= build_friendly_names('Salones') ?></li>
                <li><a href="turnos.php"><?= build_friendly_names('Turnos') ?></a></li>
                 <li class="divider"></li>
                <li role="presentation" class="dropdown-header"><?= build_friendly_names('Familiares') ?></li>
                <li><a href="nivels.php"><?= build_friendly_names('Niveles') ?></a></li>
                <li><a href="parentescos.php"><?= build_friendly_names('Parentesco') ?></a></li>
                 <li class="divider"></li>
                 <li role="presentation" class="dropdown-header"><?= build_friendly_names('Afiliados') ?></li>
                 <li><a href="areas.php"><?= build_friendly_names('Areas') ?></a></li>
            </ul>    
        </div>    

    <?php // endif ?>
</div>