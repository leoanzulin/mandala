/**
 * Controla a tela de saldo financeiro do curso.
 */

var app = angular.module("saldoBolsasApp", ['ngResource', 'servicos']);

app.controller('controlador', ['$scope', 'Bolsa',
    function ($scope, Bolsa) {

        $scope.meses = [{'id': 1}, {'id': 2}, {'id': 3}, {'id': 4},
            {'id': 5}, {'id': 6}, {'id': 7}, {'id': 8},
            {'id': 9}, {'id': 10}, {'id': 11}, {'id': 12}];
        $scope.anos = [{'id': 2016}, {'id': 2017}];
        $scope.ordenacoes = [
            {'id': 'data_pagamento_ano_na_frente', 'name': 'Data'},
            {'id': 'docente', 'name': 'Docente'},
            {'id': 'tutor', 'name': 'Tutor'},
            {'id': 'valor', 'name': 'Valor'}];
        $scope.bolsasPagasPara = [
            {'id': 'ambos', 'name': 'Docentes e tutores'},
            {'id': 'docentes', 'name': 'Docentes'},
            {'id': 'tutores', 'name': 'Tutores'},
        ];
        $scope.filtro = {'id': 'ambos', 'name': 'Docentes e tutores'};
        $scope.ordem = {'id' : 'data_pagamento_ano_na_frente', 'name': 'Data'};

        $scope.periodo_inicio_mes = {'id': 8};
        $scope.periodo_inicio_ano = {'id': 2016};
        $scope.periodo_fim_mes = {'id': 12};
        $scope.periodo_fim_ano = {'id': 2016};

        $scope.bolsas = Bolsa.query();

        $scope.filtrarQuem = function(bolsa) {
            if ($scope.filtro.id == 'docentes' && !bolsa.docente_cpf) {
                return false;
            }
            if ($scope.filtro.id == 'tutores' && !bolsa.tutor_cpf) {
                return false;
            }
            return true;
        }

        $scope.filtrarPeriodo = function (bolsa) {
            var data_inicio = $scope.periodo_inicio_ano.id + '-' + preencherComZeros(10, $scope.periodo_inicio_mes.id) + '-00';
            var data_fim = $scope.periodo_fim_ano.id + '-' + preencherComZeros(10, $scope.periodo_fim_mes.id) + '-32';
//            console.log(data_inicio + ' ' + bolsa.data_pagamento_ano_na_frente + ' ' + data_fim);
            return data_inicio < bolsa.data_pagamento_ano_na_frente &&
                    bolsa.data_pagamento_ano_na_frente < data_fim;
        };

        function preencherComZeros(limiar, numero) {
            var resultado = '';
            var n = numero;
            while (n < limiar) {
                resultado += '0';
                n *= 10;
            }
            return resultado + numero;
        }

    }]);

app.filter('filtroSoma', [function () {
    return function (bolsas) {
        var total = 0;
        for (var i in bolsas) {
            if (typeof bolsas[i].valor == 'undefined') {
                continue;
            }
            total += parseFloat(bolsas[i].valor);
        }
        return total;
    }
}]);

app.filter('filtroTirarSaldoFai', [function () {
    return function (valor, $scope) {
        var saldoFai = $scope.saldoFai || 0;
        return saldoFai - valor;
    }
}]);
