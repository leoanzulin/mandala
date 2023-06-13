$(document).ready(function () {
    atualizarEstilo();

    $("tr[data-inscricao]").click(function() {
        var check = $(this).find("input");
        check.click();
        atualizarEstilo();
    });

    $("tr[data-inscricao] input[type='checkbox']").click(function() {
        $(this).click();
        atualizarEstilo();
    });
});

function atualizarEstilo() {
    $("tr[data-inscricao]").each(function (index) {
        var check = $(this).find("input[type='checkbox']");
        if (check.is(':checked')) {
            $(this).css('background-color', 'rgb(231, 240, 199)');
            $(this).css('color', 'black');
        } else {
            $(this).css('background-color', 'white');
            $(this).css('color', '#717171');
        }
    });
}

function validar() {
    if (!haPeloMenosUmaInscricaoSelecionada()) {
        return confirm('Não há nenhuma inscrição selecionada para o próximo período. Deseja continuar mesmo assim?');
    }
    return true;
}

function haPeloMenosUmaInscricaoSelecionada() {
    var haPeloMenosUmaInscricaoSlecionada = false;
    $("tr[data-inscricao]").each(function() {
        var check = $(this).find("input[type='checkbox']");
        if (check.is(':checked')) {
            haPeloMenosUmaInscricaoSlecionada = true;
            return false;
        }
    });
    return haPeloMenosUmaInscricaoSlecionada;
}
