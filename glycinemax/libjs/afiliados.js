//onload
$(function () {
    $('#descripcion').prop('required', false);
    $('.tabla_descripcion').hide();
    $('#nombre').addClass('solo_letras');
    $('#apellido').addClass('solo_letras');
    $('#cbu').addClass('int');
     $('#cuil').addClass('cuit');
    $('#cbu').removeAttr('onfocus').attr('onfocus',"setMensaje('CBU INVALIDO')");

})

function creardescripcion() {
    nombreAfiliado = document.getElementById('nombre').value;
    apellidoAfiliado = document.getElementById('apellido').value;
    cuilAfiliado = document.getElementById('cuil').value;
    legajoAfiliado = document.getElementById('legajo').value;
    descripcion = nombreAfiliado + " " + apellidoAfiliado + " / " + cuilAfiliado + " / " + legajoAfiliado;
    document.getElementById('descripcion').value = descripcion.toUpperCase();
    document.forms.crear.submit();
}
