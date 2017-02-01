var mensaje;

function setMensaje(msj=null){
    mensaje=msj;
    console.log(mensaje);
}

function validacion(){
    
    if(typeof creardescripcion === 'function') {
     creardescripcion();   
    }
   return (validar_input()&&validar_select());
    
}

function validar_input()
{
    bandera=0;
    $("input").each(function (){
        var tabla=$(this).attr('id');
        var a_validar=$(this).attr('class'); 
        var valor=$(this).val();
        if(typeof(a_validar)!== "undefined")
        {
            $('#'+tabla).focus();
            if(!svalidar(tabla,a_validar,valor)){
                bandera++
                return false;
            }
         }
    });
    if( bandera===0)
    {
        return true;
    }    
    else return false;
}



function svalidar(tabla,a_validar,valor)
{ 
    array_validar=a_validar.split(" ");
    for(var i=0; i<array_validar.length; i++)
    {
        switch (array_validar[i])
        {
            
                case 'solo_letras':
                    return validar_solo_letras(valor,tabla);
                    break;
                case 'cuit':
                    return validar_cuit(valor,tabla);
                    break;
                case 'email':
                    return validar_Email(valor,tabla);
                    break;
                case 'int':
                        return validar_entero_positivo(valor,tabla);
                         break;
                case 'real':
                        return validar_real_positivo(valor,tabla);
                        break;
                case 'tel':
                        return validar_tel(valor,tabla);
                        break;
                case 'solo_texto':
                        return validar_solo_letras(valor,tabla);
                        break
                default:
                if(i===array_validar.length-1)return true;
                        break;


        }
    }
}



function validar_solo_letras(validar,campo=null){
     $('.tabla_'+campo+' font').remove();
    var pattern=/^[a-zA-Z]*$/;
    if(pattern.test(validar)){
        return true;
    }
    else
    {
          $('.tabla_'+campo).eq(1).append("<font style='color: red'> SOLO SE ACEPTAN LETRAS</font>");
        return false;
    }
}

function validar_cuit(cuit,campo=null)
{
     $('.tabla_'+campo+' font').remove();
    total = 0;
     if(cuit<12) { $('.tabla_'+campo).eq(1).append("<font style='color: red'> CUIT NO VALIDO</font>"); return false};
    var array_cuit=cuit.split('-');
   
    cuit = cuit.replace(/-/g, '');
    mult = ['5', '4', '3', '2', '7', '6', '5', '4', '3', '2'];
    cuitchart = cuit.split('');
    for (i = 0; i < mult.length; i++) {
        total += cuitchart[i] * mult[i];
    }
    resto = total % 11;
    dig_verificador = resto === 0 ? 0 : resto === 1 ? 9 : 11 - resto;
    digito = cuit.substring(cuit.length - 1, cuit.length);
    if (parseInt(dig_verificador) === parseInt(digito))
    {
        return true;
    }
    else{
      $('.tabla_'+campo).eq(1).append("<font style='color: red'> CUIT NO VALIDO</font>");   
    return false;}
}


function validar_Email(email,campo=null) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    $('.tabla_'+campo+' font').remove();
    if (re.test(email))
    {
        return true;
    } else {
        $('.tabla_'+campo).eq(1).append("<font style='color: red'> EMAIL INVALIDO</font>");
        return false;
    }
}

function validar_entero_positivo(num,campo=null)
{
    
    if(mensaje===null)mensaje="SOLO SE ACEPTAN NUMEROS ENTEROS POSITIVOS";
    
    $('.tabla_'+campo+' font').remove();
    var patron = /^\d*$/;
    if (patron.test(num)) {
        return true;
    } else {
         $('.tabla_'+campo).eq(1).append("<font style='color: red'>"+mensaje+"</font>");

        return false
    }
}
function validar_real_positivo(num,campo){
    $('.tabla_'+campo+' font').remove();
    if ((isNaN(num)) || (num<0))
    {
        $('.tabla_'+campo).eq(1).append("<font style='color: red'>SOLO SE ACEPTAN NUMEROS REALES POSITIVOS</font>");
        return false;
    }
    else{
        return true;
    }
}
function validar_tel(tel,campo=null){
    var pattern= /[0-9]{10}/;
     $('.tabla_'+campo+' font').remove();
    if(pattern.test(tel)) {
        return true;
    }
    {
        $('.tabla_'+campo).eq(1).append("<font style='color: red'>TELEFONO NO VALIDO</font>");
         return false;
    } 
    
}


function validar_select() {
    var bandera = 0
    $(".validar-select").each(function ()
    {

        var valor = $('option:selected', this).text();
        var tabla = $(this).attr('id');
        if (valor.search('GENERICO') !== -1)
        {
            bandera = 1;

            $('.tabla_' + tabla).eq(1).append("<font style='color: red'>DEBE SER DIFERENTE DE GENERICO</font>");
            return false;

        } else
        {
            $('.tabla_' + tabla + ' font').remove();
        }
    })
    if (bandera === 1)
        return false
    else
        return true;
}