<nav class="navbar navbar-default navbar-inverse row" role="navigation">
    <div class="navbar-header">

        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
        </button> <a class="navbar-brand" href="#"></a>
    </div>

    <div class="collapse navbar-collapse simulador" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Simulacion<strong class="caret"></strong></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="panel.php">Simulaciones</a>
                    </li>
                    <li>
                        <a class="fancybox fancybox.iframe" id="fallas" href="#">Falla instrumento</a>
                    </li>
                    <li>
                        <a class="fancybox fancybox.iframe" id="mapa" href="#">Mapa</a>
                    </li>
                    <li>
                        <a class="fancybox fancybox.iframe" id="posicion-aterrizaje" href="#">Seter posicion de aterrizaje</a>
                    </li>
                    <li>
                        <a class="fancybox fancybox.iframe" id="clima">Clima</a>
                    </li>
                    <li>
                        <a class="fancybox fancybox.iframe" id="hora" href="#">Hora</a>
                    </li>
                    <li style="display: none">
                        <a class="fancybox fancybox.iframe" id="captura" href="#">Captura</a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Alumnos<strong class="caret"></strong></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="usuarios.php?tipo=2">Mis alumnos</a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Profesores<strong class="caret"></strong></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="usuarios.php?tipo=1">Lista</a>
                    </li>
                </ul>
            </li>
        </ul>



        <ul class="nav navbar-nav navbar-right">
         

            <li>
                <a href="aut_logout.php">Cerrar</a>
            </li>
        </ul>

    </div>

</nav>

<script>
    $(function () {
        $('.simulador').attr('disabled', 'disabled');
    })

</script>