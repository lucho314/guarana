<div class="panel panel-primary" align="center" style="width: 400px;">
<div class="panel-body">
<form class="form-horizontal" role="form" name="registro" method="post" action="confirmar_login.php">        
<div class="form-group">
    <label class="control-label col-sm-5" for="user">*Usuario (user):</label>
        <div class="col-sm-2">
            <input name="user" type="text" placeholder="Ingrese el usuario">
        </div>
</div>
 
 
<div class="form-group">
    <label class="control-label col-sm-5" for="pass">*Contrase&ntilde;a (pass):</label>
        <div class="col-sm-2">
            <input name="pass" type="password" placeholder="Ingrese la clave">
        </div>
</div>
    
    
 <fieldset class="form-group">
    <label class="control-label col-sm-5" for="lang">Idioma (Language)</label>
    <div class="col-sm-4">
    <select class="form-control" id="lang" name="lang">
      <option value="esp">Espa&ntilde;ol</option>
      <option value="eng">English</option>
      <option value="por">Portugues</option>
    </select>
    </div>
  </fieldset>    

<div class="form-group">
        <div class="col-sm-offset-3 col-sm-5">
            <button class="btn btn-primary" type="submit" name="Submit" value="Entrar">Entrar</button>
        </div>
</div>
</form>
</div>
</div>