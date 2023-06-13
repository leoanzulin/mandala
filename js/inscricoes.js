/**
 * Script que cuida da validação dos campos do formulário de inscrição
 */

function validarCampo(campo, nomeFormatado) {
    var nomeElemento = '#Inscricao_' + campo;
    var elemento = $(nomeElemento);
    if (elemento == null || elemento.val() == null || elemento.val().trim() === '') {
        return "- " + nomeFormatado + " deve ser preenchido\n";
    }
    return '';
}

function validarCpf() {
    var cpf = $("#Inscricao_cpf").val();

    if (cpf.length != 11) return "- CPF inválido\n";
    if (!/^\d+$/.test(cpf)) return "- CPF deve conter apenas números\n";
    if (cpf == "00000000000" || cpf == "11111111111" || cpf == "22222222222" ||
            cpf == "33333333333" || cpf == "44444444444" || cpf == "55555555555" ||
            cpf == "66666666666" || cpf == "77777777777" || cpf == "88888888888" ||
            cpf == "99999999999") return "- CPF inválido\n";

    var soma;
    var resto;

    // verifica dígito 1
    soma = 0;
    for (i = 1; i <= 9; i++)
        soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
    resto = (soma * 10) % 11;
    if (resto == 10 || resto == 11) resto = 0;
    if (resto != parseInt(cpf.substring(9, 10))) return "- CPF inválido\n";

    // verifica dígito 2
    soma = 0;
    for (i = 1; i <= 10; i++)
        soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
    resto = (soma * 10) % 11;
    if (resto == 10 || resto == 11) resto = 0;
    if (resto != parseInt(cpf.substring(10, 11))) return "- CPF inválido\n";

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

function validarFormacao() {
    var numeroDeLinhasDaTabelaDeFormacao = $('#tabela-formacao tr').length;
    if (numeroDeLinhasDaTabelaDeFormacao <= 2) {
        return "- Preencha pelo menos uma formação acadêmica\n";
    }
    return '';
}

function validarHabilitacoes() {
    if (!$("#tipo_curso_especializacao").prop('checked')) return '';

    const habilitacoesSelecionadas = $("tr[data-habilitacao='edtec'] input[type='checkbox']:checked");
    return habilitacoesSelecionadas.length == 0
        ? '- Pelo menos uma habilitação deve ser selecionada\n'
        : '';
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

function validarDocumentos() {
    var camposDocumentos = {
        'documento_cpf': 'CPF (documento)',
        'documento_rg': 'RG (documento)',
        'documento_diploma': 'Diploma',
        'documento_curriculo': 'Currículo',
    };

    var mensagem = '';
    for (var campo in camposDocumentos) {
        var nomeElemento = '#Inscricao_' + campo;
        var elemento = $(nomeElemento);
        if (elemento == null || elemento.val() == null || elemento.val().trim() === '') {
            mensagem += "- " + camposDocumentos[campo] + " deve ser enviado\n";
        }
    }
    return mensagem;
}

function validar() {

    var campos = {
        'cpf': 'CPF',
        'identidade': 'Identidade',
        'orgao_expedidor': 'Órgão expedidor',
        'nome': 'Nome',
        'sobrenome': 'Sobrenome',
        'email': 'E-mail',
        'confirmarEmail': 'Confirmação de e-mail',
        'data_nascimento': 'Data de nascimento',
        'naturalidade': 'Naturalidade',
        'estado_civil': 'Estado civil',
        'cep': 'CEP',
        'endereco': 'Endereço',
        'numero': 'Número',
        'cidade': 'Cidade',
        'estado': 'Estado',
    };

    var mensagem = '';

    mensagem += validarCpf();
    for (campo in campos) {
        mensagem += validarCampo(campo, campos[campo]);
    }
    mensagem += validarSexo();
    mensagem += validarFormacao();
    mensagem += validarHabilitacoes();
    mensagem += validarDocumentos();

    if (mensagem !== '') {
        alert(mensagem);
        return false;
    }

    if (sessionStorage) {
        sessionStorage.setItem('cpf', $("#Inscricao_cpf").val());
    }

    return true;
}

function validarSemDocumentos() {

    var campos = {
        'cpf': 'CPF',
        'identidade': 'Identidade',
        'orgao_expedidor': 'Órgão expedidor',
        'nome': 'Nome',
        'sobrenome': 'Sobrenome',
        'email': 'E-mail',
        'data_nascimento': 'Data de nascimento',
        'naturalidade': 'Naturalidade',
        'estado_civil': 'Estado civil',
        'cep': 'CEP',
        'endereco': 'Endereço',
        'numero': 'Número',
        'cidade': 'Cidade',
        'estado': 'Estado',
    };

    var mensagem = '';

    mensagem += validarCpf();
    for (campo in campos) {
        mensagem += validarCampo(campo, campos[campo]);
    }
    mensagem += validarSexo();
    mensagem += validarFormacao();
    mensagem += validarHabilitacoes();

    if (mensagem !== '') {
        alert(mensagem);
        return false;
    }

    return true;
}
