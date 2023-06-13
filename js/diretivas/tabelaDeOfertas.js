var modulo = angular.module('tabelaDeOfertas', []);

// https://docs.angularjs.org/guide/directive
// https://gist.github.com/CMCDragonkai/6282750

modulo.directive('tabelaDeOfertas', function () {
    return {
        restrict: 'E',
        replace: 'true',
        templateUrl: 'js/templates/tabelaDeOfertas.html',
        link: function (scope, element, attributes) {
            scope.modo = attributes.modo;

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
        }
    };
});
