/**
 * Controla a edição do perfil de um usuário.
 */

var app = angular.module("editarPerfilApp", []);

app.controller('controlador', function($scope) {

    $scope.formacoes = [];

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

});
