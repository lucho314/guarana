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
           
            <h4 class="modal-title" id="myModalLabel">Iniciar Sesion</h4>
          </div> <!-- /.modal-header -->

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
          </div> <!-- /.modal-footer -->

        </div><!-- /.modal-content -->
  </body>
</html>