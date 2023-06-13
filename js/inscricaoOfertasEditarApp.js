/**
 * Controla a inscrição dos alunos nas ofertas do período aberto atualmente.
 */
angular.module('inscricaoOfertasApp', ['ngResource', 'servicos', 'contadorDeComponentes', 'resumoDeComponentes']);

angular.module('inscricaoOfertasApp').controller("controlador", ["$scope", "$log", "Oferta", "Habilitacao", "ContadorDeComponentes",
    function ($scope, $log, Oferta, Habilitacao, ContadorDeComponentes) {

        $scope.habilitacao1 = 0;
        $scope.habilitacao2 = 0;

        // O método OfertaController->actionPorPeriodo traz todas as ofertas
        // da turma do aluno atualmente logado, separadas por períodos
        $scope.periodos = Oferta.porPeriodoDoUsuario({
            'inscricaoId': recuperarIdDaUrl()
        }, carregarHabilitacoes);

        function recuperarIdDaUrl() {
            var url = window.location.href;
            var resultados = /.*&id=(\d+)$/.exec(url);
            return resultados[1];
        }

        function carregarHabilitacoes() {
            $scope.habilitacoes = Habilitacao.daInscricao({
                'id': recuperarIdDaUrl()
            }, function () {
                $scope.habilitacao1 = $scope.habilitacoes[0].id;
                if ($scope.habilitacoes.length > 1) {
                    $scope.habilitacao2 = $scope.habilitacoes[1].id;
                }
            });
        }

        $scope.selecionar = function (oferta) {
            oferta.selecionada = !oferta.selecionada;
        };

        $scope.cargaHorariaTotal = function ()
        {
            return ContadorDeComponentes.cargaHoraria(
                    agruparOfertasEmQueOUsuarioEstaInscrito(),
                    [$scope.habilitacao1, $scope.habilitacao2]
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
            return oferta.selecionada;
        }

    }]);
