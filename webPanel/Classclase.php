<?php

if (isset($_POST['function'])) {
    $instancia = new Classclase();
    $function = $_POST['function'];
    $instancia->$function();
}

class Classclase {

    private $claseId;
    private $profesorId;
    private $alumnoId;
    private $conexion;

    function __construct() {
        include ("lib/conexion.php");
        $this->conexion = $conexion;
    }

    function setPost() {
        $this->profesorId = $_POST['profesorId'];
        $this->alumnoId = $_POST['alumnoId'];
    }

    function getTodos() {
        $query = "select clase.id, concat(pilotos.nombre,', ',pilotos.apellido)as piloto ,concat(instructores.nombre,', ',instructores.apellido)as instructor"
                . " ,fecha,inicio,fin FROM clase INNER JOIN pilotos on pilotos.id=clase.usuario_alumno_id "
                . "INNER JOIN instructores on instructores.id=clase.usuario_instructor_id";
        $resultado = mysqli_query($this->conexion, $query);

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

    function nuevaSimulacion() {
        $this->setPost();
        $query = "INSERT INTO `clase` (`usuario_instructor_id`, `usuario_alumno_id`,fecha) VALUES ($this->profesorId,$this->alumnoId,CURRENT_DATE);";
        mysqli_query($this->conexion, $query);
        $this->claseId = mysqli_insert_id($this->conexion);
        mysqli_close($this->conexion);
    }

    function getClaseId() {
        return $this->claseId;
    }

    function iniciarSimulacion() {
        $query = "UPDATE clase set inicio=CURRENT_TIME where id=" . $_POST['id'];
        mysqli_query($this->conexion, $query);
        mysqli_close($this->conexion);
    }

    function finalizarSimulacion() {
        $query = "UPDATE clase set fin=CURRENT_TIME where id=" . $_POST['id'];
        mysqli_query($this->conexion, $query);
        echo json_encode($this->getClase($_POST['id']));
        mysqli_close($this->conexion);
    }

    function getClase($id) {
        $query = "SELECT clase.*, TIMEDIFF(clase.fin,clase.inicio)as diferencia FROM clase WHERE id=" . $id;
        $resultado = mysqli_query($this->conexion, $query);
        while ($data = mysqli_fetch_assoc($resultado)) {
            $arreglo["data"][] = $data;
        }
        mysqli_free_result($resultado);
        $query = "SELECT concat(usuarios.nombre,', ',usuarios.apellido)as nombre FROM usuarios WHERE id=" . $arreglo["data"][0]["usuario_instructor_id"];
        $resultado = mysqli_query($this->conexion, $query);
        $nombreInstructor = mysqli_fetch_row($resultado);
        $arreglo["data"][0]["usuario_instructor_id"] = $nombreInstructor[0];
        mysqli_free_result($resultado);

        $query = "SELECT concat(usuarios.nombre,', ',usuarios.apellido)as nombre FROM usuarios WHERE id=" . $arreglo["data"][0]["usuario_alumno_id"];
        $resultado = mysqli_query($this->conexion, $query);
        $nombreAlumno = mysqli_fetch_row($resultado);
        $arreglo["data"][0]["usuario_alumno_id"] = $nombreAlumno[0];
        mysqli_free_result($resultado);

        return $arreglo;
    }

    function setComentario() {
        $query = "UPDATE clase set comentario='" . $_POST['comentario'] . "' where id=" . $_POST['id'];
        mysqli_query($this->conexion, $query);
        mysqli_close($this->conexion);
    }

    function eliminar() {
        $query = "DELETE FROM clase WHERE id=" . $_POST['idClase'];
        $resultado = mysqli_query($this->conexion, $query);
        if ($resultado) {
            $informacion['respuesta'] = "BIEN";
        } else {
            $informacion['respuesta'] = "error";
        }
        echo json_encode($informacion);
        mysqli_close($this->conexion);
    }

    function getFallas($claseId) {
        $query = "SELECT instrumentos.descripcion, evento, hora "
                . "FROM fallas INNER JOIN instrumentos "
                . "ON instrumentos.id=fallas.instrumento_id WHERE clase_id=$claseId";
        $resultado = mysqli_query($this->conexion, $query);
        while ($data = mysqli_fetch_assoc($resultado)) {
            $arreglo["data"][] = $data;
        }
        mysqli_free_result($resultado);
        return $arreglo;
    }

    function getHoras($claseId) {
        $query = "SELECT fecha, hora, forma_cambio from hora WHERE clase_id=$claseId";
        $resultado = mysqli_query($this->conexion, $query);
        while ($data = mysqli_fetch_assoc($resultado)) {
            $arreglo["data"][] = $data;
        }
        mysqli_free_result($resultado);
        return $arreglo;
    }

    function getClimas($claseId) {
        $query = "SELECT metar,hora,forma_cambio FROM clima WHERE clase_id=$claseId";
        $resultado = mysqli_query($this->conexion, $query);
        while ($data = mysqli_fetch_assoc($resultado)) {
            $arreglo["data"][] = $data;
        }
        mysqli_free_result($resultado);
        return $arreglo;
    }

    function generarFalla() {
        $instrumentoId = $_POST['instrumentoId'];
        $eventoId = $_POST['eventoId'];
        $claseId = $_POST['claseId'];
        $query = "INSERT INTO fallas values(NULL,$instrumentoId,$eventoId,$claseId,CURRENT_TIME)";
        $resultado = mysqli_query($this->conexion, $query);
        if ($resultado) {
            $informacion['respuesta'] = "BIEN";
        } else {
            $informacion['respuesta'] = "error";
        }
        echo json_encode($informacion);
        mysqli_close($this->conexion);
    }

    function cambioHora() {
        $claseId = $_POST['claseId'];
        $tipoCambio = $_POST['tipoCambio'];
        $gmt = $_POST['gmt'];
        $gmtArray = explode(" ", $gmt);
        $query = "INSERT INTO `hora` (`id`, `fecha`, `hora`, `forma_cambio`, `clase_id`) VALUES (NULL, '$gmtArray[0]', '" . $gmtArray[1] . "', '$tipoCambio', '$claseId')";
        $resultado = mysqli_query($this->conexion, $query);
        if ($resultado) {
            $informacion['respuesta'] = "BIEN";
        } else {
            $informacion['respuesta'] = "error";
        }
        echo json_encode($informacion);
        mysqli_close($this->conexion);
    }

    function setClima() {
        $claseId = $_POST['claseId'];
        $metar = mb_convert_encoding($_POST['metar'], "ISO-8859-1", "UTF-8");
        $formaCambio = $_POST['formaCambio'];
        $query = "INSERT INTO `clima` (`id`, `metar`, `hora`, `forma_cambio`, `clase_id`) VALUES (NULL, '$metar', CURRENT_TIME, '$formaCambio', '$claseId');";
        $resultado = mysqli_query($this->conexion, $query);
        if ($resultado) {
            $informacion['respuesta'] = "BIEN";
        } else {
            $informacion['respuesta'] = "error";
        }
        echo json_encode($informacion);
        mysqli_close($this->conexion);
    }

    function flightHistory() {
        $target_path = "Flight_History/";
        $name = "simulacion_" . $_POST['claseId'] . '.jpg';
        $target_path = $target_path . $name;
        if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
            echo "El archivo " . basename($_FILES['uploadedfile']['name']) . " ha sido subido";
        } else {
            echo "Ha ocurrido un error, trate de nuevo!";
        }
    }

}
