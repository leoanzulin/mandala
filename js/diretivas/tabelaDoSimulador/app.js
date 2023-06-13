//INCLUDE /js/ui-bootstrap-tpls-1.3.3.min

/**
 * Diretiva responsável por apresentar as tabelas de inscrições em ofertas
 * nas views de simulador.
 * 
 * Seus atributos são: "modo", que pode ser ausente ou ter o valor "visualizar".
 * Quando esse valor é atribuído, o botão de adicionar novas ofertas à um
 * determinado período não é mostrado.
 * O outro atributo é "periodos", que deve conter o objeto que contém todas as
 * ofertas do sistema organizadas por períodos.
 */
var modulo = angular.module('tabelaDoSimulador', ['ui.bootstrap']);

// https://docs.angularjs.org/guide/directive
// https://gist.github.com/CMCDragonkai/6282750

modulo.directive('tabelaDoSimulador', ["$uibModal", function ($uibModal) {
        return {
            restrict: 'E',
            replace: 'true',
            templateUrl: 'js/diretivas/tabelaDoSimulador/templates/index.html',
            scope: {
                'periodos': '=',
                'modo': '@'
            },
            link: function (scope) {

                verificarSeModoEhValido();

                function verificarSeModoEhValido() {
                    if (scope.modo != null && scope.modo != 'visualizar') {
                        var mensagemDeErro = 'ERRO: Modo atribuído à diretiva tabela-do-simulador é inválido: ' + scope.modo;
                        alert(mensagemDeErro);
                        throw new Error(mensagemDeErro);
                    }
                }

                scope.numeroDeComponentesDoPeriodo = function (periodo) {
                    return periodo.ofertas.filter(filtrarOfertasSelecionadas).length;
                }

                var filtrarOfertasSelecionadas = function (oferta) {
                    return oferta.selecionadaSimulador;
                }

                scope.cargaHorariaDoPeriodo = function (periodo) {
                    return periodo.ofertas
                            .filter(filtrarOfertasSelecionadas)
                            .reduce(somarHoras, 0);
                };

                var somarHoras = function (somaDeHoras, oferta) {
                    return somaDeHoras + oferta.componente.cargaHoraria;
                }

                scope.naoHaOfertasNeste = function (periodo) {
                    return periodo.ofertas.filter(filtrarOfertasSelecionadas).length == 0;
                };

                scope.remover = function (oferta) {
                    if (confirm('Tem certeza que deseja remover o componente "' + oferta.componente.nome + '" de sua grade?')) {
                        oferta.selecionadaSimulador = false;
                    }
                }

                scope.adicionarOfertasAo = function (periodo) {
                    var modalInstance = $uibModal.open({
                        animation: true,
                        templateUrl: 'js/diretivas/tabelaDoSimulador/templates/selecionarOfertas.html',
                        controller: 'SelecionarOfertasCtrl',
                        size: 'lg',
                        resolve: {
                            ofertasDisponiveis: function () {
                                return periodo.ofertas.filter(function (oferta) {
                                    return !oferta.selecionadaSimulador;
                                });
                            },
                            periodoSelecionado: function () {
                                return periodo;
                            }
                        }
                    });

                    modalInstance.result.then(function (ofertasSelecionadas) {
                        for (var i in ofertasSelecionadas) {
                            ofertasSelecionadas[i].selecionadaParaAdicionar = false;
                            ofertasSelecionadas[i].selecionadaSimulador = true;
                        }
                    });
                }
            }
        };
    }]);

modulo.controller('SelecionarOfertasCtrl', function ($scope, $uibModalInstance, ofertasDisponiveis, periodoSelecionado) {
    $scope.ofertasDisponiveis = ofertasDisponiveis;
    $scope.periodoSelecionado = periodoSelecionado;

    $scope.ok = function () {
        var ofertasSelecionadas = $scope.ofertasDisponiveis.filter(function (oferta) {
            return oferta.selecionadaParaAdicionar == true;
        });
        $uibModalInstance.close(ofertasSelecionadas);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

    $scope.selecionar = function (oferta) {
        oferta.selecionadaParaAdicionar = !oferta.selecionadaParaAdicionar;
    };
});
