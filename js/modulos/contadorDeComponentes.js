/**
 * Módulo que realiza a contagem de ofertas de acordo com habilitações de uma
 * inscrição.
 */
angular.module('contadorDeComponentes', []);

angular.module('contadorDeComponentes').service('ContadorDeComponentes', function () {

    // As constantes são injetadas neste método pois a diretiva contagemDeComponentes
    // já faz a requisição delas. Colocar a dependência de Constantes neste script e
    // fazer outra chamada para a requisição das constantes é redundante.

    /**
     * @param array ofertasInscritas Array com um ou mais objetos de ofertas em que um aluno está inscrito
     * @param array habilitacoes Array com um ou mais IDs das habilitações que o aluno escolheu
     * @param array constantes Contém as constantes do sistema
     * @returns object Objeto contendo a carga horária total e dividida por habilitações
     */
    this.cargaHoraria = function (ofertasInscritas, habilitacoes, constantes) {
        let componentes = recuperarComponentesDasOfertasComHabilitacoesInscritas(ofertasInscritas);
        componentes = removerComponentesDuplicadas(componentes);
        let cargaHoraria = null;

        // O Desenvolvimento de Projeto Integrador só deve ser adicionado para os alunos de especialização
        if (habilitacoes.length > 0) {
            cargaHoraria = inicializarContagem(habilitacoes);
            calcularCargaHorariaPorHabilitacoes(cargaHoraria, componentes, habilitacoes);
            calcularTotaisPorHabilitacao(cargaHoraria, habilitacoes);
        }
        else {
            cargaHoraria = inicializarContagem(habilitacoes);
            calcularCargaHorariaSemHabilitacoes(cargaHoraria, componentes, constantes);
        }
        cargaHoraria.total = componentes.length;

        return cargaHoraria;
    };

    function recuperarComponentesDasOfertasComHabilitacoesInscritas(ofertasInscritas) {
        return ofertasInscritas.map(function (oferta) {
            oferta.componente.selecionadaParaHabilitacoes = oferta.selecionadaParaHabilitacoes;
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

    function inicializarContagem(habilitacoes) {
        const cargaHoraria = [];
        for (var i = 0; i < habilitacoes.length; ++i) {

            // Cada habilitação conta os componentes novos (que não estão selecionados para outras
            // habilitações) e os componentes repetidos, que estão selecionados para outras habilitações
            const cargaHorariaParaHabilitacao = {
                // Carga horária dos componentes (Ob)rigatórios, (Op)cionais e (L)ivres
                componentesNovos: [ 0, 0, 0 ],
                componentesRepetidos: [ 0, 0, 0 ],
            };

            cargaHoraria.push(cargaHorariaParaHabilitacao);
        }
        if (habilitacoes.length == 0) {
            cargaHoraria.componentesObrigatorios = 0;
            cargaHoraria.componentesLivres = 0;
        }
        return cargaHoraria;
    }

    function calcularCargaHorariaPorHabilitacoes(cargaHoraria, componentes, habilitacoes) {
        componentes.forEach(function(componente) {
            for (let j = 0; j < habilitacoes.length; ++j) {
                if (componente.selecionadaParaHabilitacoes[habilitacoes[j]]) {
                    const prioridade = recuperarPrioridadePara(componente, habilitacoes[j]);

                    if (componenteEhNovoParaHabilitacao(componente, habilitacoes, j)) {
                        cargaHoraria[j].componentesNovos[prioridade]++;
                    } else {
                        cargaHoraria[j].componentesRepetidos[prioridade]++;
                    }
                }
            }
        });
    }

    // Retorna 0, 1 ou 2 de acordo com a prioridade dessa componente na habilitação
    // 0 = Obrigatória
    function recuperarPrioridadePara(componente, habilitacaoId) {
        const prioridade = componente.prioridades.find(function(prioridade) {
            return prioridade.id == habilitacaoId;
        });
        return prioridade.prioridade; 
    }

    /**
     * Verifica se o componente já havia sido selecionado para uma habilitação anterior a esta
     */
    function componenteEhNovoParaHabilitacao(componente, habilitacoes, habilitacaoAtual) {
        for (i = 0; i < habilitacaoAtual; i++) {
            if (componente.selecionadaParaHabilitacoes[ habilitacoes[i] ]) {
                return false;
            }
        }
        return true;
    }

    function calcularTotaisPorHabilitacao(cargaHoraria, habilitacoes) {
        cargaHoraria.totalHabilitacoes = [ 0 ];

        for (let i = 1; i <= habilitacoes.length; i++) {
            const totalDeComponentesParaHabilitacao = cargaHoraria[i - 1].componentesNovos[0] + cargaHoraria[i - 1].componentesRepetidos[0]
                + cargaHoraria[i - 1].componentesNovos[1] + cargaHoraria[i - 1].componentesRepetidos[1]
                + cargaHoraria[i - 1].componentesNovos[2] + cargaHoraria[i - 1].componentesRepetidos[2];

            cargaHoraria.totalHabilitacoes.push(totalDeComponentesParaHabilitacao);
        }
    }

    function calcularCargaHorariaSemHabilitacoes(cargaHoraria, componentes, constantes) {
        componentes.forEach(function(componente) {
            if (ehObrigatorio(componente, constantes)) {
                cargaHoraria.componentesObrigatorios++;
            } else {
                cargaHoraria.componentesLivres++;
            }
        });
    }

    function ehObrigatorio(componente, constantes) {
        return constantes.COMPONENTES_OBRIGATORIAS_PARA_EXTENSAO_E_APERFEICOAMENTO.includes(componente.id);
    }

});
