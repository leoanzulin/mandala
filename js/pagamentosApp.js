/**
 * Controla a tela dos pagamentos dos alunos do curso.
 */

var app = angular.module("pagamentosApp", ['ngResource', 'servicos']);

app.controller('controlador', ['$scope', 'Aluno',
    function ($scope, Aluno) {

        $scope.paginacao = {
            selecaoDePagina: 1,
            paginaAtual: 0,
            paginaMaxima: 0,
            tamanhoDaPagina: 25,
            mostrandoInicio: 0,
            mostrandoFim: 0,
            numeroTotal: 0
        };
        $scope.filtrosStatus = ['Todos', 'Ativo', 'Cancelado', 'Desistente', 'Trancado'];
        $scope.filtroSelecionado = 'Todos';

        $scope.atualizarPaginacao = function () {
            $scope.paginacao.mostrandoInicio = $scope.paginacao.paginaAtual * $scope.paginacao.tamanhoDaPagina + 1;
            $scope.paginacao.mostrandoFim = Math.min($scope.paginacao.mostrandoInicio + $scope.paginacao.tamanhoDaPagina - 1, $scope.paginacao.numeroTotal);
            $scope.paginacao.paginaMaxima = Math.ceil($scope.paginacao.numeroTotal / $scope.paginacao.tamanhoDaPagina);
        }

        $scope.alunos = Aluno.recuperarAlunosPagamento(function () {
            $scope.paginacao.numeroTotal = $scope.alunos.length;
            $scope.atualizarPaginacao();
        });

        $scope.totalPago = function (aluno) {
            var total = Number(transformarVirgulaParaPonto(aluno.pagamento.inscricao.valor)) +
                    Number(transformarVirgulaParaPonto(aluno.pagamento.matricula.valor)) +
                    Number(transformarVirgulaParaPonto(aluno.pagamento.pagouAVista.valor));
            for (var j = 1; j <= 27; ++j) {
                total += Number(transformarVirgulaParaPonto(aluno.pagamento.parcelas[j].valor));
            }
            return total.toFixed(2);
        };

        function transformarVirgulaParaPonto(string) {
            return string.replace(/,/g, '.');
        }

        $scope.totalFaltante = function (aluno) {
            var totalFaltante = $scope.totalPago(aluno) - Number(transformarVirgulaParaPonto(aluno.pagamento.totalPrevisto.valor));
            return totalFaltante.toFixed(2);
        }

        $scope.voltarParaPrimeiraPagina = function () {
            $scope.paginacao.paginaAtual = 0;
        }

        $scope.atualizarPagina = function () {
            if ($scope.paginacao.selecaoDePagina < 1) {
                $scope.paginacao.selecaoDePagina = 1;
            }
            if ($scope.paginacao.selecaoDePagina > $scope.paginacao.paginaMaxima) {
                $scope.paginacao.selecaoDePagina = $scope.paginacao.paginaMaxima;
            }
            $scope.paginacao.paginaAtual = $scope.paginacao.selecaoDePagina - 1;
            $scope.atualizarPaginacao();
        }

    }]);

// http://stackoverflow.com/questions/11873570/angularjs-for-loop-with-numbers-ranges
app.filter('range', function () {
    return function (input, total) {
        total = parseInt(total);

        for (var i = 0; i < total; i++) {
            input.push(i);
        }

        return input;
    };
});

// http://www.angulartutorial.net/2014/03/client-side-pagination-using-angular-js.html
app.filter('pagination', function () {
    return function (input, start) {
        start = +start;
        return input.slice(start);
    };
});

app.filter('filtrarStatusAluno', function () {
    return function (input, status, scope) {
        var alunosFiltrados = input.filter(function (aluno) {
            if (status == 'Todos') {
                return true;
            }
            return aluno.status_aluno == status;
        })
        scope.paginacao.numeroTotal = alunosFiltrados.length;
        scope.atualizarPaginacao();
        return alunosFiltrados;
    };
});

app.directive('itemDePagamento', function () {
    return {
        restrict: 'E',
        replace: 'true',
        scope: {
            'pagamento': '=',
        },
        link: function (scope) {

            var TECLA_BACKSPACE = 8;
            var TECLA_DELETE = 46;
            var TECLA_TAB = 9;

            scope.campoValor = function (event, campo) {
                scope.pagamento.valor = formatarValor(event, campo);
                if (!ehTeclaCtrlV(event) && !(event.keyCode == TECLA_TAB)) {
                    event.preventDefault();
                }
            }

            function formatarValor(event, campo) {
                if (campo == null) {
                    campo = '0,00';
                }

                var campoApenasDigitos = removerCaracteresQueNaoSaoNumeros('' + campo);
                if (ehTeclaNumerica(event.key)) {
                    campoApenasDigitos += event.key;
                }
                else if (event.keyCode == TECLA_BACKSPACE || event.keyCode == TECLA_DELETE) {
                    campoApenasDigitos = campoApenasDigitos.substring(0, campoApenasDigitos.length - 1);
                }

                campoApenasDigitos = completarComZerosSeTamanhoMenorQue3(campoApenasDigitos);
                campoApenasDigitos = removerZerosAEsquerdaSeTamanhoForMaiorQue3(campoApenasDigitos);

                return campoApenasDigitos.substring(0, campoApenasDigitos.length - 2) +
                        ',' + campoApenasDigitos.slice(-2);
            }

            function completarComZerosSeTamanhoMenorQue3(string) {
                while (string.length < 3) {
                    string = '0' + string;
                }
                return string;
            }

            function removerZerosAEsquerdaSeTamanhoForMaiorQue3(string) {
                while (string.length > 3 && comecaComZero(string)) {
                    string = string.substr(1);
                }
                return string;
            }

            function comecaComZero(string) {
                if (string.charAt(0) == '0') {
                    return true;
                }
                return false;
            }

            scope.campoData = function (event, campo) {
                scope.pagamento.data_pagamento = formatarData(event, campo);
                if (!ehTeclaCtrlV(event) && !(event.keyCode == TECLA_TAB)) {
                    event.preventDefault();
                }
            };

            function formatarData(event, campo) {
                if (campo == null) {
                    campo = '__/__/____';
                }

                var campoApenasDigitos = removerCaracteresQueNaoSaoNumeros('' + campo);

                if (ehTeclaNumerica(event.key)) {
                    campoApenasDigitos += event.key;
                }
                else if (event.keyCode == TECLA_BACKSPACE || event.keyCode == TECLA_DELETE) {
                    campoApenasDigitos = campoApenasDigitos.substring(0, campoApenasDigitos.length - 1);
                }

                var dia = campoApenasDigitos.substring(0, 2);
                var mes = campoApenasDigitos.substring(2, 4);
                var ano = campoApenasDigitos.substring(4, 8);
                dia = completarComUnderline(dia, 2);
                mes = completarComUnderline(mes, 2);
                ano = completarComUnderline(ano, 4);

                return dia + '/' + mes + '/' + ano;
            }

            function removerCaracteresQueNaoSaoNumeros(string) {
                return string.replace(/\D/g, '');
            }

            function ehTeclaNumerica(tecla) {
                return tecla >= '0' && tecla <= '9';
            }

            function ehTeclaCtrlV(event) {
                return event.key == 'v' && event.ctrlKey;
            }

            function completarComUnderline(string, comprimento) {
                while (string.length < comprimento) {
                    string += '_';
                }
                return string;
            }

        },
        templateUrl: 'js/templates/itemDePagamento.html',
    };
});
