/**
 * Visualiza ofertas de componentes curriculares por período.
 * 
 * Usado na view oferta/visualizar.
 */
angular.module('visualizarOfertasApp', ['ngResource', 'servicos']);

angular.module('visualizarOfertasApp').controller("controlador", ["$scope", "Oferta",
        function ($scope, Oferta) {

    $scope.turmasDisponiveis = [1, 2];
    $scope.turmaAtual = 1;
    $scope.turmas = Oferta.todasPorTurmaEPeriodoComInscricoes();

}]);
