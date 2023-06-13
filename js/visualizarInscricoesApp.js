/**
 * Controla a inscrição dos alunos nas ofertas do período aberto atualmente.
 */
angular.module('visualizarInscricoesApp', ['ngResource', 'servicos', 'contadorDeComponentes', 'resumoDeComponentes']);

angular.module('visualizarInscricoesApp').controller("controlador", ["$scope", "$log", "Oferta", "Habilitacao", "ContadorDeComponentes",
    function ($scope, $log, Oferta, Habilitacao, ContadorDeComponentes) {

        var habilitacao1 = 0;
        var habilitacao2 = 0;

        $scope.periodos = Oferta.porPeriodoDoUsuario({
            inscricaoId: recuperarIdDaUrl()
        }, carregarHabilitacoes);

        function recuperarIdDaUrl() {
            var url = window.location.href;
            var resultados = /.*&id=(\d+)$/.exec(url);
            return resultados[1];
        }

        function carregarHabilitacoes() {
            $scope.habilitacoes = Habilitacao.daInscricao({
                'id': recuperarIdDaUrl()
            },
            function () {
                habilitacao1 = $scope.habilitacoes[0].id;
                $scope.habilitacao1 = $scope.habilitacoes[0].id;
                if ($scope.habilitacoes.length > 1) {
                    habilitacao2 = $scope.habilitacoes[1].id;
                    $scope.habilitacao2 = $scope.habilitacoes[1].id;
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
            return oferta.selecionada;
        }

    }]);
