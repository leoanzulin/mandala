/**
 * Módulo que realiza a contagem de ofertas de acordo com habilitações.
 */

angular.module('contadorDeComponentes', ['servicos']);

angular.module('contadorDeComponentes').service('ContadorDeComponentes', ['Constantes', function (Constantes) {

        var constantesCarregadas = false;
        var constantes = Constantes.todas(function () {
            constantesCarregadas = true
        });

        /**
         * @param array ofertas Array com um ou mais objetos de ofertas em que um aluno está inscrito
         * @param array habilitacoes Array com um ou mais IDs das habilitações que o aluno escolheu
         * @returns object Objeto contendo a carga horária total e dividida por habilitações
         */
        this.cargaHoraria = function (ofertas, habilitacoes) {

            // Enquanto os dados das constantes não estiverem carregados, não atualiza esta função
            if (!constantesCarregadas)
                return;

            var componentes = recuperarComponentesDasOfertas(ofertas);
            componentes = removerComponentesDuplicadas(componentes);
            adicionaComponenteDoTcc(componentes);

            var cargaHoraria = inicializarContagem(habilitacoes);
            calcularCargaHorariaPorHabilitacoes(cargaHoraria, componentes, habilitacoes);
            ajustarSobrasDasComponentesOptativas(cargaHoraria, habilitacoes);

            cargaHoraria.total = componentes.length;
            ajustarSobrasDosTotaisDasHabilitacoes(cargaHoraria, habilitacoes);

            return cargaHoraria;
        };

        function recuperarComponentesDasOfertas(ofertas) {
            return ofertas.map(function (oferta) {
                return oferta.componente;
            });
        }

        function removerComponentesDuplicadas(componentes) {
            var componentesInscritos = [];
            var idsDeComponentesInscritos = {};

            for (var i in componentes) {
                var idDaComponente = componentes[i].id;
                if (idDaComponente in idsDeComponentesInscritos) {
                    continue;
                }

                idsDeComponentesInscritos[idDaComponente] = true;
                componentesInscritos.push(componentes[i]);
            }

            return componentesInscritos;
        }

        function adicionaComponenteDoTcc(componentes) {
            componentes.push({
                'prioridades': {0: -1,
                    1: constantes.PRIORIDADE_NECESSARIA,
                    2: constantes.PRIORIDADE_NECESSARIA,
                    3: constantes.PRIORIDADE_NECESSARIA,
                    4: constantes.PRIORIDADE_NECESSARIA,
                    5: constantes.PRIORIDADE_NECESSARIA},
            });
        }

        function inicializarContagem(habilitacoes) {
            var cargaHoraria = [];
            for (var i = 0; i < habilitacoes.length; ++i) {
                // Carga horária das componentes (N)ecessárias, (O)pcionais e (L)ivres
                cargaHoraria.push([0, 0, 0]);
            }
            return cargaHoraria;
        }

        function calcularCargaHorariaPorHabilitacoes(cargaHoraria, componentes, habilitacoes) {
            for (var i = 0; i < componentes.length; ++i) {
                for (var j = 0; j < habilitacoes.length; ++j) {
                    var prioridadeParaHabilitacao = componentes[i].prioridades[habilitacoes[j]];
                    cargaHoraria[j][prioridadeParaHabilitacao]++;
                }
            }
        }

        /**
         * O que "sobra" a mais das componentes optativas vai para as
         * componentes livres.
         */
        function ajustarSobrasDasComponentesOptativas(cargaHoraria, habilitacoes) {
            for (var i = 0; i < habilitacoes.length; ++i) {
                var sobraDeDisciplinasOptativasHabilitacao = Math.max(
                        cargaHoraria[i][constantes.PRIORIDADE_OPTATIVA] - constantes.NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS,
                        0
                        );
                cargaHoraria[i][constantes.PRIORIDADE_OPTATIVA] -= sobraDeDisciplinasOptativasHabilitacao;
                cargaHoraria[i][constantes.PRIORIDADE_LIVRE] += sobraDeDisciplinasOptativasHabilitacao;
            }
        }

        function ajustarSobrasDosTotaisDasHabilitacoes(cargaHoraria, habilitacoes) {
            var numeroDeHabilitacoes = 0;
            for (var i in habilitacoes) {
                if (habilitacoes[i] != 0) {
                    numeroDeHabilitacoes++;
                }
            }

            if (numeroDeHabilitacoes == 1) {
                cargaHoraria.totalHabilitacoes = [0, cargaHoraria.total, 0, 0];
            }
            else if (numeroDeHabilitacoes == 2) {
                cargaHoraria.totalHabilitacoes = [
                    0,
                    Math.min(cargaHoraria.total, constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[1]),
                    Math.max(cargaHoraria.total - constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[1], 0),
                    0
                ];
            }
            else if (numeroDeHabilitacoes == 3) {
                cargaHoraria.totalHabilitacoes = [
                    0,
                    Math.min(cargaHoraria.total, constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[1]),
                    Math.max(cargaHoraria.total - constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[1], 0),
                    Math.max(cargaHoraria.total - (constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[1] + constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[2]), 0)
                ];
            }
//            cargaHoraria.totalHabilitacoes = [
//                0,
//                Math.min(cargaHoraria.total, constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[1]),
//                Math.max(cargaHoraria.total - constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[1], 0),
//                Math.max(cargaHoraria.total - (constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[1] + constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[2]), 0),
//            ];
        }

    }]);
