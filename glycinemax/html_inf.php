</td></tr></table>
</div>
<br><br>
<div align="center" class="pie">
    <img src="arabidopsis/auxiliar/arabidopsis_thaliana.png" width="100" align="middle">  &copy; 2015 - Sistema desarrollado por <strong>Oro Verde Digital SRL</strong>

</div> 
<!--[if lt IE 8]>
<script src="http://ie7-js.googlecode.com/svn/trunk/lib/IE8.js" type="text/javascript"></script>
<![endif]-->
<script src="js/jquery.eventCalendar.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8">
$(function(){
 if (!$.fn.dataTable.isDataTable('#lista')) {
            $('#lista').DataTable({
                "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "Todos"]],
                "language": {
                    "url": "DataTables-1.10.12/media/Spanish.json"
                }
            });
        }
})
$("input").change(function () {
        var tabla = $(this).attr('id');
        var a_validar = $(this).attr('class');
        var valor = $(this).val();
        if (typeof (a_validar) !== "undefined")
        {

            svalidar(tabla, a_validar, valor)
        }
    });

    $('.formulario').click(function () {
        var tabla = $(this).val();
        window.open("formulario_nuevo_ajax.php?tabla=" + tabla, "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=100,left=100,width=1024,height=600");
    });
    $('.wizard').click(function () {
        var tabla = $(this).val();
        window.open("formulario_nuevo_ajax.php?tabla=" + tabla + "&apertura=wizard", "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=100,left=100,width=1024,height=600");
    });

    $('.tabla_empresa_id').hide();
    $('.tabla_usuario_id').hide();
    //Se configura que todos lo campos de textos se escriben en mayusculas.
    $('.mayuscula').change(function(){
          $(this).val($(this).val().toUpperCase());
    })
    $('textarea').on('keyup', function () {
        $(this).val($(this).val().toUpperCase());
    })
</script>
<style>

    .select2-container .select2-selection--single .select2-selection__rendered {
        padding-right: inherit;
    }    </style>
</div>
</body>
</html>
