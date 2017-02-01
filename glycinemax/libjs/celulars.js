//onload
$(function () {
    $('#descripcion').prop('required', false);
    $('.tabla_descripcion').hide();
    $('#imei').addClass('int');
    $('#imei').removeAttr('onfocus').attr('onfocus',"setMensaje('IMEI INVALIDO')");
})

function creardescripcion() {
    afiliado = $('#afiliado_id option:selected').text();
    imeiCelular=document.getElementById('imei').value;
    numeroCelular=document.getElementById('nro_telefono').value;
    descripcion=afiliado+" / "+numeroCelular+" / "+imeiCelular;
    document.getElementById('descripcion').value = descripcion.toUpperCase();
    document.forms.crear.submit();
}
