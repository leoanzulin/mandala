/**
 * Controla a inscrição dos alunos nas ofertas do período aberto atualmente.
 */
angular.module('inscricaoApp', ['ngResource', 'ui.bootstrap', 'servicos']);

angular.module('inscricaoApp').controller("controlador", ["$scope", "$uibModal", "$log", "Oferta", "Habilitacao", "InscricaoOferta", "Componente", "Configuracao",
    function ($scope, $uibModal, $log, Oferta, Habilitacao, InscricaoOferta, Componente, Configuracao) {

        // Cada período contém componentes curriculares
        $scope.componentes = [];
        var todasComponentes = [];
        var ofertasDoPeriodo = [];
        $scope.habilitacao1 = 0;
        $scope.habilitacao2 = 0;
        $scope.periodos = [];

        $scope.configuracoes = {};
        var configuracoesBanco = Configuracao.query(function () {
            for (var i in configuracoesBanco) {
                if (typeof configuracoesBanco[i].atributo == "undefined") {
                    continue;
                }
                $scope.configuracoes[configuracoesBanco[i].atributo] = configuracoesBanco[i].valor;
            }
            //~ console.log($scope.configuracoes);
            carregarComponentes();
        });

        function carregarComponentes() {
            todasComponentes = Componente.query(function () {
                carregarOfertas();
            });
        }

        function carregarOfertas() {
            ofertasDestePeriodo = Oferta.periodo({
                deAno: $scope.configuracoes['inscricoes.inicio_periodo_aberto.ano'],
                deMes: $scope.configuracoes['inscricoes.inicio_periodo_aberto.mes'],
                ateAno: $scope.configuracoes['inscricoes.fim_periodo_aberto.ano'],
                ateMes: $scope.configuracoes['inscricoes.fim_periodo_aberto.mes'],
            }, function () {
                carregarHabilitacoes();
            });
        }

        function carregarHabilitacoes() {
            $scope.habilitacoes = Habilitacao.get(function () {
                $scope.habilitacao1 = $scope.habilitacoes[0].id;
                if ($scope.habilitacoes.length > 1) {
                    $scope.habilitacao2 = $scope.habilitacoes[1].id;
                }
                processarOfertasEComponentes();
            });
        }

        function processarOfertasEComponentes() {
            for (var i in ofertasDestePeriodo) {
                if (typeof ofertasDestePeriodo[i].ano == "undefined") {
                    continue;
                }
                var componente = angular.copy(recuperarComponenteDeId(ofertasDestePeriodo[i].componente_id));
                componente.docentes = formatarNomeDeDocentes(ofertasDestePeriodo[i].docentes);
                componente.bloqueada = ofertasDestePeriodo[i].bloqueada;
//                setarPrioridades(componente);
                var indicePeriodo = getPeriodoOuCriaSeNaoExistir(ofertasDestePeriodo[i].mes, ofertasDestePeriodo[i].ano);
                $scope.periodos[indicePeriodo].componentes.push(componente);
            }
            //~ console.log($scope.periodos);
            carregarInscricoesEmOfertas();
        }

        function formatarNomeDeDocentes(listaDeDocentes) {
            var nomes = listaDeDocentes.map(function(docente) { return docente.nome + ' ' + docente.sobrenome; });
            return nomes.join(', ');
        }

        function carregarInscricoesEmOfertas() {
            var ofertasInscritas = InscricaoOferta.get(function () {

                //~ console.log(ofertasInscritas);

                for (var i in ofertasInscritas) {
                    if (typeof ofertasInscritas[i].ano == "undefined") {
                        continue;
                    }

                    var indicePeriodo = periodoExiste(ofertasInscritas[i].mes, ofertasInscritas[i].ano);
                    // Verifica se é uma oferta de um período que está aberto
                    if (indicePeriodo != -1) {
                        selecionarComponenteDeIdNoPeriodo(ofertasInscritas[i].componente_id, indicePeriodo);
                    }
                }
            });
        }

        function selecionarComponenteDeIdNoPeriodo(idComponente, indicePeriodo) {
//            console.log('selecionarcomp ' + idComponente + ' no periodo ' + indicePeriodo);
            for (var i in $scope.periodos[indicePeriodo].componentes) {
                if ($scope.periodos[indicePeriodo].componentes[i].id == idComponente) {
                    $scope.periodos[indicePeriodo].componentes[i].selecionado = true;
                    return;
                }
            }
        }

        function periodoExiste(mes, ano) {
            for (var i in $scope.periodos) {
                if ($scope.periodos[i].mes == mes && $scope.periodos[i].ano == ano) {
                    return i;
                }
            }
            return -1;
        }

        /**
         * Procura o período em $scope, retornando-o se existir. Caso contrário,
         * cria um novo período.
         */
        function getPeriodoOuCriaSeNaoExistir(mes, ano) {
            if (periodoExiste(mes, ano) != -1) {
                return periodoExiste(mes, ano);
            }
            $scope.periodos.push({
                mes: mes,
                ano: ano,
                componentes: []
            });
            //~ console.log('Novo período sendo adicionado: ' + mes + ', ' + ano);
            return $scope.periodos.length - 1;
        }

        function recuperarComponenteDeId(id) {
            for (var i in todasComponentes)
                if (todasComponentes[i].id === id)
                    return todasComponentes[i];
        }

        $scope.selecionar = function (componente) {
            if (!componente.bloqueada) {
                componente.selecionado = !componente.selecionado;
            }
        };

    }]);
