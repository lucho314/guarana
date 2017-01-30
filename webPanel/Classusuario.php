<?php

//include 'lib/conexion.php';

$function = (isset($_POST['function'])) ? $_POST['function'] : "getUsuarios";
$usuarios = new Classusuarios();
$usuarios->$function();

class Classusuarios {

    private $usuarioId;
    private $nombre;
    private $apellido;
    private $usuario;
    private $pass;
    private $dni;
    private $tipo;

    function setPost() {
        $this->usuarioId = $_POST['usuarioId'];
        $this->nombre = $_POST['nombres'];
        $this->apellido = $_POST['apellidos'];
        $this->usuario = $_POST['usuario'];
        $this->pass = $_POST['password'];
        $this->tipo = $_POST['tipo'];
        $this->dni = $_POST['dni'];
    }

    function nuevo() {
        $this->setPost();
        if ($this->usuarioExiste()) {
            $informacion['respuesta'] = "EXISTE";
            echo json_encode($informacion);
            die();
        }
        include ("lib/conexion.php");
        $query = "INSERT INTO usuarios values (NULL,'$this->nombre','$this->apellido','$this->dni','$this->usuario','" . md5($this->pass) . "','$this->tipo',1) ";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado) {
            $informacion['respuesta'] = "BIEN";
        } else {
            $informacion['respuesta'] = "ERROR";
        }
        $this->usuarioId = mysqli_insert_id($conexion);
        $this->alumnoInstructor();
        echo json_encode($informacion);
        mysqli_close($conexion);
    }

    function editar() {
        $this->setPost();
        include ("lib/conexion.php");
        $query = "UPDATE usuarios set nombre='$this->nombre',apellido='$this->apellido',dni='$this->dni',usuario='$this->usuario',password='" . md5($this->pass) . "' WHERE id=$this->usuarioId";
        $resultado = mysqli_query($conexion, $query);
         $this->alumnoInstructor(true);
        if ($resultado) {
            $informacion['respuesta'] = "BIEN";
        } else {
            $informacion['respuesta'] = $query;
        }
        echo json_encode($informacion);
        mysqli_close($conexion);
    }

    function getUsuarios() {
        include ("lib/conexion.php");
        $query = "SELECT * FROM usuarios WHERE estado = 1 and tipo_usuario_id=" . $_GET['tipo'] . " ORDER BY id desc;";
        $resultado = mysqli_query($conexion, $query);

        if (!$resultado) {
            die($query);
        } else {
            if (mysqli_num_rows($resultado) === 0) {
                echo '{"data":[]}';
                die();
            }
            while ($data = mysqli_fetch_assoc($resultado)) {
                $arreglo["data"][] = $data;
            }
            echo json_encode($arreglo);
        }
        mysqli_free_result($resultado);
        mysqli_close($conexion);
    }

    function eliminar() {
        $usuarioId = $_POST['idusuario'];
        include ("lib/conexion.php");
        $query = "DELETE FROM `usuarios` WHERE id=$usuarioId";
        $resultado = mysqli_query($conexion, $query);
        if ($resultado) {
            $informacion['respuesta'] = "BIEN";
        } else {
            $informacion['respuesta'] = "error";
        }
        echo json_encode($informacion);
        mysqli_close($conexion);
    }

    private function usuarioExiste() {
        include ("lib/conexion.php");
        $query = "SELECT id FROM usuarios WHERE usuario = '$this->usuario'";
        $resultado = mysqli_query($conexion, $query);
        if (mysqli_num_rows($resultado) > 0)
            return true;
        return false;
    }

    static function listaUsuarioInstructor() {
        include ("lib/conexion.php");
        $query = "SELECT id,concat(nombre,'-',apellido)as dato FROM usuarios WHERE tipo_usuario_id = 1";
        $resultado = mysqli_query($conexion, $query);
        while ($data = mysqli_fetch_assoc($resultado)) {
            $instructor[] = $data;
        }
        return $instructor;
        mysqli_free_result($resultado);
        mysqli_close($conexion);
    }

        static function listaUsuarioAlumno() {
        include ("lib/conexion.php");
        $query = "SELECT id,concat(nombre,'-',apellido)as dato FROM usuarios WHERE tipo_usuario_id = 2";
        $resultado = mysqli_query($conexion, $query);
        while ($data = mysqli_fetch_assoc($resultado)) {
            $alumno[] = $data;
        }
        return $alumno;
        mysqli_free_result($resultado);
        mysqli_close($conexion);
    }
    
    
    function vacio(){}

    private function alumnoInstructor($editar=false) {
        include ("lib/conexion.php");
        if ($this->tipo === '2') {
            $profesorId = $_POST['profesor'];
            $query = ($editar)?"UPDATE alumno_instructor SET usuario_instructor_id=$profesorId WHERE usuario_alumno_id=$this->usuarioId":"INSERT INTO alumno_instructor VALUES(NULL,'$profesorId','$this->usuarioId')";
            mysqli_query($conexion, $query);
            mysqli_close($conexion);
        }
    }
    
     function idInstructorAlumno(){
         include ("lib/conexion.php");
         $query = "SELECT usuario_instructor_id FROM alumno_instructor where usuario_alumno_id=".$_POST['id'];
         $resultado=  mysqli_query($conexion, $query);
         $instructorId=  mysqli_fetch_row($resultado);
        echo json_encode($instructorId[0]);
    }

}
