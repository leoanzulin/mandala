/**
 * Controla a pré-inscrição dos usuários em componentes curriculares (simulação).
 */

angular.module('simuladorApp', ['ngResource', 'ui.bootstrap', 'servicos']);

angular.module('simuladorApp').controller("controlador", ["$scope", "$uibModal", "$log", "Componente", "PreinscricaoComponente", "Habilitacao", "Oferta",
        function ($scope, $uibModal, $log, Componente, PreinscricaoComponente, Habilitacao, Oferta) {

    // Cada período contém componentes curriculares
    $scope.periodos = [];
    var todasComponentes = [];
    var idsDasComponentesSelecionadas = [];
    var ofertas;
    var mapaDePeriodosParaIndices = [];
    
    $scope.componentesDisponiveis = [];
    $scope.periodoSelecionado = -1;

    // Faz o carregamento de todas as componentes do sistema, todas as
    // ofertas do período aberto e todas as ofertas que o aluno escolheu.
    todasComponentes = Componente.query(function() {
        // Faz uma cópia do vetor
        $scope.componentesDisponiveis = todasComponentes.slice();

        // Recupera todas as ofertas de componentes
        ofertas = Oferta.doPeriodoAberto(function() {
            
            console.log('asdf ' + JSON.stringify(ofertas) + ' asdf');
            
            montarMapaDePeriodosParaIndices(ofertas);

            for (var i = 0; i < mapaDePeriodosParaIndices.length; ++i) {
                var partes = mapaDePeriodosParaIndices[i].split('_');

                $scope.periodos.push({
                    componentes: [],
                    mes: partes[1],
                    ano: partes[0],
                });
            }

            // Carrega preinscrições cadastradas se já houver alguma
            var preInscricoesSalvas = PreinscricaoComponente.get(function() {
    //            console.log(preInscricoesSalvas);
                for (var i in preInscricoesSalvas) {

                    // No objeto resposta vêm algumas coisas que não são componentes curriculares
                    // http://stackoverflow.com/questions/2647867/how-to-determine-if-variable-is-undefined-or-null
                    if (typeof preInscricoesSalvas[i].periodo == "undefined") continue;

                    var periodoDaComponente = preInscricoesSalvas[i].periodo;
                    while (periodoDaComponente > $scope.periodos.length) {
                        $scope.periodos.push({componentes: []});
                    }

                    $scope.periodos[periodoDaComponente - 1].componentes.push(
                        recuperarComponenteDeId(preInscricoesSalvas[i].componente_id)
                    );
                    idsDasComponentesSelecionadas.push(preInscricoesSalvas[i].componente_id);
                    removerComponenteDaListaDeDisponiveis(preInscricoesSalvas[i].componente_id);

                }
            });
        });
    });

    function montarMapaDePeriodosParaIndices(ofertas) {
        var periodos = [];
        for (var i in ofertas) {
            if (typeof ofertas[i].ano == "undefined") continue;
            var periodo = ofertas[i].ano + '_' + completarComZero(ofertas[i].mes);
            if (periodos.indexOf(periodo) == -1) {
                periodos.push(periodo);
            }
        }
        periodos.sort();
        mapaDePeriodosParaIndices = periodos;
//        console.log(mapaDePeriodosParaIndices);
    }

    function completarComZero(numero) {
        if (numero < 10) {
            return '0' + numero;
        }
        return numero;
    }

    var habilitacao1 = -1;
    var habilitacao2 = -1;
    $scope.habilitacoes = Habilitacao.get(function() {
        habilitacao1 = $scope.habilitacoes[0].id;
        if ($scope.habilitacoes.length > 1) {
            habilitacao2 = $scope.habilitacoes[1].id;
        }
    });

    $scope.selecionar = function(componente) {
        componente.selecionado = !componente.selecionado;
    };

    $scope.adicionarComponentes = function(periodo) {
        $scope.periodoSelecionado = periodo + 1;
    };

    // Adiciona componentes selecionados ao período selecionado
    $scope.adicionarComponentesSelecionados = function() {
        componentes = $scope.componentesDisponiveis;
        var idsParaRemover = [];

        for (var i in componentes) {
            if (componentes[i].selecionado) {
                componentes[i].selecionado = false;
                idsParaRemover.push(componentes[i].id);
                idsDasComponentesSelecionadas.push(componentes[i].id);
                $scope.periodos[$scope.periodoSelecionado - 1].componentes.push(componentes[i]);
            }
        }

        for (var i in idsParaRemover) {
            removerComponenteDaListaDeDisponiveis(idsParaRemover[i]);
        }

//        console.log('Componentes selecionadas = ' + idsDasComponentesSelecionadas);
    };

    function removerComponenteDaListaDeDisponiveis(id) {
        for (var i in $scope.componentesDisponiveis) {
            if ($scope.componentesDisponiveis[i].id === id) {
                $scope.componentesDisponiveis.splice(i, 1);
                return;
            }
        }
    };

    $scope.cancelarAdicionarComponentes = function() {
        $scope.periodoSelecionado = -1;
    };

    $scope.adicionarNovoPeriodo = function() {
        $scope.periodos.push({"componentes": []});
    };

    $scope.removerPeriodo = function(periodo) {
        if (confirm('Tem certeza que deseja remover o ' + (periodo + 1) + 'º período de sua grade?')) {
            $scope.cancelarAdicionarComponentes();

            var idsASeremRemovidos = [];
            for (var i in $scope.periodos[periodo].componentes) {
                idsASeremRemovidos.push($scope.periodos[periodo].componentes[i].id);
            }
            $scope.periodos.splice(periodo, 1);

            removerIds(idsASeremRemovidos, idsDasComponentesSelecionadas);
            repopularListaDeComponentesNaoSelecionadas();

//            console.log('Componentes selecionadas depois de remoção = ' + idsDasComponentesSelecionadas);   
        }
    };

    function removerIds(listaDeIdsASeremRemovidos, vetorDeIds) {
//        console.log('removerIds(' + listaDeIdsASeremRemovidos + ', ' + vetorDeIds + ')');

        for (var i in listaDeIdsASeremRemovidos) {
            var id = listaDeIdsASeremRemovidos[i];
            var indice = vetorDeIds.indexOf(id);
            vetorDeIds.splice(indice, 1);
        }
    };

    $scope.remover = function(componente, periodo) {
//        console.log('$scope.remover(' + componente + ', ' + periodo + ')');
        if (confirm('Tem certeza que deseja remover o componente "' + componente.nome + '" de sua grade?')) {
            removerComponenteComId(componente.id, $scope.periodos[periodo].componentes);
            var indiceARemover = idsDasComponentesSelecionadas.indexOf(componente.id);
            idsDasComponentesSelecionadas.splice(indiceARemover, 1);
            repopularListaDeComponentesNaoSelecionadas();
//        console.log('Componentes selecionadas depois de remoção = ' + idsDasComponentesSelecionadas);
        }
    };

    $scope.cargaHorariaDoPeriodo = function(periodo) {
        var soma = 0;
        for (var i in $scope.periodos[periodo].componentes) {
            soma += $scope.periodos[periodo].componentes[i].cargaHoraria;
        }
        return soma;
    };

    $scope.cargaHorariaTotal = function() {

//        console.log(JSON.stringify($scope.periodos));

        var cargaHoraria = [
            {total: 0, necessarias: 0, prioritarias: 0, livres: 0},
            {total: 0, necessarias: 0, prioritarias: 0, livres: 0}
        ];

        for (var i in $scope.periodos) {
            for (var j in $scope.periodos[i].componentes) {
                cargaHoraria[0].total++;
                cargaHoraria[1].total++;

                var prioridade1 = $scope.periodos[i].componentes[j].prioridades[habilitacao1];
                var prioridade2 = -1;
                if (habilitacao2 != -1) {
                    prioridade2 = $scope.periodos[i].componentes[j].prioridades[habilitacao2];
                }

//                console.log('prioridade1 = ' + prioridade1 + ', prioridade2 = ' + prioridade2);

                if ($scope.periodos[i].componentes[j].ehNecessaria) {
                    cargaHoraria[0].necessarias++;
                }
                else if (prioridade1 == 1) {
                    cargaHoraria[0].prioritarias++;
                }
                else if (prioridade1 == 2) {
                    cargaHoraria[0].livres++;
                }

                if ($scope.periodos[i].componentes[j].ehNecessaria) {
                    cargaHoraria[1].necessarias++;
                }
                else if (prioridade2 == 1) {
                    cargaHoraria[1].prioritarias++;
                }
                else if (prioridade2 == 2) {
                    cargaHoraria[1].livres++;
                }

            }
        }

        return cargaHoraria;
    };

    function removerComponenteComId(id, vetorDeComponentes) {
        indiceARemover = -1;
        for (var i in vetorDeComponentes) {
            if (vetorDeComponentes[i].id === id) {
                indiceARemover = i;
                break;
            }
        }
        vetorDeComponentes.splice(indiceARemover, 1);
    };

    function repopularListaDeComponentesNaoSelecionadas() {
        $scope.componentesDisponiveis = todasComponentes.slice();
        for (var i in idsDasComponentesSelecionadas) {
            removerComponenteComId(
                idsDasComponentesSelecionadas[i],
                $scope.componentesDisponiveis
            );
        }
    };

    function recuperarComponenteDeId(id) {
        for (var i in todasComponentes) {
            if (todasComponentes[i].id === id) {
                return todasComponentes[i];
            }
        }
    };

//    $scope.consegueTitulo = function(nivel) {
//        var cargaHoraria = $scope.cargaHorariaTotal();
//        var total = cargaHoraria[0].total;
//        if (total >= 360 && nivel == 2) {
//            return true;
//        }
//        else if (total >= 180 && nivel == 1) {
//            return true;
//        }
//        else if (total >= 60 && nivel == 0) {
//            return true;
//        }
//        return false;
//    };

    $scope.open = function(periodoSelecionado, ano, mes) {

        $scope.periodoSelecionado = periodoSelecionado + 1;

        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'myModalContent.html',
            controller: 'ModalInstanceCtrl',
            size: 'lg',
            resolve: {
                componentesDisponiveis: function() {
//                    return $scope.componentesDisponiveis;
                    return filtrarOfertasDoPeriodo(ofertas, periodoSelecionado);
                },
                periodoSelecionado: function() {
                    return periodoSelecionado + 1;
                },
                ano: function() {
                    return ano;
                },
                mes: function() {
                    return mes;
                }
            }
        });

        modalInstance.result.then(function(componentesSelecionados) {
            var idsParaRemover = [];

            for (var i in componentesSelecionados) {
                componentesSelecionados[i].selecionado = false;
                idsParaRemover.push(componentesSelecionados[i].id);
                idsDasComponentesSelecionadas.push(componentesSelecionados[i].id);
                $scope.periodos[$scope.periodoSelecionado - 1].componentes.push(componentesSelecionados[i]);
            }

            for (var i in idsParaRemover) {
                removerComponenteDaListaDeDisponiveis(idsParaRemover[i]);
            }

//            console.log('Componentes selecionadas = ' + idsDasComponentesSelecionadas);

        }, function () {
            $log.info('Modal dismissed at: ' + new Date());
        });
    };

    function filtrarOfertasDoPeriodo(ofertas, periodoSelecionado) {
        var periodo = mapaDePeriodosParaIndices[periodoSelecionado] + 1;
        var ano = periodo.substr(0, 4);
        var mes = periodo.substr(5, 2);

        var ofertasDoPeriodo = ofertas.filter(function(oferta) {
            return oferta.ano == ano && oferta.mes == mes;
        })

        var componentes = ofertasDoPeriodo.map(function(oferta) {
            return recuperarComponenteDeId(oferta.componente_id);
        });

        return removerComponentesQueJaEstaoNoPeriodo(componentes, periodoSelecionado);
    }
    
    function removerComponentesQueJaEstaoNoPeriodo(componentes, periodoSelecionado) {
        return componentes.filter(function(componente) {
            return $scope.periodos[periodoSelecionado].componentes.indexOf(componente) == -1;
        });
    }

}]);

// Please note that $uibModalInstance represents a modal window (instance) dependency.
// It is not the same as the $uibModal service used above.

angular.module('simuladorApp').controller('ModalInstanceCtrl', function ($scope, $uibModalInstance, componentesDisponiveis, periodoSelecionado, ano, mes) {
    $scope.componentesDisponiveis = componentesDisponiveis;
    $scope.periodoSelecionado = periodoSelecionado;
    $scope.ano = ano;
    $scope.mes = mes;

    $scope.ok = function () {
        var componentesSelecionadas = $scope.componentesDisponiveis.filter(function(componente) {
            return componente.selecionado == true;
        });
        $uibModalInstance.close(componentesSelecionadas);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
    
    $scope.selecionar = function(componente) {
        componente.selecionado = !componente.selecionado;
    };
});
