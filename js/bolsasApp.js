/**
 * Controla a inscrição dos usuários no curso.
 */

var app = angular.module("bolsasApp", ['ngResource', 'ui.bootstrap', 'servicos']);

app.controller('controlador', ['$scope', '$uibModal', 'Docente', 'Tutor', 'Bolsa',
    function ($scope, $uibModal, Docente, Tutor, Bolsa) {

        var contador = 1;
        // Recupera o próximo ID disponível para as bolsas
        Bolsa.proximoId(function(resposta) {
            contador = resposta[0].max + 1;
        });

        $scope.docentes = Docente.query();
        $scope.tutores = Tutor.query();

        $scope.remover = function(pessoa, bolsa) {
            if (confirm('Tem certeza que deseja remover esta bolsa?')) {
                var indice = pessoa.bolsas.indexOf(bolsa);
                pessoa.bolsas.splice(indice, 1);
            }
        };

        $scope.adicionarNovaBolsa = function (pessoa, titulo) {
//            console.log(pessoa);
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'adicionarBolsa.html',
                controller: 'adicionarBolsaController',
                size: 'lg',
                resolve: {
                    nome: function() {
                        return pessoa.nome + ' ' + pessoa.sobrenome;
                    },
                    titulo: function() {
                        return titulo;
                    }
                }
            });

            modalInstance.result.then(function (resposta) {
                resposta.id = contador++;
                pessoa.bolsas.push(resposta);
                console.log(resposta);
            }, function () {
//            $log.info('Modal dismissed at: ' + new Date());
            });

        }

    }]);

angular.module('bolsasApp').controller('adicionarBolsaController', function ($scope, $uibModalInstance, titulo, nome) {

    $scope.titulo = titulo;
    $scope.nome = nome;
    recuperarData();

    // Quantos dias cada mês tem
    var diasMeses = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

    function recuperarData() {
        var hoje = new Date();
        $scope.dia_pagamento = hoje.getDate();
        $scope.mes_pagamento = hoje.getMonth() + 1;
        $scope.ano_pagamento = hoje.getFullYear();
    }

    $scope.corrigirData = function() {
        if ($scope.ano_pagamento < 1) {
            $scope.ano_pagamento = 1;
        }
        if ($scope.ano_pagamento > 9999) {
            $scope.ano_pagamento = 9999;
        }

        if ($scope.mes_pagamento < 1) {
            $scope.mes_pagamento = 1;
        }
        if ($scope.mes_pagamento > 12) {
            $scope.mes_pagamento = 12;
        }

        if ($scope.dia_pagamento < 1) {
            $scope.dia_pagamento = 1;
        }
        if ($scope.dia_pagamento > diasMeses[$scope.mes_pagamento]) {
            $scope.dia_pagamento = ultimoDiaDoMes();
        }
    }

    function ultimoDiaDoMes() {
        if ($scope.mes_pagamento != 2) {
            return diasMeses[$scope.mes_pagamento];
        }
        return ehAnoBissexto() ? 29 : 28;
    }

    function ehAnoBissexto() {
        if ($scope.ano_pagamento % 400 == 0) {
            return true;
        }
        if ($scope.ano_pagamento % 100 == 0) {
            return false;
        }
        if ($scope.ano_pagamento % 4 == 0) {
            return true;
        }
        return false;
    }

    $scope.ok = function () {
        $uibModalInstance.close({
            'data_pagamento': formatarData(),
            'data_pagamento_ano_na_frente' : formatarData2(),
            'valor': $scope.valor,
            'descricao': $scope.descricao
        });
    };

    function formatarData() {
        var dia = $scope.dia_pagamento;
        var mes = $scope.mes_pagamento;
        var ano = $scope.ano_pagamento;
        return preencherComZeros(10, dia) + '/' + preencherComZeros(10, mes) + '/' + preencherComZeros(1000, ano);
    }

    function formatarData2() {
        var dia = $scope.dia_pagamento;
        var mes = $scope.mes_pagamento;
        var ano = $scope.ano_pagamento;
        return preencherComZeros(1000, ano) + '-' + preencherComZeros(10, mes) + '-' + preencherComZeros(10, dia);
    }

    function preencherComZeros(limiar, numero) {
        var resultado = '';
        var n = numero;
        while (n < limiar) {
            resultado += '0';
            n *= 10;
        }
        return resultado + numero;
    }

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

});