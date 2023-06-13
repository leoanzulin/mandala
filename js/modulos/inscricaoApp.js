//INCLUDE /js/bower_components/angular/angular.min
//INCLUDE /js/modulos/servicos
//INCLUDE /js/modulos/validadorDeCpf
//INCLUDE /js/inscricoes
//INCLUDE /js/mascara_celular

/**
 * Controla a inscrição dos usuários no curso.
 */
var app = angular.module("inscricaoApp", ['servicos', 'validadorDeCpf']);

app.controller('controlador', ['$scope', 'Formacao', 'Habilitacao', 'validadorDeCpf',
    function ($scope, Formacao, Habilitacao, validadorDeCpf) {

        const idInscricao = recuperarId(window.location.href) || -1;

        function recuperarId(url) {
            const resultado = /id=(\d+)$/.exec(url);
            return resultado ? resultado[1] : null;
        }

        $scope.cpf = '';
        $scope.formacoes = [];
        $scope.habilitacoes = Habilitacao.query(recuperarHabilitacoesDaInscricao);
        $scope.cpfValido = validadorDeCpf.validar;
        $scope.tipoDeCurso = 2;
        $scope.dePara = {
            "graduacao": "Graduação",
            "especializacao": "Especialização",
            "mestrado": "Mestrado",
            "doutorado": "Doutorado"
        };
        let quantidadeDeHabilitacoesSelecionadas = 0;

        function recuperarHabilitacoesDaInscricao() {
            if (idInscricao == -1) return;

            const habilitacoesSelecionadas = Habilitacao.daInscricao({'id': idInscricao}, function() {
                const letrasSelecionadas = habilitacoesSelecionadas.map(function(h) { return h.letra; });

                letrasSelecionadas.forEach(function(letra, i) {
                    $scope.habilitacoes.forEach(function(habilitacao) {
                        if (habilitacao.letra == letra) {
                            habilitacao.ordem = i + 1;
                            habilitacao.selecionada = true;
                        }
                    });
                });

                quantidadeDeHabilitacoesSelecionadas = letrasSelecionadas.length;
            });
        }

        const novaFormacao = {
            "nivel": "",
            "curso": "",
            "instituicao": "",
            "conclusao": ""
        };

        if (sessionStorage && idInscricao == -1) {
            $scope.cpf = sessionStorage.getItem('cpf') || '';
            $scope.formacoes = JSON.parse(sessionStorage.getItem('formacoes')) || [];
        }

        // Se estiver na tela de editar inscrições, reucpera as formações que
        // foram informadas
        if (idInscricao != -1) {
            $scope.formacoes = Formacao.get({'id': idInscricao});
        }

        $scope.adicionar = function () {
            $scope.formacao.curso = $scope.formacao.curso.trim();
            $scope.formacao.instituicao = $scope.formacao.instituicao.trim();
            if (angular.isUndefined($scope.formacao.conclusao)) {
                alert('- O ano de conclusão deve ser um número');
                return;
            }
            $scope.formacao.conclusao = $scope.formacao.conclusao.trim();

            var anoEhNumerico = /^\d+$/.test($scope.formacao.conclusao);
            if (!anoEhNumerico) {
                alert('- O ano de conclusão deve ser um número');
                return;
            }

            if ($scope.formacao.nivel === '' ||
                    $scope.formacao.curso === '' ||
                    $scope.formacao.instituicao === '' ||
                    $scope.formacao.conclusao === '') {
                alert('- Preencha todos os campos da formação');
                return;
            }

            $scope.formacoes.push($scope.formacao);
            $scope.formacao = angular.copy(novaFormacao);
            atualizarStorage();
        };

        function atualizarStorage() {
            if (idInscricao == -1 && sessionStorage) {
                sessionStorage.setItem('formacoes', angular.toJson($scope.formacoes));
            }
        }

        $scope.remover = function(formacao) {
            var indice = $scope.formacoes.indexOf(formacao);
            $scope.formacoes.splice(indice, 1);
            atualizarStorage();
        };

        $scope.formacao = angular.copy(novaFormacao);

        $scope.selecionar = function(habilitacao) {
            if ($scope.tipoDeCurso != 2) return;

            habilitacao.selecionada = !habilitacao.selecionada;
            if (habilitacao.selecionada) quantidadeDeHabilitacoesSelecionadas++
            else quantidadeDeHabilitacoesSelecionadas--;
            atualizarOrdens(habilitacao);
        };

        function atualizarOrdens(habilitacao) {
            if (habilitacao.selecionada) {
                habilitacao.ordem = quantidadeDeHabilitacoesSelecionadas;
            } else {
                const ordemAtual = habilitacao.ordem;
                habilitacao.ordem = null;
                $scope.habilitacoes.forEach(function(h) {
                    if (h.ordem > ordemAtual) h.ordem--;
                });
            }
        }

        $scope.deselecionarHabilitacoes = function() {
            $scope.habilitacoes.forEach(function(habilitacao) {
                habilitacao.selecionada = false;
                habilitacao.ordem = null;
            });
            quantidadeDeHabilitacoesSelecionadas = 0;
        };

    }]);
