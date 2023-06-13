//INCLUDE /js/bower_components/angular/angular.min
//INCLUDE /js/modulos/servicos
//INCLUDE /js/diretivas/contagemDeComponentes/app
//INCLUDE /js/diretivas/contagemDeComponentesSimples/app
//INCLUDE /js/diretivas/tabelaDeInscricaoEmOfertas/app

/**
 * Módulo que recupera as ofertas em que um usuário está inscrito e suas habilitações,
 * e realiza a contagem de componentes dividios pelas habilitações.
 * 
 * Parâmetro que pode vir na instanciação do controlador:
 * - idVemDaUrl => "true"
 *       Diz se o ID do usuário em questão deve vir da URL atual (id=123). Se
 *       este atributo não estiver definido, será utilizado o ID do usuário que
 *       está logado atualmente.
 */
angular.module('recuperarInscricoesEmOfertas', ['servicos', 'contadorDeComponentes', 'contagemDeComponentes', 'contagemDeComponentesSimples', 'tabelaDeInscricaoEmOfertas']);

angular.module('recuperarInscricoesEmOfertas').controller("controlador", ["$scope", "$attrs", "Oferta", "Habilitacao", "ContadorDeComponentes", "Constantes",
    function ($scope, $attrs, Oferta, Habilitacao, ContadorDeComponentes, Constantes) {

        verificarSeIdVemDaUrlEhValido();

        function verificarSeIdVemDaUrlEhValido() {
            if ($attrs.idVemDaUrl != null && $attrs.idVemDaUrl != 'true') {
                var mensagemDeErro = 'ERRO: Atributo "idVemDaUrl" atribuído à diretiva recuperarInscricoesEmOfertas é inválido: ' + $attrs.idVemDaUrl;
                alert(mensagemDeErro);
                throw new Error(mensagemDeErro);
            }
        }

        $scope.periodos = recuperarPeriodos();
        $scope.habilitacoes = recuperarhabilitacoes();
        const hoje = new Date();
        $scope.mesAtual = hoje.getMonth() + 1;
        $scope.anoAtual = hoje.getFullYear();
        $scope.mostrarOfertasPassadas = false;
        $scope.tipoDeCurso = 2;
        const constantes = Constantes.todas();
        $scope.constantes = constantes;

        function recuperarPeriodos() {
            if ($attrs.idVemDaUrl == 'true') {
                if ($attrs.selecaoParaCertificados == 'true') {
                    return Oferta.selecaoParaCertificadosDoUsuario({
                        'inscricaoId': recuperarIdDaUrl()
                    });
                } else {
                    return Oferta.porPeriodoDoUsuario({
                        'inscricaoId': recuperarIdDaUrl()
                    });
                }
            } else {
                if ($attrs.selecaoParaCertificados == 'true') {
                    return Oferta.selecaoParaCertificados();
                } else {
                    return Oferta.porPeriodo();
                }
            }
        }

        function recuperarIdDaUrl() {
            var url = window.location.href;
            var resultados = /.*&id=(\d+)$/.exec(url);
            return resultados[1];
        }

        function recuperarhabilitacoes() {
            if ($attrs.idVemDaUrl == 'true') {
                return Habilitacao.daInscricao({
                    'id': recuperarIdDaUrl()
                });
            }
            return Habilitacao.get();
        }

        $scope.irParaMesAnterior = function() {
            $scope.mesAtual--;
            if ($scope.mesAtual == 0) {
                $scope.mesAtual = 12;
                $scope.anoAtual--;
            }
        }

        $scope.irParaProximoMes = function() {
            $scope.mesAtual++;
            if ($scope.mesAtual == 13) {
                $scope.mesAtual = 1;
                $scope.anoAtual++;
            }
        }

        $scope.ativarOfertasPassadas = function() {
            $scope.mostrarOfertasPassadas = !$scope.mostrarOfertasPassadas;
        }

        $scope.gerarPdf = function($event) {
            // if ($scope.houveAlgumaMudanca) {
                // alert("Por favor, salve suas inscrições antes de gerar o PDF");
                // $event.preventDefault();
                // return;
            // }
            $scope.validarNumeroDeInscricoes($event);
        }

        $scope.houveMudanca = function() {
            $scope.houveAlgumaMudanca = true;
        }

        // Este código de validação é replicado em ValidadorDeInscricoesEmOfertas, lembrar de alterar lá
        // se aqui for alterado
        $scope.validarNumeroDeInscricoes = function($event) {
            const ofertasInscritas = recuperarOfertasInscritas();
            let problemas = '';

            // Especialização
            if ($scope.tipoDeCurso === 2) {
                const idsHabilitacoes = $scope.habilitacoes.map(function(habilitacao) {
                    return habilitacao.id;
                });
                const cargaHoraria = ContadorDeComponentes.cargaHoraria(ofertasInscritas, idsHabilitacoes, constantes);

                $scope.habilitacoes.forEach(function(habilitacao, i) {
                    if (!validarNumeroMinimoDeComponentesObrigatoriosPorHabilitacao(cargaHoraria, i)) {
                        problemas += '- O número mínimo de componentes obrigatórios para a habilitação ' + (i + 1) + ' não foi atendido\n';
                    }
                    if (!validarNumeroMinimoDeComponentesOptativosPorHabilitacao(cargaHoraria, i)) {
                        problemas += '- O número mínimo de componentes optativos para a habilitação ' + (i + 1) + ' não foi atendido\n';
                    }
                    // if (!validarNumeroMinimoDeComponentesNovas(cargaHoraria, i)) {
                    //     problemas += '- O número mínimo de componentes novas (' + constantes.NUMERO_MINIMO_DE_COMPONENTES_NOVAS_POR_HABILITACAO + ') para a habilitação ' + (i + 1) + ' não foi atendido\n';
                    // }
                    if (!validarNumeroMinimoDeComponentesTotais(cargaHoraria, i)) {
                        problemas += '- O número mínimo (' + constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[i + 1] + ') de componentes totais para a habilitação ' + (i + 1) + ' não foi atendido\n';
                    }
                });
                if (!validarNumeroMinimoDeComponentesTotaisDeTodasHabilitacoes(cargaHoraria)) {
                    problemas += '- O número mínimo (' + constantes.NUMERO_MINIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES[$scope.habilitacoes.length] + ') de componentes distintos somando todas as habilitações não foi atendido\n';
                }

            } else {
                const cargaHoraria = ContadorDeComponentes.cargaHoraria(ofertasInscritas, [], constantes);

                if (!validarComponentesObrigatoriosSemHabilitacao(cargaHoraria, ofertasInscritas)) {
                    problemas += '- ' + constantes.STRING_COMPONENTES_OBRIGATORIOS_PARA_EXTENSAO_E_APERFEICOAMENTO + '\n';
                }
                if (!validarNumeroDeComponentesLivresSemHabilitacao(cargaHoraria)) {
                    const numeroDeComponentesLivres = $scope.tipoDeCurso === 0
                        ? constantes.NUMERO_DE_COMPONENTES_LIVRES_PARA_EXTENSAO
                        : constantes.NUMERO_DE_COMPONENTES_LIVRES_PARA_APERFEICOAMENTO;
                    problemas += '- Você deve selecionar pelo menos ' + numeroDeComponentesLivres + ' componentes livres';
                }
            }

            if (problemas !== '') {
                alert(problemas);
                // $event.preventDefault();
            }
        };

        function recuperarOfertasInscritas() {
            const ofertasInscritas = [];

            $scope.periodos.forEach(function(periodo) {
                periodo.ofertas.forEach(function(oferta) {
                    if (oferta.selecionada) {
                        ofertasInscritas.push(oferta);
                    }
                });
            });

            return ofertasInscritas;
        };

        function validarNumeroMinimoDeComponentesObrigatoriosPorHabilitacao(cargaHoraria, indiceHabilitacao) {
            return cargaHoraria[indiceHabilitacao].componentesNovos[0] + cargaHoraria[indiceHabilitacao].componentesRepetidos[0] >= constantes.NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS;
        }

        function validarNumeroMinimoDeComponentesOptativosPorHabilitacao(cargaHoraria, indiceHabilitacao) {
            return cargaHoraria[indiceHabilitacao].componentesNovos[1] + cargaHoraria[indiceHabilitacao].componentesRepetidos[1] >= constantes.NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS;
        }

        function validarNumeroMinimoDeComponentesTotais(cargaHoraria, indiceHabilitacao) {
            return constantes.NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES[indiceHabilitacao + 1] <= cargaHoraria.totalHabilitacoes[indiceHabilitacao + 1];
        }

        function validarNumeroMaximoDeComponentesTotais(cargaHoraria, indiceHabilitacao) {
            return cargaHoraria.totalHabilitacoes[indiceHabilitacao + 1] <= constantes.NUMERO_MAXIMO_DE_COMPONENTES_POR_HABILITACAO[indiceHabilitacao + 1];
        }

        function validarNumeroMinimoDeComponentesNovas(cargaHoraria, indiceHabilitacao) {
            return constantes.NUMERO_MINIMO_DE_COMPONENTES_NOVAS_POR_HABILITACAO <=
                cargaHoraria[indiceHabilitacao].componentesNovos[0]
                + cargaHoraria[indiceHabilitacao].componentesNovos[1]
                + cargaHoraria[indiceHabilitacao].componentesNovos[2];
        }

        function validarNumeroMinimoDeComponentesTotaisDeTodasHabilitacoes(cargaHoraria) {
            return cargaHoraria.total >= constantes.NUMERO_MINIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES[$scope.habilitacoes.length];
        }

        function validarNumeroMaximoDeComponentesTotaisDeTodasHabilitacoes(cargaHoraria) {
            return cargaHoraria.total <= constantes.NUMERO_MAXIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES[$scope.habilitacoes.length];
        }

        function validarComponentesObrigatoriosSemHabilitacao(cargaHoraria, ofertasInscritas) {
            const componentesObrigatoriasEstaoSelecionadas = constantes.COMPONENTES_OBRIGATORIAS_PARA_EXTENSAO_E_APERFEICOAMENTO.every(function(idComponenteObrigatorio) {
                return ofertasInscritas.some(function(oferta) {
                    return oferta.componente.id == idComponenteObrigatorio;
                });
            });
            return componentesObrigatoriasEstaoSelecionadas
                && cargaHoraria.componentesObrigatorios == constantes.NUMERO_DE_COMPONENTES_OBRIGATORIOAS_PARA_EXTENSAO_E_APERFEICOAMENTO;
        }

        function validarNumeroDeComponentesLivresSemHabilitacao(cargaHoraria) {
            const numeroDeComponentesLivres = $scope.tipoDeCurso === 0
                ? constantes.NUMERO_DE_COMPONENTES_LIVRES_PARA_EXTENSAO
                : constantes.NUMERO_DE_COMPONENTES_LIVRES_PARA_APERFEICOAMENTO;
            return cargaHoraria.componentesLivres >= numeroDeComponentesLivres;
        }

        $scope.limparHistorico = function($event) {
            if (!confirm("Ao fazer isto, todas as inscriçõees em ofertas reprovadas no passado deste aluno serão trancadas. Deseja continuar?")) {
                $event.preventDefault();
            }
        }

    }]);
