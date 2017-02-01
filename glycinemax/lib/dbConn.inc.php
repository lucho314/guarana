<?
//Clase de base de datos
class  DbConn {

          //************* DATOS DE CONEXION CON LA BD  LOCAL **********************
          var $conn;               //Enlace a la conexion de base de datos
          var $usuariodb;          //Usuario de la base de datos
          var $clavedb;            //Contraseña de conexion a la base de datos
          var $servidordb;         //Servidor de base de datos
          var $dbname;             //Nombre de la base de datos

          //Arreglo de errores
          var $errores_DbConn = array
                               (
                               "Problemas conectandose a la base de datos.",
                               "Debe llenar todos los campos"
                               );

          function DbConn ($dbuser="welias", $dbpass="shinw1sa", $dbserver="localhost", $db="tevasaver")
          {
            $this->usuariodb = $dbuser;
            $this->clavedb = $dbpass;
            $this->servidordb =$dbserver;
            $this->dbname = $db;

          }
          function dbOpen()
          {
            //dbOpenrse a la base de datos con los parametros requeridos
            $this->conn = mysql_pconnect($this->servidordb, $this->usuariodb, $this->clavedb);
            // si la conexion tuvo exito
            if ($this->conn ) {
                //abrir base de datos
                mysql_select_db($this->dbname);
                return ($this->conn);
            } else {//si la conexion no tuvo exito
                print mysql_error();
                return 0;
              }
          }

          function dbClose()
          {
                   mysql_close($this->conn);
          }

          function execAnySql($cadenaSql, $orderBy="")
           {
            if ($this->dbOpen())
            {
                 if ($orderBy!="") $cadenaSql.=$cadenaSql." order by $orderBy";

                 $result = mysql_query($cadenaSql)
                        or die(mysql_error ());
                 return $result;

            }

           }
           //Devuelve el resultado de una consulta personalizada
          function execCustomSql ($campos, $tabla, $criterio="",$orderBy="")
          {
            $miCadenaSql= "SELECT ".$campos." FROM ". $tabla;
            if ($criterio !=""){
                 $miCadenaSql.=" WHERE ".$criterio;
                 }

            if ($orderBy!="") $miCadenaSql.=" order by $orderBy";
            if ($this->dbOpen()){

              $result = mysql_query($miCadenaSql)
                        or die(mysql_error ());
                 return $result;
            } else return false;

           }
           function getOneField ($fieldToGet,$fromTable, $crit="") {
                    if ($this->dbOpen()) {
                        //Construyo cadena de consulta con los parametros pasados
                        $sql_str ="SELECT ".$fieldToGet." FROM ".$fromTable;
                        //Adiciono criterio si lo tiene
                        if ($crit!="") {$sql_str.= " WHERE ".$crit; }
                        $result= mysql_query($sql_str);
                        //Si devolcio registro alguno.
                        if ($result) {
                            list($field1) = mysql_fetch_row($result);
                            return $field1;
                        } else  return "";
                    }else  return "";
           }

           //Devuelve verdadero si existe el valor especificado de un campo de texto especificado
           function existTextData($tabla,$campo,$valor) {

                    if ($this->dbOpen()) {
                        return ($this->getOneField ($campo,$tabla, $campo."='$valor'" )!="");
                    }
           }
           function getAllDataInArray($table,  $crit=""){

                    if ($this->dbOpen()){
                        $sql_str = "SELECT * from $table ";
                        if ($crit!="") { $sql_str.=" WHERE $crit";}
                        $result = $this->execAnySql($sql_str);
                        return mysql_fetch_array($result);
                    }
           }

           function paging($current, $total, $per_pages, $link) {

              $total_pages = ceil($total/$per_pages);
              $prev = $current - 1;
              $next = $current + 1;

              if ($current>1)
                $text = "<a href=\"$link$prev\">&laquo;</a> ";
              else
                   $text = "<b>&laquo;</b> ";
                   for ($i=1; $i<$current; $i++)
                        $text .= "<a href=\"$link$i\">$i</a> ";
                        $text .= "<b>$current</b> ";
                   for ($i=$current+1; $i<=$total_pages; $i++)
                        $text .= "<a href=\"$link$i\">$i</a> ";
                   if ($current<$total_pages)
                       $text .= "<a href=\"$link$next\">&raquo;</a>";
                   else
                       $text .= "<b>&raquo;</b>";
              return $text;
            }
}
?>