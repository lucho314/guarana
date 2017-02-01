<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <?php
    //require("aut_verifica.inc.php");
    include_once('lib/connect_mysql.php');
    include_once('lib/funciones.php');
    $ventananueva = LimpiarXSS($_POST['ventananueva']);
    ?>
   <head>
        <title>ARABIDOPSIS</title>
        <meta name="author" content="Walter R. Elias">
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <!--   <link type="text/css" rel="stylesheet" href="estilos.css"> -->
        <!--<link rel="stylesheet" href="menu.css">-->
        <!--<link rel="stylesheet" href="viejos/style.css">-->


     
        <script src="js/jquery-1.10.2.js"></script>
        <script src="js/jquery-ui.js"></script>
        <link rel="stylesheet" href="css/jquery-ui.css">




        <!--AUTOCOMPLETAR-->
        <link href="css/select2.css" rel="stylesheet" />
        <script src="js/select2.min.js"></script>
        <!--FIN AUTOCOMPLETAR-->



        <script type="text/javascript" src="js/jquery.timepicker.js"></script>
        <script src="js/vanadium.js" type="text/javascript"></script>

        <link href="css/facebox.css" media="screen" rel="stylesheet" type="text/css"/>
        <script src="js/facebox.js" type="text/javascript"></script>
        <script type="text/javascript">

            jQuery(document).ready(function ($) {
                $('a[rel*=facebox]').facebox({
                    loadingImage: 'css/loading.gif',
                    closeImage: 'css/closelabel.png'
                })
            })
        </script>

        <script language="javascript">
            function eltooltip(algo)
            {
                var ejecuta = window.event;
                var x = ejecuta.x;
                var y = ejecuta.srcElement.offsetTop + ejecuta.srcElement.offsetHeight + 10;
                var pos = tabla.style;
                tabla.innerHTML = '<table style="background-color:INFOBACKGROUND;font:8pt Arial;padding:3px 3px 3px 3px;border:1px solid INFOTEXT"><tr><td align=left>' + algo + '</td></tr></table>';
                pos.posTop = y
                pos.posLeft = x;
                pos.visibility = '';
            }
            
                   </script>




        <!--SCRIPTS PARA CALENDARIO-->

        <!-- Set the viewport width to deevvice width for mobile -->
        <meta name="viewport" content="width=device-width" />

        <!-- Core CSS File. The CSS code needed to make eventCalendar works -->
        <link rel="stylesheet" href="css/eventCalendar.css">

        <!-- Theme CSS file: it makes eventCalendar nicer -->
        <link rel="stylesheet" href="css/eventCalendar_theme_responsive.css">

        <!--FIN SCRIPTS CALENDARIO-->


        <script type="text/javascript">
            $('select').select2();

            $(document).ready(function () {
                $(".js-example-basic-single").select2();
            });



        </script>


        <!--LIBRERIAS SELECT HORA-->
        
        <script type="text/javascript" src="js/jquery.timepicker.js"></script>
        <link rel="stylesheet" type="text/css" href="css/jquery.timepicker.css" />

        <!--FIN LIBRERIAS SELECT HORA-->


        <script>
            $(function () {
                $(".datepicker").datepicker({
                    dateFormat: "dd-mm-yy"
                });
            });

            $(function () {
                $(".selecthora").timepicker({'timeFormat': 'H:i'});
            });


        </script>


        <!--FIN CALENDARIO SELECTOR DE FECHA-->

        <!--VENTANAS EMERGENTES-->


        <link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
        <script src="js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>

        <!--FIN VENTANAS EMERGENTES-->

        <!-- Datatable y estilos Agregados estilos y libreria datatable    F.C. -->
        <script type="text/javascript" language="javascript" src="DataTables-1.10.12/media/js/jquery.dataTables.js">
        </script>
        <link rel="stylesheet" href="bootstrap-3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="DataTables-1.10.12/media/css/jquery.dataTables.min.css">
         <link rel="stylesheet" href="DataTables-1.10.12/media/css/buttons.dataTables.min.css">
         
         

	<script type="text/javascript" language="javascript" src="DataTables-1.10.12/pluggin/dataTables.buttons.min.js">
	</script>
	<script type="text/javascript" language="javascript" src="DataTables-1.10.12/pluggin/jszip.min.js">
	</script>
         <script type="text/javascript" language="javascript" src="DataTables-1.10.12/pluggin/pdfmake.min.js">
	</script>
	<script type="text/javascript" language="javascript" src="DataTables-1.10.12/pluggin/vfs_fonts.js">
	</script>
	<script type="text/javascript" language="javascript" src="DataTables-1.10.12/pluggin/buttons.html5.min.js">
	</script>
         
         
         
       
        
        <script src="bootstrap-3.3.6/js/bootstrap.min.js" ></script>
    </head>
    <body style="background-color: rgb(122, 161, 174);">
        <br>

        <div align="center">

            <table bgcolor="#e8e6e6" width="1100" height="50" align="center" cellpadding="5" cellspacing="0" border="0">

                <tr>
                    <td colspan="2">




                <tr>

                    <td align="center" valign="top">
                        <br>