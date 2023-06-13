angular.module('resumoDeComponentes', ['servicos']);

angular.module('resumoDeComponentes').directive('resumoDeComponentes', ['Constantes', function (Constantes) {
    return {
        restrict: 'E',
        replace: 'true',
        templateUrl: 'js/templates/resumoDeComponentes.html',
        link: function (scope) {
            scope.constantes = Constantes.todas();
        }
    };
}]);
