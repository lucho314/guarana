<html>
    <head>
        <link rel="stylesheet" href="css/login.css">
    </head>
    <body>
        <div class="login-page">
          
            <div class="form">
                <div class="form button" id="caja-texto">
				INICIAR SESI&Oacute;N
				</div>
                <form class="login-form" action="panel.php" method="post">
                    <input type="text" name="user" placeholder="Nombre Usuario"/>
                    <input type="password" name="pass" placeholder="Contrase&#241;a"/>
                    <button>Entrar</button>
                   
                </form>
            </div>
        </div>
    </body>
</html>

<STYLE>
#caja-texto{
    outline: 0;
    background: #4CAF50;
    /* width: 100%; */
    /* border: 0; */
    margin-bottom: 5%;
    padding: 15px;
    color: #FFFFFF;


}
</STYLE>