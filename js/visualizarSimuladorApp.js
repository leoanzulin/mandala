/**
 * Controla a inscrição dos usuários em ofertas (simulação).
 */
angular.module('visualizarSimuladorApp', ['ngResource', 'servicos', 'contadorDeComponentes', 'resumoDeComponentes', 'tabelaDeOfertas']);

angular.module('visualizarSimuladorApp').controller("controlador", ["$scope", "$log", "Habilitacao", "Oferta", "ContadorDeComponentes",
    function ($scope, $log, Habilitacao, Oferta, ContadorDeComponentes) {

        var habilitacao1 = 0;
        var habilitacao2 = 0;

        $scope.periodos = Oferta.porPeriodo({
            inscricaoId: recuperarIdDaUrl()
        }, carregarHabilitacoes);

        function recuperarIdDaUrl() {
            var url = window.location.href;
            var resultados = /.*&id=(\d+)$/.exec(url);
            return resultados[1];
        }

        function carregarHabilitacoes() {
            $scope.habilitacoes = Habilitacao.daInscricao({
                id: recuperarIdDaUrl()
            }, function () {
                habilitacao1 = $scope.habilitacoes[0].id;
                if ($scope.habilitacoes.length > 1) {
                    habilitacao2 = $scope.habilitacoes[1].id;
                }
            });
        }

        $scope.cargaHorariaTotal = function () {
            return ContadorDeComponentes.cargaHoraria(
                    agruparOfertasEmQueOUsuarioEstaInscrito(),
                    [habilitacao1, habilitacao2]
                    )
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

    }]);
