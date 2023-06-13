/**
 * Controla a inscrição dos usuários no curso.
 */

var app = angular.module("inscricaoApp", ['ngResource', 'servicos']);

app.controller('controlador', ['$scope', 'Formacao',
        function($scope, Formacao) {

    var idInscricao = recuperarId(window.location.href) || -1;
    $scope.cpf = '';
    $scope.formacoes = [];
    // Se estiver na tela de editar inscrições, reucpera as formações que
    // foram informadas
    if (idInscricao != -1) {
        var formacoes = Formacao.get({'id': idInscricao}, function() {
            for (var i = 0; i < formacoes.length; ++i) {
                formacoes[i].nivel_ = formacoes[i].nivel;
                formacoes[i].nivel = dePara[formacoes[i].nivel_];
                formacoes[i].conclusao = formacoes[i].ano_conclusao;
                $scope.formacoes.push(formacoes[i]);
            }
        });
    }

    function recuperarId(url) {
        var resultado = /id=(\d+)$/.exec(url);
        if (!resultado) {
            return null;
        }
        return resultado[1];
    }

    var novaFormacao = {
        "id": 0,
        "nivel": "",
        "nivel_": "",
        "curso": "",
        "instituicao": "",
        "conclusao": ""
    };

    var dePara = {
        "graduacao": "Graduação",
        "especializacao": "Especialização",
        "mestrado": "Mestrado",
        "doutorado": "Doutorado"
    };

    var contador = 1;

    $scope.cpfValido = function(cpf) {
        if (cpf.length != 11) return true;
        if (!/^\d+$/.test(cpf)) return false;
        if (cpf == "00000000000" || cpf == "11111111111" || cpf == "22222222222" ||
                cpf == "33333333333" || cpf == "44444444444" || cpf == "55555555555" ||
                cpf == "66666666666" || cpf == "77777777777" || cpf == "88888888888" ||
                cpf == "99999999999") return false;

        var soma;
        var resto;

        // verifica dígito 1
        soma = 0;
        for (i = 1; i <= 9; i++)
            soma = soma + parseInt(cpf.substring(i - 1, i)) * (11 - i);
        resto = (soma * 10) % 11;
        if (resto == 10 || resto == 11) resto = 0;
        if (resto != parseInt(cpf.substring(9, 10))) return false;

        // verifica dígito 2
        soma = 0;
        for (i = 1; i <= 10; i++)
            soma = soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
        resto = (soma * 10) % 11;
        if (resto == 10 || resto == 11) resto = 0;
        if (resto != parseInt(cpf.substring(10, 11))) return false;

        return true;
    }

    $scope.adicionar = function () {

        $scope.formacao.nivel = $scope.formacao.nivel.trim();
        $scope.formacao.curso = $scope.formacao.curso.trim();
        $scope.formacao.instituicao = $scope.formacao.instituicao.trim();
        if (angular.isUndefined($scope.formacao.conclusao)) {
            alert('- O ano de conclusão deve ser um número');
            return;
        }
        $scope.formacao.conclusao = $scope.formacao.conclusao.trim();

        var anoEhNumerico = /^\d+$/.test($scope.formacao.conclusao);
        if (!anoEhNumerico) {
            alert('- O ano de conclusão deve ser um número');
            return;
        }

        if ($scope.formacao.nivel === '' ||
                $scope.formacao.curso === '' ||
                $scope.formacao.instituicao === '' ||
                $scope.formacao.conclusao === '') {
            alert('- Preencha todos os campos da formação');
            return;
        }

        $scope.formacao.id = contador++;
        $scope.formacao.nivel_ = $scope.formacao.nivel;
        $scope.formacao.nivel = dePara[$scope.formacao.nivel];
        $scope.formacoes.push($scope.formacao);
        $scope.formacao = angular.copy(novaFormacao);
    };

    $scope.remover = function (formacao) {
        var indice = $scope.formacoes.indexOf(formacao);
        $scope.formacoes.splice(indice, 1);
    };

    $scope.formacao = angular.copy(novaFormacao);

}]);
