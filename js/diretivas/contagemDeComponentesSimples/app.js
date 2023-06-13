//INCLUDE /js/modulos/servicos
//INCLUDE /js/modulos/contadorDeComponentes

/**
 * Diretiva que apresenta um resumo com o número de componentes inscritas divididas
 * por prioridade e pelas habilitações escolhidas pelo aluno.
 */
var modulo = angular.module('contagemDeComponentesSimples', ['servicos', 'contadorDeComponentes']);

modulo.directive('contagemDeComponentesSimples', ['Aluno', 'Constantes', 'ContadorDeComponentes',
    function (Aluno, Constantes, ContadorDeComponentes) {
        return {
            restrict: 'E',
            replace: 'true',
            templateUrl: 'js/diretivas/contagemDeComponentesSimples/templates/index.html',
            scope: {
                'periodos': '=',
            },
            link: function (scope) {

                var carregouConstantes = false;

                scope.numeroTotalDeOfertas = Aluno.recuperarLimiteDeOfertasQuePodemSerInscritas(function() {
                    scope.numeroTotalDeOfertas = scope.numeroTotalDeOfertas.limite;
                });
                scope.constantes = Constantes.todas(function () {
                    carregouConstantes = true;
                });

                scope.cargaHorariaTotal = function () {
                    if (carregouConstantes) {
                        return ContadorDeComponentes.cargaHoraria(
                            agruparOfertasEmQueOUsuarioEstaInscrito(),
                            [],
                            scope.constantes
                        );
                    }
                };

                function agruparOfertasEmQueOUsuarioEstaInscrito() {
                    return scope.periodos.reduce(function (ofertasInscritas, periodo) {
                        var ofertasSelecionadas = periodo.ofertas.filter(filtrarOfertasSelecionadas);
                        return ofertasInscritas.concat(ofertasSelecionadas);
                    }, []);
                }

                var filtrarOfertasSelecionadas = function (oferta) {
                    return oferta.selecionada;
                };

                scope.excedeuTotal = function() {
                    if (carregouConstantes) {
                        return scope.numeroTotalDeOfertas < scope.cargaHorariaTotal().total;
                    }
                };

            }
        };
    }]);
