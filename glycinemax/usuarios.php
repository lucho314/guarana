<?php
$tip = '';
include_once('html_sup.php');
include_once('lib/connect_mysql.php');
$query="SELECT nombre_usuario, afiliados.descripcion as 'afiliado' FROM usuarios "
	."INNER JOIN afiliados on afiliados.id=usuarios.afiliado_id";

$resultado=mysql_query($query);
echo  mysql_num_rows($resultado)."registros encontrado" ;
?>

<table class="table table-striped" id="lista" cellpadding="2" cellspacing="0" border="0">
	<thead>
		<tr>
			<th>Nombre de usuario</th>
			<th>Afiliado</th>
		
	</thead>
	<tbody>
		<?php while ($usuarios=mysql_fetch_assoc($resultado)): ?>
			<tr>
				<td><?= $usuarios['nombre_usuario']?></td>
				<td><?= $usuarios['afiliado']?></td>
			</tr>
		<?php endwhile;?>
	<tbody>