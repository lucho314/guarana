//onload
$(function () {
    $('#descripcion').prop('required', false);
    $('.tabla_descripcion').hide();
    $('#nombre').addClass('solo_letras');
    $('#apellido').addClass('solo_letras');
})

function creardescripcion() {
    afiliado = $('#afiliado_id option:selected').text();
    parentesco = $('#parentesco_id option:selected').text();
    nombreAfiliado = document.getElementById('nombre').value;
    apellidoAfiliado = document.getElementById('apellido').value;
    descripcion = afiliado + " / " + parentesco + " / " + nombreAfiliado + "-" + apellidoAfiliado;
    document.getElementById('descripcion').value = descripcion.toUpperCase();
    document.forms.crear.submit();
}
