/**
 * Máscara para número do celular, que pode ter 8 ou 9 dígitos.
 * A função mascara_celular deve ser chamada passando o ID do input
 * que vai conter um número de celular.
 */
function mascara_celular(elemento)
{

    $(elemento).bind('input propertychange', function ()
    {
        var telefone = $(this).val();
        telefone = telefone.replace(/[^\d]/g, '');

        if (telefone.length === 0) {
            $(this).val('');
            return;
        }

        // Primeiro parêntese do DDD
        telefone = '(' + telefone;

        // Fecha o parêntese do DDD
        if (telefone.length >= 3)
            telefone = [telefone.slice(0, 3), ')', telefone.slice(3)].join('');

        // Colocação de hífens
        if (telefone.length >= 13)
            telefone = [telefone.slice(0, 9), '-', telefone.slice(9)].join('');
        else if (telefone.length >= 8)
            telefone = [telefone.slice(0, 8), '-', telefone.slice(8)].join('');

        // Tamanho máximo
        if (telefone.length > 14)
            telefone = telefone.substr(0, 14);

        $(this).val(telefone);

    });

    /**
     * Trata o backspace quando o cursor está logo depois de um
     * parêntese ou de um hífen.
     */
    $(elemento).on('keydown', function (event) {
        var key = event.keyCode || event.charCode;
        var telefone = $(this).val();
        
        if (key == 8) { // backspace
            if (telefone.length == 2)
                telefone = '';
            else if (telefone.length == 4)
                telefone = telefone.substr(0, 3);
            else if (telefone.length == 9)
                telefone = telefone.substr(0, 8);
        }

        $(this).val(telefone);
    });

}
