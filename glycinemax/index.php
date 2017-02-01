<?php
include_once('lib/funciones.php');
if (isset($_GET['cuil'])) {
    $cuil = $_GET['cuil'];
    $sql = "UPDATE usuarios SET activo =1 WHERE descripcion LIKE  '%$cuil'";
    mysql_query($sql);

}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Glycinemax</title>
        <script src="js/jquery-1.10.2.js"></script>
        <script src="bootstrap-3.3.6/js/bootstrap.min.js" ></script>
        <script src="js/login.js" ></script>
        <link rel="stylesheet" href="bootstrap-3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/login.css">
    </head>
    <body>
        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal" style="display: none" id="open"> 
            Iniciar Sesion
        </button>

        <div class=”modal fade” data-backdrop=”static” data-keyboard=”false” tabindex=”-1″ id=”MiModal” role=”dialog”>
             <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">

                        <button class="btn btn-success col-md-5" id="iniciar">Iniciar Sesion</button>
                        <button  class="col-md-5  col-md-offset-1 btn btn-primary" id="registrarse">Registrarse</h4></button>
                    </div> <!-- /.modal-header -->

                    <div id="login">
                        <div class="modal-body">
                            <form action="areas.php" method="POST">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="uLogin" placeholder="Usuario..." name="user">
                                        <label for="uLogin" class="input-group-addon glyphicon glyphicon-user"></label>
                                    </div>
                                </div> <!-- /.form-group -->

                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="uPassword" placeholder="Contraseña..." name="pass">
                                        <label for="uPassword" class="input-group-addon glyphicon glyphicon-lock"></label>
                                    </div> <!-- /.input-group -->
                                </div> <!-- /.form-group -->

                        </div> <!-- /.modal-body -->

                        <div class="modal-footer">
                            <button class="form-control btn btn-primary" type="submit">Aceptar</button>
                            </form>

                        </div>
                    </div>

                    <div id="registro" style="display: none">
                        <div class="modal-body">
                            <form action="registro.php" method="POST" id="form-registro">
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>CUIL</label>
                                        <input type="text" class="form-control" id="cuit" placeholder="CUIT ejem: 23-35668821-5" name="cuil" required autofocus>
                                    </div>
                                </div> <!-- /.form-group -->

                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Nombre</label>
                                        <input type="text" class="form-control" id="nombre" placeholder="Nombre..."  required readonly>
                                    </div> <!-- /.input-group -->
                                </div> <!-- /.form-group -->
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Apellido</label>
                                        <input type="text" class="form-control" id="apellido" placeholder="Apellido..."  required readonly>
                                    </div> <!-- /.input-group -->
                                </div> 
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Nombre de usuario</label>
                                        <input type="text" class="form-control" id="usuario" placeholder="usuario..." name="usuario" required>
                                    </div> <!-- /.input-group -->
                                </div> 
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control" id="email" placeholder="Email..." name="email" required>
                                    </div> <!-- /.input-group -->
                                </div>
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Contrase&ntilde;a</label>
                                        <input type="password" class="form-control" id="pass" placeholder="Contraseña..." name="pass" required>
                                    </div> <!-- /.input-group -->
                                </div> 
                                <div class="form-group">
                                    <div class="input-group">
                                        <label>Repetir contrase&ntilde;a</label>
                                        <input type="password" class="form-control" id="rep_pass" placeholder="Repetir Contraseña..."  required>
                                    </div> <!-- /.input-group -->
                                </div> 

                        </div> <!-- /.modal-body -->

                        <div class="modal-footer">
                            <button class="form-control btn btn-primary" type="submit">Aceptar</button>
                            </form>

                        </div>
                    </div>




                </div> <!-- /.modal-footer -->

            </div><!-- /.modal-content -->
    </body>
</html>


<script type="text/javascript">
    $('#cuit').change(function () {
        $.post('ajaxAfiliado.php', {'funcion': 'checkExist', 'cuit': $(this).val()}, function (dato) {
            console.log(dato);
            if (dato) {

                $('#email').val(dato.email);
                $('#nombre').val(dato.nombre);
                $('#apellido').val(dato.apellido);
                verificaExistente();
            } else {
                alert('no se encontro registro asociado a ese cuit, favor de contactarse con el administrador');
            }

        }, 'json')
    })

    $('#form-registro').submit(function (event) {
        event.preventDefault();
        datos=$(this).serialize();
        if (comparaContra()) {

            $.post('send_mail_afiliado.php',datos, function (data) {
                console.log(data);
                alert('SE ENVIO UN MAIL A SU CORREO PARA CONFIRMAR LA CUENTA. REVISE SU BANDEJA DE ENTRADA.')

            }).fail(function () {
                alert("error");
            })


        }
    })

    comparaContra = function () {
        if ($('#pass').val() !== $('#rep_pass').val()) {
            alert('las contraseñas deben coincidir');
            return false;
        }
        return true;
    }


    verificaExistente = function () {
        $('#aceptar').prop('disabled', false);
        cuit = $('#cuit').val();
        nom_usuario = $('#nom_usuario').val();
        $.post('ajaxUsuario.php', {'funcion': 'checkExist', 'cuit': cuit, 'usuario': nom_usuario}, function (data) {
            console.log(data);
            if (data) {
                alert("El nombre de usuario o el cuit ya existen");
                $('#aceptar').prop('disabled', true);
            }
        }, 'json');
    }


    $('#nom_usuario').change(function () {
        verificaExistente();
    })
    
    $('#iniciar').click(function(){
        $('#login').show();
        $('#registro').hide();
    })
    $('#registrarse').click(function(){
        $('#login').hide();
        $('#registro').show();
    })
</script>