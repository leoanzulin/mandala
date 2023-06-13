/**
 * Script que cuida do destaque nas habilitações selecionadas
 */

function atualizarEstiloDasHabilitacoes() {
    $("tr[data-habilitacao='edtec']").each(function (index) {
        var radio = $(this).find("input[type='radio']");
        if (radio.is(':checked')) {
            $(this).addClass('selecionado');
        }
        else {
            $(this).removeClass('selecionado');
        }
    });
}

$(document).ready(function () {
    atualizarEstiloDasHabilitacoes();

    $("tr[data-habilitacao='edtec']").click(function () {
        var radio = $(this).find("input");
        radio.prop('checked', true);
        atualizarEstiloDasHabilitacoes();
    });

    $("tr[data-habilitacao='edtec'] input[type='radio']").click(function () {
        $(this).prop('checked', true);
        atualizarEstiloDasHabilitacoes();
    });

    $("tr[data-habilitacao='edtec'] label").click(function () {
        var radio = $(this).parent().parent().find('input');
        radio.prop('checked', true);
        atualizarEstiloDasHabilitacoes();
    });
});

function validarCampo(campo, nomeFormatado) {
    var nomeElemento = '#Inscricao_' + campo;
    var elemento = $(nomeElemento);
    console.log(elemento.val());
    if (elemento == null || elemento.val() == null || elemento.val().trim() === '') {
        return "- " + nomeFormatado + " deve ser preenchido\n";
    }
    return '';
}

function validarHabilitacoes() {
    var seletorElementoHabilitacao1 = 'input[name="Inscricao[habilitacao1]"]:radio:checked';
    var seletorElementoHabilitacao2 = 'input[name="Inscricao[habilitacao2]"]:radio:checked';
    var habilitacao1 = $(seletorElementoHabilitacao1).val();
    var habilitacao2 = $(seletorElementoHabilitacao2).val();
    if (habilitacao1 == habilitacao2) {
        return "- As habilitações escolhidas devem ser diferentes\n";
    }
    return '';
}

function validar() {
    var campos = {
        'tipo_identidade': 'Tipo de identidade',
        'identidade': 'Identidade',
        'orgao_expedidor': 'Órgão expedidor',
    };
    var mensagem = '';

    for (var campo in campos) {
        mensagem += validarCampo(campo, campos[campo]);
    }
    mensagem += validarHabilitacoes();

    if (mensagem !== '') {
        alert(mensagem);
    }

    return mensagem === '' ? true : false;
}
