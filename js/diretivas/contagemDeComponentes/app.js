//INCLUDE /js/modulos/servicos
//INCLUDE /js/modulos/contadorDeComponentes

/**
 * Diretiva que apresenta um resumo com o número de componentes inscritas divididas
 * por prioridade e pelas habilitações escolhidas pelo aluno.
 */
var modulo = angular.module('contagemDeComponentes', ['servicos', 'contadorDeComponentes']);

modulo.directive('contagemDeComponentes', ['Constantes', 'ContadorDeComponentes',
    function (Constantes, ContadorDeComponentes) {
        return {
            restrict: 'E',
            replace: 'true',
            templateUrl: 'js/diretivas/contagemDeComponentes/templates/index.html',
            scope: {
                'habilitacoes': '=',
                'periodos': '=',
            },
            link: function (scope) {

                var carregouConstantes = false;

                scope.constantes = Constantes.todas(function () {
                    carregouConstantes = true;
                });

                scope.cargaHorariaTotal = function () {
                    if (carregouConstantes) {
                        return ContadorDeComponentes.cargaHoraria(
                            agruparOfertasEmQueOUsuarioEstaInscrito(),
                            recuperarIdsDasHabiltiacoes(),
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

                function recuperarIdsDasHabiltiacoes() {
                    return scope.habilitacoes.map(function (habilitacao) {
                        return habilitacao.id;
                    });
                }

                scope.algumaHabilitacaoExcedeuTotal = function() {
                    if (carregouConstantes) {
                        return scope.habilitacoes.some(function(habilitacao, i) {
                            return scope.constantes.NUMERO_MAXIMO_DE_COMPONENTES_POR_HABILITACAO[i + 1]
                                < scope.cargaHorariaTotal().totalHabilitacoes[i + 1];
                        })
                    }
                };

                scope.numeroDeComponentesObrigatorios = function(indiceHabilitacao) {
                    if (carregouConstantes) {
                        return scope.cargaHorariaTotal()[indiceHabilitacao].componentesNovos[0]
                            + scope.cargaHorariaTotal()[indiceHabilitacao].componentesRepetidos[0];
                    }
                    return 0;
                };

                scope.numeroDeComponentesOptativos = function(indiceHabilitacao) {
                    if (carregouConstantes) {
                        return scope.cargaHorariaTotal()[indiceHabilitacao].componentesNovos[1]
                            + scope.cargaHorariaTotal()[indiceHabilitacao].componentesRepetidos[1];
                    }
                    return 0;
                };

                scope.numeroDeComponentesLivres = function(indiceHabilitacao) {
                    if (carregouConstantes) {
                        return scope.cargaHorariaTotal()[indiceHabilitacao].componentesNovos[2]
                            + scope.cargaHorariaTotal()[indiceHabilitacao].componentesRepetidos[2];
                    }
                    return 0;
                };

                scope.numeroDeComponentesDistintos = function(indiceHabilitacao) {
                    if (carregouConstantes) {
                        return scope.cargaHorariaTotal()[indiceHabilitacao].componentesNovos[0]
                            + scope.cargaHorariaTotal()[indiceHabilitacao].componentesNovos[1]
                            + scope.cargaHorariaTotal()[indiceHabilitacao].componentesNovos[2];
                    }
                    return 0;
                };

                scope.cumpriuNumeroMinimoDeComponentesObrigatorios = function() {
                    if (carregouConstantes) {
                        return scope.cargaHorariaTotal()[0].componentesNovos[0]
                            + scope.cargaHorariaTotal()[0].componentesRepetidos[0]
                            == scope.constantes.NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS;
                    }
                    return false;
                };

                scope.cumpriuNumeroMinimoDeComponentesOptativosParaHabilitacao = function(indiceHabilitacao) {
                    if (carregouConstantes) {
                        return scope.cargaHorariaTotal()[indiceHabilitacao].componentesNovos[1]
                            + scope.cargaHorariaTotal()[indiceHabilitacao].componentesRepetidos[1]
                            >= scope.constantes.NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS;
                    }
                    return false;
                };

                scope.cumpriuNumeroIdealDeComponentesParaHabilitacao = function(indiceHabilitacao) {
                    if (carregouConstantes) {
                        const numeroTotalDeComponentesParaHabilitacao = scope.cargaHorariaTotal().totalHabilitacoes[indiceHabilitacao + 1];
                        return scope.constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[indiceHabilitacao + 1] <= numeroTotalDeComponentesParaHabilitacao
                            && numeroTotalDeComponentesParaHabilitacao <= scope.constantes.NUMERO_MAXIMO_DE_COMPONENTES_POR_HABILITACAO[indiceHabilitacao + 1];
                    }
                    return false;
                };

                scope.excedeuNumeroIdealDeComponentesParaHabilitacao = function(indiceHabilitacao) {
                    if (carregouConstantes) {
                        return scope.constantes.NUMERO_MAXIMO_DE_COMPONENTES_POR_HABILITACAO[indiceHabilitacao + 1]
                            < scope.cargaHorariaTotal().totalHabilitacoes[indiceHabilitacao + 1];
                    }
                    return false;
                };

                scope.cumpriuNumeroIdealDeComponentesDistintos = function() {
                    if (carregouConstantes) {
                        const total = scope.cargaHorariaTotal().total;
                        return scope.constantes.NUMERO_MINIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES[scope.habilitacoes.length] <= total
                            && total <= scope.constantes.NUMERO_MAXIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES[scope.habilitacoes.length];
                    }
                    return false;
                }

            }
        };
    }]);
