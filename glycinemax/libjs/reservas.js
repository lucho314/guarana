//onload
$(function () {
    $('#descripcion').prop('required', false);
    $('.tabla_descripcion').hide();
    $('#importe').prop('readonly',true);
    getPrecio($('#salon_id').val()); 
    $('#nombre').addClass('solo_letras');
    $('#apellido').addClass('solo_letras');
    
})

function creardescripcion() {
    nombreAfiliado = $('#afiliado_id option:selected').text();
    salon=$('#salon_id option:selected').text();
    turno=$('#turno_id option:selected').text();
    fecha=document.getElementById('fecha_reserva').value;
    descripcion=fecha+" / "+salon+" / "+turno;
    document.getElementById('descripcion').value = descripcion.toUpperCase();
}

$('#salon_id').change(function(){
   getPrecio($(this).val());
});


function getPrecio(idSalon){ 
$.post('ajax_salon.php',{id:idSalon,funcion:'getPrecio'},function(data){
         $('#importe').val(data);
        }, "html");
    
}
