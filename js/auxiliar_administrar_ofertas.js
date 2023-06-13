/**
 * Controla a administração de ofertas de componentes curriculares por período.
 * 
 * Usado na view admin/gerenciarOfertas.
 */
angular.module('administrarOfertasApp', ['ngResource', 'ui.bootstrap', 'servicos']);

angular.module('administrarOfertasApp').controller("controlador", ["$scope", "$uibModal", "$log", "Componente", "Oferta", "Docente", "DocenteOferta", "Tutor", "TutorOferta",
        function ($scope, $uibModal, $log, Componente, Oferta, Docente, DocenteOferta, Tutor, TutorOferta) {

    // Cada período contém componentes curriculares, ano e mês
    $scope.periodos = [];
    var todosDocentes = [];
    var todosTutores = [];

    // Popula os períodos com as ofertas que já estão salvas
    var todasComponentes = Componente.query(function() {
        var ofertasSalvas = Oferta.query(function() {
            for (var i in ofertasSalvas) {

                // No objeto resposta vêm algumas coisas que não são ofertas
                // http://stackoverflow.com/questions/2647867/how-to-determine-if-variable-is-undefined-or-null
                if (typeof ofertasSalvas[i].ano == "undefined") {
                    continue;
                }

                var anoDaOferta = ofertasSalvas[i].ano;
                var mesDaOferta = ofertasSalvas[i].mes;
                var indicePeriodoAtual = indicePeriodo(anoDaOferta, mesDaOferta);

                if (indicePeriodoAtual == -1) {
                    var novoPeriodo = {
                        'componentes': [],
                        'ano': anoDaOferta,
                        'mes': mesDaOferta,
                        'podeSerDeletado': true,
                    };
                    $scope.periodos.push(novoPeriodo);
                    indicePeriodoAtual = $scope.periodos.indexOf(novoPeriodo);
                }

                var componente = angular.copy(recuperarComponenteDeId(ofertasSalvas[i].componente_id));

                // Uma oferta só pode ser excluída enquanto nenhum aluno tiver se inscrito nela
                componente.podeSerDeletada = ofertasSalvas[i].podeSerDeletada;
                componente.docentes = ofertasSalvas[i].docentes;
                componente.tutores = ofertasSalvas[i].tutores;
                componente.inscricoes = ofertasSalvas[i].inscricoes;
                componente.linkMoodle = ofertasSalvas[i].linkMoodle;
                componente.codigoMoodle = ofertasSalvas[i].codigoMoodle;
                if (!componente.podeSerDeletada) {
                    $scope.periodos[indicePeriodoAtual].podeSerDeletado = false;
                }

                $scope.periodos[indicePeriodoAtual].componentes.push(componente);
            }

            todosDocentes = Docente.query();
            todosTutores = Tutor.query();
        });
    });

    function indicePeriodo(ano, mes) {
        for (var i in $scope.periodos)
            if ($scope.periodos[i].ano == ano && $scope.periodos[i].mes == mes)
                return i;
        return -1;
    }

    function recuperarComponenteDeId(id) {
        for (var i in todasComponentes)
            if (todasComponentes[i].id === id)
                return todasComponentes[i];
    };

    $scope.selecionar = function(componente) {
        componente.selecionado = !componente.selecionado;
    };

    $scope.adicionarNovoPeriodo = function() {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'anomesperiodo.html',
            controller: 'anoMesController',
            size: 'lg',
            resolve: {
                proximoPeriodo: function() {
                    return proximoPeriodoDisponivel();
                }
            }
        });

        modalInstance.result.then(function(anomes) {
            if (indicePeriodo(anomes.ano, anomes.mes) == -1) {
                $scope.periodos.push({
                    'componentes': [],
                    'ano': anomes.ano,
                    'mes': anomes.mes
                });
            }
        }, function () {
//            $log.info('Modal dismissed at: ' + new Date());
        });
    }

    function proximoPeriodoDisponivel() {
        if ($scope.periodos.length == 0) {
            var hoje = new Date();
            return {'ano': hoje.getFullYear(), 'mes': hoje.getMonth() + 1};
        }

        var maior = {'ano': -1, 'mes': -1};
        for (var i in $scope.periodos) {
            var mesesPeriodo = $scope.periodos[i].ano * 12 + $scope.periodos[i].mes;
            var mesesMaior = maior.ano * 12 + maior.mes;
            if (mesesPeriodo > mesesMaior) {
                maior.ano = $scope.periodos[i].ano;
                maior.mes = $scope.periodos[i].mes;
            }
        }

        maior.mes++;
        if (maior.mes == 13) {
            maior.ano++;
            maior.mes = 1;
        }
        return maior;
    }

    $scope.removerPeriodo = function(periodo) {
        if (confirm('Tem certeza que deseja remover o período de ' + periodo.mes + '/' + periodo.ano + '?')) {
            var indice = $scope.periodos.indexOf(periodo);
            $scope.periodos.splice(indice, 1);
        }
    };

    $scope.remover = function(componente, periodo) {
        if (confirm('Tem certeza que deseja remover a oferta do componente "' + componente.nome + '"?')) {
            var indice = $scope.periodos.indexOf(periodo);
            removerComponenteComId(componente.id, $scope.periodos[indice].componentes);
        }
    };

    function removerComponenteComId(id, vetorDeComponentes) {
        indiceARemover = -1;
        for (var i in vetorDeComponentes) {
            if (vetorDeComponentes[i].id === id) {
                indiceARemover = i;
                break;
            }
        }
        if (indiceARemover != -1) {
            vetorDeComponentes.splice(indiceARemover, 1);
        }
    };

    $scope.cargaHorariaDoPeriodo = function(periodo) {
        var soma = 0;
        var indice = $scope.periodos.indexOf(periodo);
        for (var i in $scope.periodos[indice].componentes) {
            soma += $scope.periodos[indice].componentes[i].cargaHoraria;
        }
        return soma;
    };

    $scope.adicionarComponentes = function(periodo) {

        var indicePeriodoAtual = $scope.periodos.indexOf(periodo);
//        console.log('indice do periodo atual = ' + indicePeriodoAtual);

        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'myModalContent.html',
            controller: 'ModalInstanceCtrl',
            size: 'lg',
            resolve: {
                componentesDisponiveis: function() {
                    return removerComponentesQueJaEstaoNoPeriodoSelecionado(todasComponentes, periodo);
                },
                mes: function() {
                    return periodo.mes;
                },
                ano: function() {
                    return periodo.ano;
                }
            }
        });

        modalInstance.result.then(function(componentesSelecionados) {
            //~ console.log('componentes selecionados = ' + componentesSelecionados);
            for (var i in componentesSelecionados) {
                componentesSelecionados[i].selecionado = false;
                componentesSelecionados[i].docentes = [];
                componentesSelecionados[i].podeSerDeletada = true;
                $scope.periodos[indicePeriodoAtual].componentes.push(componentesSelecionados[i]);
            }
        }, function () {
//            $log.info('Modal dismissed at: ' + new Date());
        });
    };

    function removerComponentesQueJaEstaoNoPeriodoSelecionado(componentes, periodo) {
        var indice = $scope.periodos.indexOf(periodo);
        var componentesDoPeriodo = $scope.periodos[indice].componentes;
        var copiaComponentes = componentes.slice();

        for (i in componentesDoPeriodo) {
            for (j in copiaComponentes) {
                if (copiaComponentes[j].id == componentesDoPeriodo[i].id) {
                    copiaComponentes.splice(j, 1);
                    break;
                }
            }
        }

//        console.log('Retornando ' + JSON.stringify(copiaComponentes));
        return copiaComponentes;
    }

    $scope.associarDocentes = function(componente, periodo) {
        
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'associarDocentes.html',
            controller: 'associarDocentesController',
            size: 'lg',
            resolve: {
                componenteSelecionada: function() {
                    return componente;
                },
                docentesDisponiveis: function() {
//                    return removerComponentesQueJaEstaoNoPeriodoSelecionado(todasComponentes, periodo);
                    return removerDocentesQueJaEstaoAssociadosAOferta(todosDocentes, periodo, componente);
                },
                tutoresDisponiveis: function() {
                    return removerTutoresQueJaEstaoAssociadosAOferta(todosTutores, periodo, componente);
                },
                mes: function() {
                    return periodo.mes;
                },
                ano: function() {
                    return periodo.ano;
                }
            }
        });

        modalInstance.result.then(function(resposta) {
            //~ console.log('componentes selecionados = ' + componentesSelecionados);
            var docentesSelecionados = resposta.docentesSelecionados;
            var tutoresSelecionados = resposta.tutoresSelecionados;
            for (var i in docentesSelecionados) {
                docentesSelecionados[i].selecionado = false;
//                $scope.periodos[indicePeriodoAtual].componentes.push(docentesSelecionados[i]);
                componente.docentes.push(docentesSelecionados[i]);
            }
            for (var i in tutoresSelecionados) {
                tutoresSelecionados[i].selecionado = false;
                componente.tutores.push(tutoresSelecionados[i]);
            }
        }, function () {
//            $log.info('Modal dismissed at: ' + new Date());
        });
        
    }

    function removerDocentesQueJaEstaoAssociadosAOferta(todosDocentes, periodo, componente) {
        var docentesAssociados = componente.docentes;
        var copiaDocentes = todosDocentes.slice();

        for (i in docentesAssociados) {
            for (j in copiaDocentes) {
                if (copiaDocentes[j].cpf == docentesAssociados[i].cpf) {
                    copiaDocentes.splice(j, 1);
                    break;
                }
            }
        }

        return copiaDocentes;
    }

    function removerTutoresQueJaEstaoAssociadosAOferta(todosTutores, periodo, componente) {
        var tutoresAssociados = componente.tutores;
        var copiaTutores = todosTutores.slice();

        for (i in tutoresAssociados) {
            for (j in copiaTutores) {
                if (copiaTutores[j].cpf == tutoresAssociados[i].cpf) {
                    copiaTutores.splice(j, 1);
                    break;
                }
            }
        }

        return copiaTutores;
    }

    $scope.desassociarDocente = function(docente, componente) {
        if (confirm('Tem certeza que deseja desassociar o docente "' + docente.nome + '" desta oferta?')) {
            var indiceDoDocente = componente.docentes.indexOf(docente);
            componente.docentes.splice(indiceDoDocente, 1);
        }
    }

    $scope.desassociarTutor = function(tutor, componente) {
        if (confirm('Tem certeza que deseja desassociar o tutor "' + tutor.nome + '" desta oferta?')) {
            var indiceDoTutor = componente.tutores.indexOf(tutor);
            componente.tutores.splice(indiceDoTutor, 1);
        }
    }

    $scope.adicionarInfoMoodle = function(componente, periodo) {

        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'adicionarInfoMoodle.html',
            controller: 'adicionarInfoMoodleController',
            size: 'lg',
            resolve: {
                componenteSelecionada: function() {
                    return componente;
                },
                mes: function() {
                    return periodo.mes;
                },
                ano: function() {
                    return periodo.ano;
                }
            }
        });

        modalInstance.result.then(function(resposta) {
            componente.linkMoodle = resposta.linkMoodle;
            componente.codigoMoodle = resposta.codigoMoodle;
            console.log(componente);
        }, function () {
//            $log.info('Modal dismissed at: ' + new Date());
        });

    }

}]);

// Please note that $uibModalInstance represents a modal window (instance) dependency.
// It is not the same as the $uibModal service used above.

angular.module('administrarOfertasApp').controller('ModalInstanceCtrl', function ($scope, $uibModalInstance, componentesDisponiveis, mes, ano) {
    $scope.componentesDisponiveis = componentesDisponiveis;
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

angular.module('administrarOfertasApp').controller('anoMesController', function ($scope, $uibModalInstance, proximoPeriodo) {
    $scope.ano = proximoPeriodo.ano;
    $scope.mes = proximoPeriodo.mes;

    $scope.ok = function () {
        var anomes = {ano: $scope.ano, mes: $scope.mes};
        $uibModalInstance.close(anomes);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
});

angular.module('administrarOfertasApp').controller('associarDocentesController', function ($scope, $uibModalInstance, componenteSelecionada, docentesDisponiveis, tutoresDisponiveis, mes, ano) {
    $scope.componenteSelecionada = componenteSelecionada;
    $scope.docentesDisponiveis = docentesDisponiveis;
    $scope.tutoresDisponiveis = tutoresDisponiveis;
    $scope.ano = ano;
    $scope.mes = mes;
    
    $scope.ok = function () {
        var docentesSelecionados = $scope.docentesDisponiveis.filter(function(docente) {
            return docente.selecionado == true;
        });
        var tutoresSelecionados = $scope.tutoresDisponiveis.filter(function(tutor) {
            return tutor.selecionado == true;
        });
//        console.log('tutores selecionados = ' + JSON.stringify(tutoresSelecionados));
        $uibModalInstance.close({
            "docentesSelecionados": docentesSelecionados,
            "tutoresSelecionados": tutoresSelecionados
        });
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

    $scope.selecionar = function(pessoa) {
        pessoa.selecionado = !pessoa.selecionado;
    };
});

angular.module('administrarOfertasApp').controller('adicionarInfoMoodleController', function ($scope, $uibModalInstance, componenteSelecionada, mes, ano) {
    $scope.componenteSelecionada = componenteSelecionada;
    $scope.linkMoodle = componenteSelecionada.linkMoodle;
    $scope.codigoMoodle = componenteSelecionada.codigoMoodle;
    $scope.ano = ano;
    $scope.mes = mes;

    $scope.ok = function () {
        $uibModalInstance.close({
            "linkMoodle": $scope.linkMoodle,
            "codigoMoodle": $scope.codigoMoodle
        });
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
});
