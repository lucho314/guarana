<?php
#Version mejorada para maestro detalle el 21 de febrero de 2012

class Scaffold {


// configuracion
	
      /*   public $db_host = 'localhost';
	public $db_user = 'arabidopsis';
	public $db_password = 'Shinw1sa_'; 
	public $db_name = 'pymeser_arabidopsis';
   */
	public $db_host = 'localhost';
	public $db_user = 'root';
        public $db_password = 'shinw1sa'; 
	public $db_name = 'glycinemax';        
      
	/**
	Los siguientes arrays contienen los campos de las fk que estan relacionados con tablas que no llevan el mismo nombre.
	*/

	var $singular = array ('pagado','estudia');	// las fk
	var $plural = array ('si_nos','si_nos'); //las tabas correspondientes a las fk anteriores

	/**
	* colores de filas alternados
	*/
	public $row_odd = '#fff';
	public $row_even = '#E9FEFF';
// fin configuracion

	var $table = '';								// Variable interna para la tabla

                        
	function Scaffold(
                            $mostrar, 
                            $table, 
                            $max_records    = 100, 
                            $fields         = array(), 
                            $ocultos        = array(), 
                            $relacion       = array(), 
                            $oc_maestro     = array(), 
                            $view_column    = array(), 
                            $filter         = array(),
                            $fk             = array(),
                            $fk_filtro      = array(),
                            $calendar,
                            $htmlsafe       = true, 
                            $width          = NULL)
                {
                
		$this->mostrar      = $mostrar; //si la descripcion es editable o no
		$this->table        = $table;	// Seteo la tabla
		$this->max_records  = intval($max_records);	// Seteo el limite de registros por tabla
		$this->fields       = $fields; // Campos a mostrar. Opcional. Si no se detalla, se muestran todos.
		$this->ocultos      = $ocultos; // Campos a ocultar. Opcional
                $this->relacion     = $relacion;  //Array que contiene los campos que están relacionados desde otras tablas
		//$this->table_asoc = $table_asoc; //Tabla asociada a la table. Opcional.
                $this->oc_maestro   = $oc_maestro; 
                $this->filter       = $filter;
                $this->fk           = $fk; /** Array con el nombre del campo que se va a filtrar.  Por ejemplo, equipo_id */
                $this->fk_filtro    = $fk_filtro; /** Array con el nombre del campo que filtra. Por ejemplo, cliente_id. Se empareja con el array $fk */
                $this->calendar     = $calendar;
		$this->htmlsafe     = $htmlsafe;	// html seguro
		$this->width        = intval($width);	//
                $this->view_column  = $view_column;  //D = detalles, E=Editar, B=Borrar   view_column('','E','B') muestra Editar y Borrar pero no muestra Detalle.
		

                $this->control_admin = '1';
                
                
		//Defino el tipo de comprobante y creo variables propias de la clase
		$this->maestro_id = $_POST['maestro_id'];
                
                $this->where=$_POST['where'];
                
                //se define el parametro fk_id que tendra los valores de las forigkey de los campos a filtrar
                foreach($this->fk_filtro as $fk_id){
                    if(isset($_POST[$fk_id])){
                    $this->fk_id[]=$_POST[$fk_id];}
                    else{
                        $this->fk_id[]="%%";
                    }
                }                
                
              
                
                if (empty($this->cliente_id)) {
                    $this->cliente_id = $_POST['cliente_id'];
                }
                
                /*        
                ###### SOLO GESTRUCK ###################################################################
		//$this->empresa_id = DevuelveValor($this->usuario_id, 'empresa_id', 'usuarios', 'id');
                ########################################################################################

                
                
                if ($this->usuario_nivel == 1) {
                    $this->empresa_id = "'%'";
                }
                
                if ($_POST['empresa_id'] == $this->control_admin) {
                    $this->empresa_id = "'%'";
                }                
                /*

                */
                
		$cadena = $this->table;
		$largo_nombre = strlen($cadena);
		$cola = 9;
		$total_cadena = $largo_nombre - $cola;
		$this->tipo_comprobante = substr($cadena, 0, $total_cadena);


		// Variable de pagina
		(!empty($_GET['page']) && is_numeric($_GET['page'])) ? $this->page = intval($_GET['page']) : $this->page = 1;

                        
		$connection = mysql_connect($this->db_host,
							$this->db_user,
							$this->db_password);
		mysql_select_db($this->db_name, $connection) or die('Error al conectar a la base de datos.');

		$action = (!empty($_POST['variablecontrolposnavegacion'])) ? $_POST['variablecontrolposnavegacion'] : 'list' ;
                echo "<script> var accion='$action'</script>"; // esta setea una bariable de bandera para ejecutar javascript.
                switch($action){
			default:
				$this->list_table(null,$this->where);
			break;

			case 'list':
				$this->list_table(null,$this->where);
			break;

			case 'new':
				$this->new_row();
			break;

			case 'create':
				$this->create();
			break;

			case 'edit':
				$this->edit_row();
			break;
                        
                        case 'view':
				$this->view_row();
			break;

			case 'update':
				$this->update();
			break;

			case 'delete':
				$this->delete_row();
			break;

			case 'search':
				$this->search();
			break;


		}
	}
        
        
        
        /**
	* Transforma la fecua
	*
	* string $fecha_lat		// Fecha en formato latino
	* 
	*
	*/
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
         

	/**
	* Lista de registros
	*
	* string $msg 		// Mensaje opcional
	* strgin $where		// WHERE opcional como parametro para SQL
	*
	*/
	function list_table($msg = NULL, $where = null){
                   echo "<script type='text/javascript' src='libjs/".$this->table.".js'></script>";
              if ($this->max_records != 0) {  
		$start = (($this->page-1)*$this->max_records);				// Parametros por pagina
		$end = $this->max_records;	// Fin parametros por pagina
		$page = '';    
                
                 if($where == null) {
                   $where = " WHERE descripcion<>'GENERICO' ";   
                 }
                 else {
                     $where = $where." AND descripcion<>'GENERICO' ";
                 }
                
                
                if((!empty($this->maestro_id)) AND ($where == null)) {
                    $where = " WHERE ".$this->tipo_comprobante."_maestro_id = ".$this->maestro_id.' ';
                }elseif ((!empty($this->maestro_id)) AND ($where != null)) {
                    $where = $where.' AND '.$this->tipo_comprobante."_maestro_id = ".$this->maestro_id;
                }                
                
                
                if((!empty($this->filter)) AND ($where == null)) {
                    $where = 'WHERE '.$this->filter[0].'='.$this->filter[1];
                }elseif (($where != null) AND (!empty($this->filter))) {
                    $where = $where.' AND  '.$this->filter[0].'='.$this->filter[1];
                }
                
                
                
                
                 $where = $where. ' ORDER BY id DESC LIMIT '.$end;
    
                $Query = 'SELECT COUNT(*) FROM '.$this->table.''.$where;
		$totalQuery = mysql_query ($Query) or die(mysql_error());
		$totalA = mysql_fetch_array($totalQuery);
		$total = $totalA[0];
		if (!empty($this->fields)) {
		// Muestra los campos seleccionados solamente
			$query = 'SELECT id';
			foreach($this->fields as $val){
				$query .= ', '.$val;
			}
			$query .= ' FROM '.$this->table;
		}else{
			$query = 'SELECT * FROM '.$this->table;
		}
                 $query;
		if(!empty($where)){ $query .= $where;}
                
                 $query;
		$select = mysql_query($query) or die(mysql_error());
		$i = 0;
                $j = 0;

		(!empty($this->width)) ? $width = ' width="'.$this->width.'"' : $width = NULL;

		$this->build_search_bar();

                
                ##### Muestra el encabezado del documento maestro ####
                
		if(!empty($this->maestro_id)) {
                    $input_maestro = '<input type="hidden" name="maestro_id" value="'.$this->maestro_id.'">';
                    $mensaje_maestro = 'para '.$this->build_friendly_names(strtoupper(substr($this->table,0,-1)));
                    
                    
                    
                    $sql_maestro = "SELECT * FROM ".$this->tipo_comprobante."_maestros WHERE id=".$this->maestro_id;
                    
                    $fields = mysql_query($sql_maestro);
                    $rows   = mysql_query($sql_maestro);
                    $row = mysql_fetch_row($rows);
                    
                    $page_m .= '<table bgcolor="#ffffff" cellpadding="2" cellspacing="0" border="0" width="800">';
                    $page_m .= '<tr>';
                    
                    $counthidden = 0;
		    $totalhidden = count($this->oc_maestro);
                    
                    //echo 'total ocultos maestro: '.$totalhidden;
                    
                    while($j < mysql_num_fields($fields)){
			$field = mysql_fetch_field($fields);
                        
                        
                        // En el siguiente if, hay campos que deberían enviarse en un array desde afuera. 
			if(($field->name != 'id')){
 
				$page_m .= '<tr>';
				// reviso las claves foraneas..
				if(substr($field->name, -3) == '_id'){
                                        $campo = substr($field->name,0,-3);
                                        $tabla = $campo.'s';
                                        $valor_campo = DevuelveValor($row[$j], 'descripcion', $tabla, 'id');
					$page_m .= '<td align="right"  valign="top" width="150"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$valor_campo.'</td>';

				}elseif(($counthidden <= $totalhidden-1) AND ($this->oc_maestro[$counthidden] == $field->name)){
					$variable = $this->oc_maestro[$counthidden];
					$page_m .= '';
					$counthidden = $counthidden + 1;
				}elseif($field->blob == 1){
					$page_m .= '<td align="right"  valign="top" width="150"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$j].'</td>';
				}elseif($field->type == 'timestamp'){
					$page_m .= '<input type="hidden" name="'.$column->name.'" id="'.$column->name.'" value="'.date("Y-m-d H:i:s").'"/>';
					//$page_m .= '<td align="right"  width="200"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$j].'</td>';
				}elseif($field->type == 'date'){ $value_field = $this->date_transform_lat($row[$j]); 
					$page_m .= '<td align="right"  valign="top" width="150"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$value_field.'</td>';
				}elseif($field->type == 'time'){
					$page_m .= '<td align="right"  valign="top" width="150"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$j].'</td>';
				}elseif($field->name == 'descripcion'){
					if($this->mostrar=='editable'){
					$page_m .= '<td align="right"  width="150"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$j].'</td>';
					}else{
					$page_m .= '<input type="hidden" name="'.$field->name.'" id="'.$field->name.'" value="'.$row[$j].'" />';
					}
				}else{
					$page_m .= '<td align="right"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$j].' </td>';
				}

			}
			$j++;
			$page_m .= '</tr>';
		}
		$page_m .= '<tr><td>&nbsp;</td><td></td></tr>'
		      . '</table>
                          
                        ';
                

		echo $page_m;
                
                    
                    
                    
                } //esta linea verifica si se envia el id de maestro desde el formulario anterior. en caso positivo, genera un campo oculto del formulario

                ###### fin encabezado documento maestro
                
                
                
                
		$total.=' Registro/s encontrado/s.<br> Secci&oacute;n: <strong>'.$this->build_friendly_names(strtoupper(substr($this->table,0,-1))).'</strong>. | '
			. '<form name="newrecord_'.$this->filter[1].'" id="newrecord_'.$this->filter[1].'" action="'.$_SERVER['PHP_SELF'].'" method="post" style="display:inline">
                           '.$input_maestro.'
                           <input type="hidden" name="variablecontrolposnavegacion" value="new">';
                               
                if(($this->view_column[3]=='N') OR ($this->usuario_nivel == 1)){
                $total .=  '<div align="center"><a href="javascript:document.newrecord_'.$this->filter[1].'.submit()">Registro nuevo '.$mensaje_maestro.'</a></div>';
                }            

                $total .= '</form>
                           <br />
                           <br />';
                echo $total;
                
                
                
                //Filtro de resultados
                /*
                $filtro .= '
                <script>
                var searchOnTable = function() {
                var table = $(\'#lista\');
                var value = this.value;
                table.find(\'tr\').each(function(index, row) {
                        var allCells = $(row).find(\'td\');
                        if(allCells.length > 0) {
                                var found = false;
                                allCells.each(function(index, td) {
                                        var regExp = new RegExp(value, \'i\');
                                        if(regExp.test($(td).text())) {
                                                found = true;
                                                return false;
                                        }
                                });
                                if (found == true) $(row).show();
                                else $(row).hide();
                                }
                        });
                };

                $(function(){
                        $(\'#filter\').keyup(searchOnTable);
                });
                </script>
                
                ';
                $filtro .= '<br><br>Filtro: <input type="text" id="filter" placeholder="Filtrar"><br><br>';
                echo $filtro;*/
                //  fin Filtro de resultados
                
                
		if(!empty($msg)) { echo $msg; }
		//$this->paginate($total, $this->page);
		$page .= '<table class="table table-striped" id="lista" cellpadding="2" cellspacing="0" border="0"'.$width.'>';
		$page .= '<thead><tr>';
                
		while($i < mysql_num_fields($select)){
			$column = mysql_fetch_field($select, $i);
			if($column->name != 'id' && $column->name != 'updated_at' && $column->name != 'created_at'){
				$page .= '<th nowrap>'.str_replace(' ',' ',$this->build_friendly_names($column->name)).'</th>';

			}
			$i++;
		}
                
               
                
                if($this->view_column[0]=='D'){
                    $page .= "<th>Ver</th>";
                }
                  if($this->view_column[1]=='E'){
                    $page .= "<th>Editar</th>";
                }
                  if($this->view_column[2]=='B'){
                    $page .= "<th>Borrar</th>";
                }
                 if(substr($this->table, -9) == '_maestros') {
                    $page .= "<th>Detalle</th>";
                }
                 $page .="</thead><tbody>";
                
		$count = 0;
		while($array = mysql_fetch_array($select)){
			$page .= (!($count % 2) == 0) ? '<tr style="background:'.$this->row_even.';">' : '<tr style="background:'.$this->row_odd.';">';
			
                        
                        foreach($array as $column => $value){
                            

                                
				if(!is_int($column) && $column != 'id' && $column != 'updated_at' && $column != 'created_at'){
					
                                        
                                        $page .= '<td bgcolor="'.$bgcolor.'">';  
                                        
					if($column == 'foto') {
						$page .= '<img width="30" height="30" src="'.$value.'">';

					}else{
					if($this->htmlsafe) {
						if(substr($column, -3) == '_id'){

						$page .= $this->build_foreign_key_title($column,$value);
						}
						else{ 
                                                    $value ="<span style='display: none;'>$value</span>". $this->date_transform_lat ($value);
                                                    $page .= $value;
                                                }
					}else{
						if(substr($column, -3) == '_id'){
						$page .= $this->build_foreign_key_title($column,$value);}
						else{
                                                $value ="<span style='display: none;'>$value</span>". $this->date_transform_lat ($value);
                                                    $page .= $value;
                                                }
					}
					}
					$page .= '</td>';
				}
			}
			$count ++;
                        
                        //Lo siguiente añade un botón en la fila para ver los datos relacionados con la misma, 
                        //siempre y cuando esos datos hayan sido mandados por el array desde el llamado al scaffold
                        
                        if (!empty($this->relacion)) {
                            $relacion = $this->relacion;
                            $table = $this->table;
                            $id_name = substr($table,0,-1).'_id';
                            $cantidad_elementos = count($relacion);
                            
                            for ($i=0;$i<$cantidad_elementos;$i++)
                            {
                                $relacion_id = $relacion[$i];
                                $relacion_table = substr($relacion_id, 0,-3);
                                $page .= '
                                <td>
                                <form name="view_'.$array[0].'" id="view_'.$array[0].'" action="'.$relacion_table.'s.php" 
                                    method="post" style="display:inline" target="ventanaForm" 
                                    onsubmit="window.open(\'\', \'ventanaForm\', \'width=1024,height=600,top=100,left=100,scrollbars=yes\')">
                                    <input type="hidden" name="variablecontrolposnavegacion" value="view">
                                    <input type="hidden" name="ventananueva" value="si">
                                    <input type="hidden" name="id_name" value="'.$id_name.'">
                                    <input type="hidden" name="id_value" value="'.$array[0].'">
                                    <input type="hidden" name="ver_relacion" value="SI">
                                    <input type="hidden" name="tabla_relacion" value="'.$relacion_table.'s">
                                    <input type="submit" name="View" value="'.$this->build_friendly_names($relacion_table).'" class="boton_relacion" style="cursor:pointer;" />
                                 </form>
                                 </td>                            
                                ';
                                
                            }
                            
                        }
                        
                        if(($this->view_column[0]=='D') OR ($this->usuario_nivel == 1)){
			$page .= '
                            <td width="50">
                            <form name="view_'.$array[0].'" id="view_'.$array[0].'" action="'.$_SERVER['PHP_SELF'].'" 
                                method="post" style="display:inline" target="ventanaForm" 
                                onsubmit="window.open(\'\', \'ventanaForm\', \'width=1024,height=600,top=100,left=100,scrollbars=yes\')">
                                <input type="hidden" name="variablecontrolposnavegacion" value="view">
                                <input type="hidden" name="ventananueva" value="si">
                                <input type="hidden" name="id" value="'.$array[0].'">
                                <input type="submit" name="View" value="Ver" class="boton_relacion" style="cursor:pointer;" />
                             </form>
                             </td>';
                        }
                        
                        
                        if(($this->view_column[1]=='E') OR ($this->usuario_nivel == 1)){
                        $page .= '
                            
                            <td width="50">
                            <form name="edit_'.$this->filter[1].'_'.$array[0].'" id="edit_'.$this->filter[1].'_'.$array[0].'" action="'.$_SERVER['PHP_SELF'].'" 
                                method="post" style="display:inline"><input type="hidden" name="variablecontrolposnavegacion" value="edit">
                                <input type="hidden" name="id" value="'.$array[0].'">
                                <input type="hidden" name="maestro_id" value="'.$this->maestro_id.'">    
                                    <a href="javascript:document.edit_'.$this->filter[1].'_'.$array[0].'.submit()">Editar</a>
                            </form>
                            </td>
                        ';
                        }
                        
                        if(($this->view_column[2]=='B') OR ($this->usuario_nivel == 1)){
                        $page .= '
		            <td width="50">
			    <form name="delete_'.$this->filter[1].'_'.$array[0].'" id="delete_'.$this->filter[1].'_'.$array[0].'" action="'.$_SERVER['PHP_SELF'].'" 
                                method="post" style="display:inline">
                                <input type="hidden" name="variablecontrolposnavegacion" value="delete">
                                <input type="hidden" name="maestro_id" value="'.$this->maestro_id.'">
                                <input type="hidden" name="id" value="'.$array[0].'">
                                <a href="javascript:" onClick="if (confirm(\'Est&aacute; seguro?\')){document.delete_'.$this->filter[1].'_'.$array[0].'.submit();}else{return false;}">
                                    Borrar
                                </a>
                             </form>
                             </td>';
                        
                        }
                        

			if(substr($this->table, -9) == '_maestros') {
			$page .= '<td><form name="searchbar" id="searchbar" action="'.$this->tipo_comprobante.'_detalles.php" method="post" style="display:inline">'
                                . '<input type="hidden" name="variablecontrolposnavegacion" value="search">'
                                . '<input type="hidden" name="field" value="'.$this->tipo_comprobante.'_maestro_id">'
                                . '<input type="hidden" name="compare" value="="><input type="hidden" name="searchterm" value="'.$array[0].'">'
                                . '<input type="hidden" name="maestro_id" value="'.$array[0].'">'
                                . '<input type="submit" name="Search" value="Detalle" />'
                                . '</form>'
                                . '</td>';} //ESTE BOTON TIENE QUE HACER UN SEARCH LIMITANDO LOS RESULTADOS AL ID MAESTRO.
			$page .= '</tr>';
		}

		$page .= '</tbody></table>
                    
                    ';
		echo $page;
		//$this->paginate($total);


		}
                
        }



	/**
	* Este metodo crea el formulario para un nuevo registro
	*
	*/
	function new_row(){
		$page = '';
		$selectFields = mysql_query('SELECT * FROM '.$this->table);
		$i = 0;
                echo '<div class="panel panel-primary" id="panel">
                        <div class="panel-heading">
                          <h3 class="panel-title">Formulario de carga de datos: <strong>'.$this->build_friendly_names(strtoupper(substr($this->table,0,-1))).'</strong></h3>
                        </div>
                        <div class="panel-body">';
		//echo '<h2><br>';
		$page .= '<form action="'.$_SERVER['PHP_SELF'].'#'.$this->filter[1].'" method="POST" name="crear_'.$this->filter[1].'" id="crear_'.$this->filter[1].'" enctype="multipart/form-data" onsubmit="return validacion()">'
				. '<input type="hidden" name="variablecontrolposnavegacion" value="create">'
				. '<table id="scaffold" class="table table-bordered table-striped" cellpadding="2" cellspacing="0" border="0" width="80%">';
		$counthidden = 0;
		$totalhidden = count($this->ocultos);
                $idtable = 0;
                
                
                $showcalendar = $this->calendar;
                if ($showcalendar == '') {$classcalendar = 'class="calendario"';}
                
		while($i < mysql_num_fields($selectFields)){
                    $idtable = $idtable + 1;
			$column = mysql_fetch_field($selectFields);
			if($column->name != 'id'){
				if (strpos($column->name, "fecha") !== false) { $clase = 'required="required"';}
				elseif ($column->name == 'campo_de_control') {$clase = '';}
				else { $clase = 'required="required'; }
				$page .= '<tr>';
				// revisa las claves foraneas..
				
				$posterior = substr($column->name, -11);
				$tabla     = $this->table;
				$cadena    = $column->name;
                                $comment   = DevuelveComentario($cadena, $tabla);
				$largo_pre = strlen($cadena) - strlen($posterior);
				$pre       = substr($cadena,0,$largo_pre);
				// echo $pre.' | ';
			        
				if(($posterior == '_maestro_id') AND ($this->tipo_comprobante == $pre)){
					$page .= '<input type="hidden" name="'.$column->name.'" id="'.$column->name.'" value="'.$this->maestro_id.'"/>';					
				}
                                elseif((substr($column->name, -3) == '_id') AND ($column->name != 'usuario_id') AND ($this->ocultos[$counthidden] != $column->name)){
					if ($column->name==$this->filter[0]) {
                                            $page .= '<td class="tabla_'.$column->name.'"><input type="hidden" name="'.$this->filter[0].'" value="'.$this->filter[1].'" /></td>';
                                        }else{
                                            $page .= $this->build_foreign_key_dropdowns($column->name);
                                        }
                                        
				}elseif(($counthidden <= $totalhidden-1) AND ($this->ocultos[$counthidden] == $column->name)){
					$variable = $this->ocultos[$counthidden];
					$page .= '<td class="tabla_'.$column->name.'"><input type="hidden" name="'.$variable.'" id="'.$variable.'" value="" /></td>';
					$counthidden = $counthidden + 1;
                                        
				}elseif($column->blob == 1){
					$page .= '
                                            <td align="right"  valign="top" width="150" class="tabla_'.$column->name.'">
                                                <strong>'.$this->build_friendly_names($column->name).':</strong>
                                            </td>
                                            <td class="tabla_'.$column->name.'">
                                                <textarea name="'.$column->name.'" id="'.$column->name.'" rows="5" cols="40"></textarea>
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">
                                            </td>';
                                        
				}elseif($column->type == 'timestamp'){
					$page .= '
                                            <input type="hidden" name="'.$column->name.'" id="'.$column->name.'" value="'.date("Y-m-d H:i:s").'"/>
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">
                                            ';
                                        
				}elseif($column->name == 'clave'){
					$page .= '
                                            <td align="right"  valign="top" width="150" class="tabla_'.$column->name.'">(todo en mayusculas) 
                                                <strong>'.$this->build_friendly_names($column->name).':</strong>
                                            </td>
                                            <td class="tabla_'.$column->name.'">
                                                <input type="password" name="'.$column->name.'" id="'.$column->name.'" />
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">
                                            </td>';
                                        
				}elseif($column->type == 'date'){
					$page .= '
                                            <td align="right"  valign="top" width="150" class="tabla_'.$column->name.'">
                                                <strong>'.$this->build_friendly_names($column->name).':</strong>
                                            </td>
                                            <td class="tabla_'.$column->name.'">
                                                <input onfocus="cambiaFecha();"  type="text" value="'.date("d-m-Y").'" class="datepicker" name="'.$column->name.'" id="'.$column->name.'" />
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">
                                            </td>';
                                        
				}elseif($column->type == 'time'){
					$page .= '
                                            <td align="right"  valign="top" width="150" class="tabla_'.$column->name.'">
                                                <strong>'.$this->build_friendly_names($column->name).':</strong>
                                            </td>
                                            <td class="tabla_'.$column->name.'">
                                                <input  onfocus="cambiaFecha();"  type="text" value="'.date("H:i:s").'" name="'.$column->name.'" id="'.$column->name.'" />
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">
                                            </td>';
                                        
                                }elseif($column->name == 'descripcion'){
					if($this->mostrar=='editable'){
					$page .= '
                                            <td width="150" align="right" class="tabla_'.$column->name.'">
                                            <strong>'.$this->build_friendly_names($column->name).':</strong>
                                                </td>
                                                <td class="tabla_'.$column->name.'">
                                                <input class="mayuscula" required="required" type="text" name="'.$column->name.'" id="'.$column->name.'" />
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">';
					}else{
					$page .= '<td class="tabla_'.$column->name.'"><input type="hidden" name="'.$column->name.'" id="'.$column->name.'" /></td>';
					}
				/*}elseif($column->name == 'fecha_y_hora'){
					$page .= '<input type="hidden" name="'.$column->name.'" id="'.$column->name.'" />';
				*/}
                               elseif ($column->name == 'email') {
                                   $page .= '
                                            <td align="right"  width="200" class="tabla_'.$column->name.'">
                                                <strong>'.$this->build_friendly_names($column->name).':</strong>
                                            </td>
                                            <td class="tabla_'.$column->name.'">
                                                <input class="email" type="text" required name="'.$column->name.'" id="'.$column->name.'" value="'.$valor_campo.'" size="35" />
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">
                                            </td>';
                               
                           }
                           elseif ($column->name == 'telefono' || $column->name==='celular' || $column->name == 'nro_telefono') {
                            $page .= '
                                            <td align="right"  width="200" class="tabla_'.$column->name.'">
                                                <strong>'.$this->build_friendly_names($column->name).':</strong>
                                            </td>
                                            <td class="tabla_'.$column->name.'">
                                                <input class="tel" type="text" required name="'.$column->name.'" id="'.$column->name.'" value="'.$valor_campo.'" size="35" />
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">
                                            </td>';
                       }
                                
                                else{
                                        $nombre_columna = $column->name;
                                        $valor_tmp      = $_POST["$nombre_columna"];
                                        $tipo=$column->type;
					if (isset($valor_tmp)) {
                                            $valor_campo = $_POST["$nombre_columna"];
                                            echo $valor_campo;
                                        }
					$page .= '
                                            <td align="right"  width="200" class="tabla_'.$column->name.'">
                                                <strong>'.$this->build_friendly_names($column->name).':</strong>
                                            </td>
                                            <td class="tabla_'.$column->name.'">
                                               <input class="mayuscula '.$tipo.'" type="text" required name="'.$column->name.'" id="'.$column->name.'" value="'.$valor_campo.'" size="35"  onfocus="setMensaje()"/>
                                                <img src="images/helpicon.gif" title="'.$comment.'" alt="'.$comment.'">
                                            </td>';
				}
			}
			$i++;
		}

                if(!empty($this->maestro_id)) {
                    $input_maestro = '<input type="hidden" name="maestro_id" id="maestro_id" value="'.$this->maestro_id.'">';
                }
		$page .= '<tr><td>&nbsp;</td>
                    <td align="center">
                          '.$input_maestro.'
                          <input type="hidden" name="campo" id="campo" value="'.$this->table.'_id">
			  <input type="submit" value="Agregar registro" /></td></tr>'
		 		. '</table>'
		 		. '</form>';
		 		//. '<a href="'.$_SERVER['PHP_SELF'].'">Volver al listado</a>';



		echo $page;
                echo "</div></div>";
                echo "<script type='text/javascript' src='libjs/".$this->table.".js'></script>";
                        
	}



	/**
	* Este metodo inserta un nuevo registro
	* Asume que en la base de datos no hay un campo llamado 'variablecontrolposnavegacion'
	*
	*
	*/
	function create($control=NULL){
		$select = mysql_query('SELECT * FROM '.$this->table);
		$insert = "INSERT INTO ".$this->table." VALUES('',";
                
		$i = mysql_num_fields($select);
                //echo 'total: '.$i.'<br>';
		$i--;
		foreach($_POST as $key => $value){
    
			if($key != 'variablecontrolposnavegacion')
                        {
                            ($key == 'updated_at' || $key == 'created_at')? $value = 'NOW()' : (get_magic_quotes_gpc) ? $value =  "'".mysql_real_escape_string(stripslashes($value))."'" : $value = "'".mysql_real_escape_string($value)."'";
                            $i--;
                            
                            if($i > 0){
                                    //echo $i.'<br>';

                                    $value = $this->date_transform_usa($value);
                                    //echo $value;
                                
                                    $insert .= "'".($value)."', ";
                            }elseif ($key != 'maestro_id') {$insert .= ($value).')';break;}
                            
			}

		}
                
		//$insert .= strtoupper($value).')';
		//echo $insert;
                
		mysql_query($insert) or die(mysql_error());
		$last_idq = mysql_query('SELECT LAST_INSERT_ID()')or die(mysql_error());
		$last_id = mysql_fetch_array($last_idq);
		//echo $last_id[0];
                $_POST['descripcion']=($this->table==='stocks')? 'ALTA DE '.$_POST['disponibles'].' PRODUCTOS':$_POST['descripcion'];
                $id_registro_creado=  DevuelveValor('CREADO', 'id', 'estados', 'descripcion');
                $sql_maquina="INSERT INTO `registro_de_estados` (`id`, `descripcion`, `tabla`, `registro_id`, `fecha_cambio_estado`, `estado_id`, `usuario_id`) "
                        . "VALUES (NULL, '".$_POST['descripcion']."', '".$this->table."', $last_id[0], CURDATE(), $id_registro_creado, '".$_SESSION['usuario_id']."');";
               
                mysql_query($sql_maquina);
              
                $_POST=array();
                $_POST['maestro_id']=$this->maestro_id;
                $_POST[$this->filter[0]]=$this->filter[1];

		if(!empty($this->maestro_id)) {$where = " WHERE ".$this->tipo_comprobante."_maestro_id = ".$this->maestro_id;}
                if ($control == NULL) {
		$this->list_table('<div style="color:#090;"><p>Registro guardado..</p></div>',$where);
                }
	}

	/**
	* Este metodo construye el formulario para editar registros
	* 
	*/
	function edit_row(){
		$page = '';
                $idtable=0;
		$fields = mysql_query('SELECT * FROM '.$this->table) or die(mysql_error());
		$select = mysql_query('SELECT * FROM '.$this->table.' WHERE id = '.intval($_POST['id']));
		$row = mysql_fetch_row($select);
		$i = 0;
		echo '<div class="panel panel-primary">
                        <div class="panel-heading">
                        <h2 class="panel-title">Formulario de carga de datos: <strong>'.$this->build_friendly_names((substr($this->table,0,-1))).'</strong></h2><br>'
                        . '</div>
                        <div class="panel-body">';
                
		$page .= '<form action="'.$_SERVER['PHP_SELF'].'#'.$this->filter[1].'" method="POST" name="crear_'.$this->filter[1].'" enctype="multipart/form-data" onsubmit="return creardescripcion()">'
				. '<input type="hidden" name="variablecontrolposnavegacion" value="update">'
		 		. '<table cellpadding="2" cellspacing="0" border="0" width="500"  class="table table-bordered table-striped">';
                
                
                $showcalendar = $this->calendar;
                if ($showcalendar == '') {$classcalendar = 'class="calendario"';}
                
                
		while($i < mysql_num_fields($fields)){
                    $idtable+=1;
			$field = mysql_fetch_field($fields);
			if($field->name != 'id'){
//				if (strpos($field->name, "fecha") !== false) { $clase = ':date_au :required';	}
//				else { $clase = ':required'; }
                                $clase = 'required="required"'; 
				$page .= '<tr>';
				// reviso las claves foraneas..
				if(substr($field->name, -3) == '_id'){
                                        if ($field->name==$this->filter[0]) {
                                            $page .= '<input type="hidden"  name="'.$this->filter[0].'" value="'.$this->filter[1].'" />';
                                        }else{
                                            $page .= $this->build_foreign_key_dropdowns($field->name, $row[$i]);
                                        }                                    
					
				}elseif($field->blob == 1){
					$page .= '<td align="right"  valign="top" width="150" class="tabla_'.$field->name.'">'
                                                . '<strong>'.$this->build_friendly_names($field->name).':</strong>'
                                                . '</td>'
                                                . '<td class="tabla_'.$field->name.'">'
                                                . '<textarea '.$clase.' name="'.$field->name.'" id="'.$field->name.'" rows="5" cols="40">'.$row[$i].'</textarea>'
                                                . '</td>';
                                        
				}elseif($field->type == 'timestamp'){
					$page .= '<input type="hidden" name="'.$field->name.'" id="'.$field->name.'" value="'.date("Y-m-d H:i:s").'"/>';
					//$page .= '<td align="right"  width="200"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$i].'</td>';
                                        
				}elseif($field->type == 'date'){ $value_field = $this->date_transform_lat($row[$i]); 
					$page .= '<td align="right"  valign="top" width="150" class="tabla_'.$field->name.'">'
                                                . '<strong>'.$this->build_friendly_names($field->name).':</strong>'
                                                . '</td>'
                                                . '<td class="tabla_'.$field->name.'">'
                                                . '<input onfocus="cambiaFecha();"  type="text" class="datepicker" name="'.$field->name.'" id="'.$field->name.'" value="'.$value_field.'" />'
                                                . '</td>';
                                        
				}elseif($field->name == 'clave'){
					$page .= '<td align="right"  valign="top" width="150" class="tabla_'.$field->name.'">(todo en mayusculas) '
                                                . '<strong>'.$this->build_friendly_names($field->name).':</strong>'
                                                . '</td class="tabla_'.$field->name.'">'
                                                . '<td>'
                                                . '<input type="password" name="'.$field->name.'" id="'.$field->name.'" />'
                                                . '</td>';
                                        
				}elseif($field->type == 'time'){
					$page .= '<td align="right"  valign="top" width="150" class="tabla_'.$field->name.'">'
                                                . '<strong>'.$this->build_friendly_names($field->name).':</strong>'
                                                . '</td>'
                                                . '<td class="tabla_'.$field->name.'">'
                                                . '<input onfocus="cambiaFecha();" type="text" name="'.$field->name.'" id="'.$field->name.'" value="'.$row[$i].'" />' 
                                                . '</td>';
                                        
				}elseif($field->name == 'descripcion'){
					if($this->mostrar=='editable'){
					$page .= '<td align="right"  width="150" class="tabla_'.$field->name.'"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td class="tabla_'.$field->name.'"><input '.$clase.'  type="text" name="'.$field->name.'" id="'.$field->name.'" value="'.$row[$i].'" />';
					}else{
					$page .= '<input type="hidden" name="'.$field->name.'" id="'.$field->name.'" value="'.$row[$i].'" />';
					}
				}
                                elseif ($field->name == 'email') {
                                        $page .= '<td align="right" class="tabla_'.$field->name.'"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td class="tabla_'.$field->name.'"> <input '.$clase.' type="text" name="'.$field->name.'" id="'.$field->name.'" value="'.$row[$i].'" size="35" /></td>';
		          	}
                                
                                
                                
                                else{
					$page .= '<td align="right" class="tabla_'.$field->name.'"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td class="tabla_'.$field->name.'"> <input required class="mayuscula" type="text" name="'.$field->name.'" id="'.$field->name.'" value="'.$row[$i].'" size="35" /></td>';
				}
                        }
                        else{
				$page .= '<td align="right" class="tabla_'.$field->name.'"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td class="tabla_'.$field->name.'">'.$row[$i].'<input type="hidden" name="id" value="'.$row[$i].'"></td>';
			}
			$i++;
			$page .= '</tr>';
		}
                
                if(!empty($this->maestro_id)) {$input_maestro = '<input type="hidden" name="maestro_id" id="maestro_id" value="'.$this->maestro_id.'">';}
		$page .= '<tr><td>&nbsp;</td><td>
                          '.$input_maestro.'
			  <input type="submit" value="Guardar" /></td></tr>'
		 		. '</table>'
		 		. '</form><br><br>';
//		 		. '<a href="'.$_SERVER['PHP_SELF'].'">Volver al listado</a>';    

		echo $page;
                echo "</div></div>";
                echo "<script type='text/javascript' src='libjs/".$this->table.".js'></script>";
	}

	/**
	* This method updates the record
	*
	*/
	function update(){
		$select = mysql_query('SELECT * FROM '.$this->table.' WHERE id = '.intval($_POST['id']));
		$num = mysql_num_fields($select);
		$update = 'UPDATE '.$this->table.' SET ';
		$i = 1;
		$comma = '';
		while($i <= $num){
			$column = mysql_fetch_field($select);
			if($column->name != 'id' && $column->name != 'created_at' && $column->name != 'updated_at'){
					$update .= $comma.$column->name.' = ';
                                        
                                        $value = ($_POST["$column->name"]);
                                        //echo 'valor anterior: '.$value;
                                        $value = $this->date_transform_usa($value);
                                        //echo 'valor nuevo: '.$value.'   ';
					$update .= (get_magic_quotes_gpc) ? '\''.mysql_real_escape_string(stripslashes($value)).'\'' : '\''.mysql_real_escape_string($value).'\'';
					$comma =', ';
			}
			$i++;
		}
		$update .= '  WHERE id = '.intval($_POST['id']);
                //echo $update;
                
                 $id_registro_creado=  DevuelveValor('MODIFICADO', 'id', 'estados', 'descripcion');
                $sql_maquina="INSERT INTO `registro_de_estados` (`id`, `descripcion`, `tabla`, `registro_id`, `fecha_cambio_estado`, `estado_id`, `usuario_id`) "
                        . "VALUES (NULL, '".$_POST['descripcion']."', '".$this->table."', '".$_POST['id']."', CURDATE(), $id_registro_creado, '".$_SESSION['usuario_id']."');";
               
                mysql_query($sql_maquina);
		mysql_query($update) or die(mysql_error());
		$this->list_table('<div style="color:#090;"><p>Registro modificado..</p></div>');
                
                
                
	}

	/**
	* Este metodo borra un registro
	*
	*/
	function delete_row(){
                $descripcion=DevuelveValor($_POST['id'], 'descripcion', $this->table, 'id');
		mysql_query('DELETE FROM '.$this->table.' WHERE id = '.$_POST['id']);
                $id_registro_creado=  DevuelveValor('ELIMINADO', 'id', 'estados', 'descripcion');
                $sql_maquina="INSERT INTO `registro_de_estados` (`id`, `descripcion`, `tabla`, `registro_id`, `fecha_cambio_estado`, `estado_id`, `usuario_id`, `empresa_id`) "
                        . "VALUES (NULL, '$descripcion', '".$this->table."', '0', CURDATE(), $id_registro_creado, '".$_SESSION['usuario_id']."', '1');";
                
                 mysql_query($sql_maquina);
                if (mysql_error() == true) echo "La operacion no se puede realizar. El registro esta asociado a otros datos. Verifique su accion<br><br>";

		if(!empty($this->maestro_id)) {$where = " WHERE ".$this->tipo_comprobante."_maestro_id = ".$this->maestro_id;}

		$this->list_table('<div style="color:#090;"><p>Registro borrado..</p></div>',$where);
	}
        
        

	/**
	* Muestra el registro en un formato ameno para imprimir
	*
	*/       

        
	function view_row(){
                if ($_POST['ver_relacion'] == 'SI') { 
                    $this->table = $_POST['tabla_relacion'];
                    $relacion_value = $_POST['id_value'];
                    $relacion_name  = $_POST['id_name'];
                    $id = DevuelveValor($relacion_value, 'id', $this->table, $relacion_name);
                    echo '
                        <form name="newrecord_'.$this->filter[1].'" id="newrecord_'.$this->filter[1].'" action="'.$_SERVER['PHP_SELF'].'" method="post" style="display:inline">
                        '.$input_maestro.'
                        <input type="hidden" name="variablecontrolposnavegacion" value="new">
                        <input type="hidden" name="table" value="'.$this->table.'">
                        <input type="hidden" name="ventananueva" value="si">
                        <a href="javascript:document.newrecord_'.$this->filter[1].'.submit('.$this->filter[1].')">
                        Registro nuevo
                        </a>
                        </form>
                        <br /><br />
                        ';
                    }
                   
                
                else {
                    $id = $_POST['id'];
                }
                
                $page = '';
		$fields = mysql_query('SELECT * FROM '.$this->table) or die(mysql_error());
		$select = mysql_query('SELECT * FROM '.$this->table.' WHERE id = '.intval($id));
		$row = mysql_fetch_row($select);
		$i = 0;
                  
              
		echo '<h2>Detalle de registro: <strong>'.$this->build_friendly_names(strtoupper(substr($this->table,0,-1))).'</strong></h2><br>';
		$page .= '<form action="'.$_SERVER['PHP_SELF'].'#'.$this->filter[1].'" method="POST" name="crear_'.$this->filter[1].'" enctype="multipart/form-data">'
				. '<input type="hidden" name="variablecontrolposnavegacion" value="update">'
		 		. '<table cellpadding="2" class="table table-bordered table-striped" cellspacing="0" border="1" width="500" bgcolor="#ffffff">';
		while($i < mysql_num_fields($fields)){
			$field = mysql_fetch_field($fields);
			if($field->name != 'id'){
                                                        
//				if (strpos($field->name, "fecha") !== false) { $clase = ':date_au :required';	}
//				else { $clase = ':required'; }
                                $clase = ':required'; 
				$page .= '<tr>';
				// reviso las claves foraneas..
				if(substr($field->name, -3) == '_id'){
                                        $campo = substr($field->name,0,-3);
                                        $tabla = $campo.'s';
                                        $valor_campo = DevuelveValor($row[$i], 'descripcion', $tabla, 'id');
					$page .= '<td align="right"  valign="top" width="200" class="tabla_'.$field->name.'"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td class="tabla_'.$field->name.'">'.$valor_campo.'</td>';

				}elseif($field->blob == 1){
					$page .= '<td align="right"  valign="top" width="200"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$i].'</td>';
				}elseif($field->type == 'timestamp'){
					$page .= '<input type="hidden" name="'.$column->name.'" id="'.$column->name.'" value="'.date("Y-m-d H:i:s").'"/>';
					//$page .= '<td align="right"  width="200"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$i].'</td>';
				}elseif($field->type == 'date'){ $value_field = $this->date_transform_lat($row[$i]); 
					$page .= '<td align="right" class"tabla_'.$field->name.'" valign="top" width="200"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td class"tabla_'.$field->name.'">'.$value_field.'</td>';
				}elseif($field->type == 'time'){
					$page .= '<td align="right"  valign="top" width="200"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$i].'</td>';
				}elseif($field->name == 'descripcion'){
					if($this->mostrar=='editable'){
					$page .= '<td align="right"    width="200"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td>'.$row[$i].'</td>';
					}else{
					$page .= '<input type="hidden" name="'.$field->name.'" id="'.$field->name.'" value="'.$row[$i].'" />';
					}
				}else{
					$page .= '<td align="right" class"tabla_'.$field->name.'"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td class"tabla_'.$field->name.'">'.$row[$i].' </td>';
				}

			}else{
				$page .= '<td align="right" class"tabla_'.$field->name.'"><strong>'.$this->build_friendly_names($field->name).':</strong></td><td class"tabla_'.$field->name.'">'.$row[$i].'<input type="hidden" name="id" value="'.$row[$i].'"></td>';
			}
			$i++;
			$page .= '</tr>';
		}
		$page .= '<tr><td>&nbsp;</td><td></td></tr>'
				 . '</table>'
				 . '</form>'
                                 . '<input type="button" name="imprimir" value="Imprimir" onclick="window.print();">';
                

		echo $page;
	}
        
        
        
        
        

	/**
	* Construye una busqueda para ser pasada a list_table
	*
	*/
	function search(){
		if(!empty($_POST['searchterm'])){
			// Seguridad
			$searchterm = (get_magic_quotes_gpc) ? '\''.mysql_real_escape_string(stripslashes($_POST['searchterm'])).'\'' : '\''.mysql_real_escape_string($_POST['searchterm']).'\'';

			$field = (get_magic_quotes_gpc) ? '\''.mysql_real_escape_string(stripslashes($_POST['field'])).'\'' : '\''.mysql_real_escape_string($_POST['field']).'\'';
			switch ($_POST['compare']){
				default:
					$compare = '1';
					$compare = NULL;
					$searchterm = NULL;
					break;
				case '=':
					$compare = ' = ';
					break;
				case '>':
					$compare = ' > ';
					break;
				case '<':
					$compare = ' < ';
					break;
				case 'LIKE':
					$compare = ' LIKE ';
					$searchterm = (get_magic_quotes_gpc) ? "'%".mysql_real_escape_string(stripslashes($_POST["searchterm"]))."%'" : "'%".mysql_real_escape_string($_POST["searchterm"])."%'";
					break;

			}
			$where = ' WHERE '.$_POST['field'].$compare.$searchterm;
		}else{
			$where = NULL;
		}
                
		$this->list_table('<div style="color:#090">Resultados de la b&uacute;squeda..</div>',$where);

	}


	/**
	* Genera selects est�ticos. Este se usa en el "editar fila"
	* **IMPORTANTE:**
	* Los nombres de las tablas deben terminar en s, las fk deben terminar en
	* _id y las tablas de las fk deben tener un campo 'descripcion'
	* Si ademas tienen un campo 'detalle', este es usado como title del
	* option
	*
	* string $field				// campo fk
	* string $value				// valor
	*/
	function build_foreign_key_dropdowns($field, $value = null) {
		// revisa las relaciones
		$match = FALSE;
		$dd = '';
		for($i=0; $i<count($this->singular); $i++){
			$match = preg_match('/^'.$this->singular[$i].'$/', substr($field, 0, -3));
			if($match){break;}
		}
		if($match){
			$foreignTable = str_replace($this->singular, $this->plural, substr($field, 0, -3));
		}else{
			// corta _id y pluraliza el nombre
			$foreignTable = substr($field, 0, -3);
			(substr($foreignTable, -1) != 'y') ? $foreignTable .= 's' : $foreignTable = substr($foreignTable, 0, -1).'ies';
		}   
                $i=0; 
                $ban=0;
               foreach($this->fk as $fk){
                if ($field == $fk) {
                    $where_fk = ' WHERE '. $this->fk_filtro[$i].'='.$this->fk_id[$i] ;
                    $sql_fk = 'SELECT * FROM '.$foreignTable.$where_fk." ORDER BY descripcion";
                    $select = mysql_query($sql_fk) or die(mysql_error());
                    $ban=1;
                }$i++;
                
                }
                if($ban==0){
                    $sql_fk = 'SELECT * FROM '.$foreignTable." ORDER BY descripcion";
                    $select = mysql_query($sql_fk) or die(mysql_error());

               
               } 
             //  echo $sql_fk."<br>";
                   
		$tabla= $this->table;
                $cadena    = $field;
                $comment   = DevuelveComentario($cadena, $tabla);
                $bandera=  substr($comment, 0, 4);
                
		//$select = mysql_query('SELECT * FROM '.$foreignTable.' WHERE empresa_id = '.$this->empresa_id.' OR empresa_id = 14') or die(mysql_error());
		//$foreign = mysql_fetch_assoc($select);
		$dd .= '<td align="right" class=tabla_'.$field.'><strong>'.$this->build_friendly_names(substr($field, 0, -3)).'</strong></td><td class=tabla_'.$field.'>'
		. '<select class="js-example-basic-single validar-select" name="'.$field.'" id="'.$field.'">';
		while($foreign = mysql_fetch_assoc($select)){
			$dd .= "<option title=\"".$foreign['detalle']."\" name=\"".$foreign['descripcion']."\"  value='".$foreign['id']."'";
			if ($foreign['id'] == $value){ $dd .= ' selected';}
			if (!empty($foreign['descripcion'])){
				$dd .= '>'.$foreign['descripcion'].'</option>';
			}else{
				$dd .= '>'.$foreign['id'].'</option>';
			}
		};
		$dd .= '</select>';
                if($bandera==='(+) ')
                {
                    $dd.= '<button type="button" class="formulario" value="'.$field.'">(+)</button></td>';
                }
                else if($bandera==='(+)_'){
                    $dd.= '<button type="button" class="wizard" value="'.$field.'">(+)</button></td>';
                }
		return $dd;
	}



	/**
	* Genera los SELECT dinamicos. Dependen de la cantidad de datos de la
	* tabla
	* **IMPORTANTE:**
	* Los nombres de las tablas deben terminar en s, las fk deben terminar en
	* _id y las tablas de las fk deben tener un campo 'descripcion'
	* Si ademas tienen un campo 'detalle', este es usado como title del
	* option
	*
	* string $field				// campo fk
	* string $value				// valor
	*/
	function build_foreign_key_dropdowns_auto($field, $value = null) {
		// revisa las relaciones
		$match = FALSE;
		$dd = '';
		for($i=0; $i<count($this->singular); $i++){
			$match = preg_match('/^'.$this->singular[$i].'$/', substr($field, 0, -3));
			if($match){break;}
		}
		if($match){
			$foreignTable = str_replace($this->singular, $this->plural, substr($field, 0, -3));
		}else{
			// corta _id y pluraliza el nombre
			$foreignTable = substr($field, 0, -3);
			(substr($foreignTable, -1) != 'y') ? $foreignTable .= 's' : $foreignTable = substr($foreignTable, 0, -1).'ies';
		}

                //$where_fk = ' WHERE empresa_id LIKE '.$this->empresa_id.' OR empresa_id = '.$this->control_admin;

                
                
                if (!empty($this->cliente_id) AND ($foreignTable == 'equipo_clientes')) $where_fk = ' WHERE cliente_id LIKE '.$this->cliente_id;
                
                
                
                $sql_fk = 'SELECT * FROM '.$foreignTable.$where_fk;
                //echo $sql_fk;
                
		$select = mysql_query($sql_fk) or die(mysql_error());
		$foreign = mysql_fetch_assoc($select);
		$totalrows = mysql_num_rows($select);
		if ($totalrows>=15) {
		$dd .= '<script type="text/javascript">
		$(function()
		{
			// Updated script
			var categories = $.map($("#s'.$field.' option"),function(e, i)
			{
			return e;
			});

			$("#my'.$field.'").autocomplete(categories,
			{
			matchContains : true,
			formatItem : function(item) { return item.text; }
			});
			// Added to fill hidden field with option value
			$("#my'.$field.'").result(function(event, item, formatted)
			{
			$("#'.$field.'").val(item.value);
			}
		)});
		</script>';
		$dd .= '<td align="right" ><strong>'.$this->build_friendly_names(substr($field, 0, -3)).'</strong></td><td>
                    <input type="text" id="'.$field.'" name="'.$field.'" size="1" readonly="true">
                    </input>
                    <input type="text" id="my'.$field.'" class=":required" size="31" title="Cantidad de registros: '.$totalrows.'">
                    </input>'.'<select id="s'.$field.'" style="display: none">
                    
                    ';

		do{
			$dd .= "<option title=\"".$foreign['detalle']."\"   value='".$foreign['id']."'";
			if ($foreign['id'] == $value){ $dd .= ' selected';}
			if (!empty($foreign['descripcion'])){
				$dd .= '>'.$foreign['descripcion'].'</option>';
			}else{
				$dd .= '>'.$foreign['id'].'</option>';
			}
		}while($foreign = mysql_fetch_assoc($select));
		$dd .= '</select></td>';
                    //<input type="button" name="agregar" value="Agregar" onclick="window.open(\'formulario_agrega_ajax.php?ventananueva=si&campo='.$field.'\', \'_blank\', \'width=1024,height=600,top=100,left=100,scrollbars=yes\')">
                        
		return $dd;
		}else{  //Este else es del if para los select dinamicos
		$dd .= '<td align="right" ><strong>'.$this->build_friendly_names(substr($field, 0, -3)).'</strong></td><td>'
		. '<select name="'.$field.'" id="'.$field.'">';
		do{
			$dd .= "<option title=\"".$foreign['detalle']."\" name=\"".$foreign['descripcion']."\" value='".$foreign['id']."'";
			if ($foreign['id'] == $value){ $dd .= ' selected';}
			if (!empty($foreign['descripcion'])){
				$dd .= '>'.  $this->quitar_tildes($foreign['descripcion']).'</option>';
			}else{
				$dd .= '>'.$foreign['id'].'</option>';
			}
		}while($foreign = mysql_fetch_assoc($select));
		$dd .= '</select> </td>';
                    //<input type="button" name="agregar" value="Agregar" onclick="AgregaASelect();">
                   
		return $dd;


		}
	}




	/**
	* **IMPORTANTE:**
	* Los nombres de las tablas deben terminar en s, las fk deben terminar en
	* _id y las tablas de las fk deben tener un campo 'descripcion'
	*
	* string $field				// campo fk
	* string $value				// valor
	*/
	function build_foreign_key_title($field, $value) {
		// revisa las relaciones
		$match = FALSE;
		$dd = '';
		for($i=0; $i<count($this->singular); $i++){
			$match = preg_match('/^'.$this->singular[$i].'$/', substr($field, 0, -3));
			if($match){break;}
		}
		if($match){
			$foreignTable = str_replace($this->singular, $this->plural, substr($field, 0, -3));
		}else{
			// corta _id y pluraliza el nombre
			$foreignTable = substr($field, 0, -3);
			(substr($foreignTable, -1) != 'y') ? $foreignTable .= 's' : $foreignTable = substr($foreignTable, 0, -1).'ies';
		}
		$select = mysql_query("SELECT id, descripcion FROM $foreignTable WHERE id=$value") or die(mysql_error());
		$foreign = mysql_fetch_assoc($select);
		if (!empty($foreign['descripcion'])){
				//ESTO LO PODRÍA HACER CON LIGHTBOX JQUERY
                   $dd .='
                        
                        <form name="searchbar'.$foreign['id'].'" id="searchbar'.$foreign['id'].'" action="'.$foreignTable.'.php" method="post"  target="ventanaForm" onsubmit="window.open(\'\', \'ventanaForm\', \'width=1024,height=400,top=100,left=100,scrollbars=yes\')">
                        <input type="hidden" name="variablecontrolposnavegacion" value="search">
                        <input type="hidden" name="field" value="descripcion">
                        <input type="hidden" name="compare" value="=">
                        <input type="hidden" name="searchterm" value="'.$foreign['descripcion'].'">
                       '.$foreign['descripcion'].'  
                        <input type="hidden" name="ventananueva" value="si"></form>';
		}else{
				$dd .= $foreign['id'];
		}
		while($foreign = mysql_fetch_assoc($select));
		return $dd;
	}

// <a href="#" onclick="document.searchbar'.$foreign['id'].'.submit();return false">'.$foreign['descripcion'].' - '.$foreign['id'].'</a>


	/**
	* Paginacion
	*
	* int $total 	// Numero total de filas de la tabla
	*
	*/
	function paginate($total = 1) {
		// paginacion
		if($total>$this->max_records){
		// Construye los links del recordset
		$num_pages = ceil($total / $this->max_records);
		$nav = '';

		// Link a la pagina previa si es necesario
		if($this->page > 1)
		$nav .= '<a href="'.$_SERVER['PHP_SELF'].'?page=' . ($this->page-1) . '">&lt;&lt; Anterior</a> |';

		for($i = 1; $i < $num_pages+1; $i++)
		{
		if($this->page == $i)
		{
		  //
		  $nav .= ' <strong>'.$i.'</strong> |';
		}
		else
		{
		  // Link a la pagina
		  $nav .= ' <a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'">'.$i.'</a> |';
		}
		}

		// Link a la pagina siguiente si es necesario
		if($this->page < $num_pages)
		$nav .= ' <a href="'.$_SERVER['PHP_SELF'].'?page=' . ($this->page+1) . '">Siguiente &gt;&gt;</a>';

		// Dibuja un pipe entre los numeros de pagina
		$nav = preg_replace('@|$@', "", $nav);
		echo $nav;
		}
	}

	/**
	* Barra de busqueda
	*
	*/
	function build_search_bar() {
		// Campos del menu
		$fielddropdown = '<select name="field">';
		$fieldselect = mysql_query('SHOW FIELDS FROM '.$this->table);
                $fielddropdown .= '<option value="descripcion">DESCRIPCION (por defecto)</option>';
                while($fields = mysql_fetch_assoc($fieldselect)){
			$fielddropdown .= '<option value="'.$fields['Field'].'">'.$this->build_friendly_names($fields['Field']).'</option>';
		}
		$fielddropdown .= '</select>';
		$searchterm = (!empty($_POST['searchterm'])) ? $_POST['searchterm'] : '' ;
		$search = '';
		$search .=  '<form name="searchbar" id="searchbar" action="'.$_SERVER['PHP_SELF'].'" method="post" style="display:inline;"><input type="hidden" name="variablecontrolposnavegacion" value="search">'
				. $fielddropdown
				. '<select name="compare">'
				. '<option value="LIKE">Contiene</option>'
                                . '<option value="=">Es igual a</option>'
				. '<option value="<">Es menor que</option>'
				. '<option value=">">Es mayor que</option>'
				. '</select>'
				. '<input type="text" name="searchterm" value ="'.$searchterm.'">'
				. '<input type="submit" name="Search" value="Buscar" />'
				. '</form><br /><br />';

		echo $search;

	}


	/**
	* Transforma los nombres de los campos en amistosos
	* Esto lo hace reemplazando el guion bajo por espacio y poniendo la primer
	* letra de cada palabra en mayusculas
	* string $field 	// pass the field name
	*
	*/
	function build_friendly_names($field) {
            
            $idioma=$_COOKIE["LangArabidopsis"];
           $tabla=  $this->table;
           $largo = strlen($tabla);
           $largo_comodin=strlen('comodin');
           $fichero = file_get_contents('idiomas/'.$idioma.'.txt', true);
           $traduccion = explode("-?", $fichero);
           foreach ($traduccion as $traduc) {
                substr($traduc, 0, $largo);
                if (substr($traduc, 0, $largo) === $tabla || substr($traduc, 0, $largo_comodin)==='comodin') {
                    $linea = explode(';', $traduc);
                    foreach ($linea as $lin) {
                        $linea_array = $string = explode(",", $lin);
                        if (strpos($linea_array[0], $field) !== FALSE) {
                            $field = $linea_array[1];
                        }
                    }
                }
            }
            
		return ucwords(str_replace('_', ' ', $field));
	}


	/**
	* Transforma una imagen en un dato blob para subirlo a la db
	*
	*/

	function image_transform_blob ($image) {

  		$mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png");
  		$name = $_FILES["$image"]["name"];
  		$type = $_FILES["$image"]["type"];
  		$tmp_name = $_FILES["$image"]["tmp_name"];
  		$size = $_FILES["$image"]["size"];

  		//Verifico si la imagen es valida
  		if(!in_array($type, $mimetypes))
    		die("El archivo no es una imagen v�lida");

		//Tomo la foto
		$fp = fopen($tmp_name, "rb");
  		$tfoto = fread($fp, filesize($tmp_name));
  		$tfoto = addslashes($tfoto);
  		fclose($fp);

		  // Borra archivos temporales si es que existen
		@unlink($tmp_name);

		return($tfoto);
	}

        static function quitar_tildes($cadena) {
                                $cade = $cadena;
                                $no = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹");
                                $si = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
                                $i = 0;
                                foreach ($no as $n) {
                                    $no_permitidas[] = mb_convert_encoding($n, 'ISO-8859-1', 'UTF-8');
                                    $permitidas[] = mb_convert_encoding($si[$i], 'ISO-8859-1', 'UTF-8');
                                    $i++;
                                }
                                $texto = str_replace($no_permitidas, $permitidas, $cade);
                                return $texto;
                            }

}