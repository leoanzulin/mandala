/**
 * Controla a inscrição dos usuários em ofertas (simulação).
 */

angular.module('simuladorApp', ['ngResource', 'ui.bootstrap', 'servicos', 'contadorDeComponentes', 'resumoDeComponentes', 'tabelaDeOfertas']);

angular.module('simuladorApp').controller("controlador", ["$scope", "$uibModal", "$log", "Habilitacao", "Oferta", "ContadorDeComponentes",
    function ($scope, $uibModal, $log, Habilitacao, Oferta, ContadorDeComponentes) {

        var habilitacao1 = 0;
        var habilitacao2 = 0;

        $scope.periodos = Oferta.porPeriodo(carregarHabilitacoes);

        function carregarHabilitacoes() {
            $scope.habilitacoes = Habilitacao.get(function () {
                habilitacao1 = $scope.habilitacoes[0].id;
                if ($scope.habilitacoes.length > 1) {
                    habilitacao2 = $scope.habilitacoes[1].id;
                }
            });
        }

        $scope.remover = function (oferta) {
            if (confirm('Tem certeza que deseja remover o componente "' + oferta.componente.nome + '" de sua grade?')) {
                oferta.selecionadaSimulador = false;
            }
        };

        $scope.cargaHorariaTotal = function ()
        {
            return ContadorDeComponentes.cargaHoraria(
                    agruparOfertasEmQueOUsuarioEstaInscrito(),
                    [habilitacao1, habilitacao2]
                    );
        };

        function agruparOfertasEmQueOUsuarioEstaInscrito()
        {
            return $scope.periodos.reduce(function (todasOfertas, periodo) {
                var ofertasSelecionadas = periodo.ofertas.filter(filtrarOfertasSelecionadas);
                return todasOfertas.concat(ofertasSelecionadas);
            }, []);
        }

        var filtrarOfertasSelecionadas = function (oferta) {
            return oferta.selecionadaSimulador;
        }

        // Chama o diálogo de adição de novas ofertas ao período
        $scope.adicionarOfertasAo = function (periodoSelecionado) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'selecionarOfertas.html',
                controller: 'SelecionarOfertasCtrl',
                size: 'lg',
                resolve: {
                    ofertasDisponiveis: function () {
                        return periodoSelecionado.ofertas.filter(function (oferta) {
                            return !oferta.selecionadaSimulador;
                        });
                    },
                    periodoSelecionado: function () {
                        return periodoSelecionado;
                    }
                }
            });

            modalInstance.result.then(function (ofertasSelecionadas) {
                for (var i in ofertasSelecionadas) {
                    ofertasSelecionadas[i].selecionadaParaAdicionar = false;
                    ofertasSelecionadas[i].selecionadaSimulador = true;
                }
//            console.log('Componentes selecionadas = ' + idsDasComponentesSelecionadas);
            }, function () {
//                $log.info('Modal dismissed at: ' + new Date());
            });
        };

    }]);

angular.module('simuladorApp').controller('SelecionarOfertasCtrl', function ($scope, $uibModalInstance, ofertasDisponiveis, periodoSelecionado) {
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
