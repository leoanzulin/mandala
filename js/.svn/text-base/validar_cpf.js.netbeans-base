/**
 * Faz a valiação do CPF no cliente
 */
function validar_cpf(cpf) {

    cpf = cpf.replace(/[^\d]+/g,'');

    if (cpf == '') return false;

    // Elimina CPFs inválidos conhecidos
    if (cpf.length != 11 ||
            cpf == '00000000000' ||
            cpf == '11111111111' ||
            cpf == '22222222222' ||
            cpf == '33333333333' ||
            cpf == '44444444444' ||
            cpf == '55555555555' ||
            cpf == '66666666666' ||
            cpf == '77777777777' ||
            cpf == '88888888888' ||
            cpf == '99999999999')
        return false;

    // Valida 1o digito
    soma = 0;
    for (i = 0; i < 9; i++)
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    digito1 = 11 - (soma % 11);
    if (digito1 == 10 || digito1 == 11)
        digito1 = 0;
    if (digito1 != parseInt(cpf.charAt(9)))
        return false;

    // Valida 2o digito
    soma = 0;
    for (i = 0; i < 10; i++)
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    digito2 = 11 - (soma % 11);
    if (digito2 == 10 || digito2 == 11)
        digito2 = 0;
    if (digito2 != parseInt(cpf.charAt(10)))
        return false;

    return true;

}
