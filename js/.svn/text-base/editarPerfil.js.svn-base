/**
 * Script que cuida da validação dos campos na edição do perfil
 */

function validarCampo(campo, nomeFormatado) {
    var nomeElemento = '#Inscricao_' + campo;
    var elemento = $(nomeElemento);
    if (elemento == null || elemento.val() == null || elemento.val().trim() === '') {
        return "- " + nomeFormatado + " deve ser preenchido\n";
    }
    return '';
}

function validarSexo() {
    var sexo1 = $("#Inscricao_sexo_0").is(":checked");
    var sexo2 = $("#Inscricao_sexo_1").is(":checked");
    if (!sexo1 && !sexo2) {
        return "- Sexo deve ser preenchido\n";
    }
    return '';
}

function validarBolsa() {
    var forma_pagamento = $('#Inscricao_forma_pagamento').val();
    if (forma_pagamento !== 'bolsa_parcial' && forma_pagamento !== 'bolsa_integral') {
        return '';
    }

    var mensagem = '';
    mensagem += validarCampo('criterio_bolsa', 'Critério para solicitação de bolsa');
    mensagem += validarCampo('renda_familiar_aproximada', 'Renda familiar aproximada');
    mensagem += validarCampo('justificativa_bolsa', 'Justificativa para solicitação de bolsa');

    return mensagem;
}

function validar() {
    var mensagem = '';
    var campos = {
        'data_nascimento': 'Data de nascimento',
        'naturalidade': 'Naturalidade',
        'estado_civil': 'Estado civil',
        'cep': 'CEP',
        'endereco': 'Endereço',
        'numero': 'Número',
        'cidade': 'Cidade',
        'estado': 'Estado',
    };

    for (campo in campos) {
        mensagem += validarCampo(campo, campos[campo]);
    }
    mensagem += validarSexo();
    if (mensagem !== '') {
        alert(mensagem);
    }

    return mensagem === '' ? true : false;
}
