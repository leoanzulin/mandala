/**
 * Controla o gerenciamento de ofertas de componentes curriculares por período.
 * 
 * Usado na view oferta/gerenciar.
 */
angular.module('gerenciarOfertasApp', ['ngResource', 'ui.bootstrap', 'servicos']);

angular.module('gerenciarOfertasApp').controller("controlador", ["$scope", "$uibModal", "Componente", "Oferta", "Docente", "Tutor",
        function ($scope, $uibModal, Componente, Oferta, Docente, Tutor) {

    // Cada turma contém períodos, que contêm componentes curriculares, ano e mês
    $scope.turmasDisponiveis = [1, 2];
    $scope.turmaAtual = 1;
    $scope.turmaASerDuplicada = 1;
    var todasComponentes =[];
    var todosDocentes = [];
    var todosTutores = [];

    var novaOferta = {
        // Campos que deverão ser preenchidos
        'componente': null,
        'ano': 0,
        'mes': 0,
        'nome': '',
        'turma': 0,
        // Campos que não precisam ser preenchidos
        'podeSerDeletada': true,
        'bloqueada': false,
        'linkMoodle': null,
        'codigoMoodle': null,
        'docentes': [],
        'tutores': [],
        'inscricoes': []
    };

    $scope.turmas = Oferta.todasPorTurmaEPeriodo(function() {
        todasComponentes = Componente.query();
        todosDocentes = Docente.query();
        todosTutores = Tutor.query();
    });

    $scope.adicionarNovoPeriodo = function() {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'adicionarNovoPeriodo.html',
            controller: 'anoMesController',
            size: 'lg',
            resolve: {
                proximoPeriodo: proximoPeriodoDisponivel()
            }
        });

        modalInstance.result.then(function(anomes) {
            var turmaAtual = $scope.turmas[$scope.turmaAtual - 1];
            if (!periodoExiste(anomes.ano, anomes.mes)) {
                turmaAtual.periodos.push({
                    'ofertas': [],
                    'ano': anomes.ano,
                    'mes': anomes.mes,
                    'podeSerDeletado': true
                });
            }
        });
    }

    function periodoExiste(ano, mes) {
        var turmaAtual = $scope.turmas[$scope.turmaAtual - 1];
        for (var i in turmaAtual.periodos)
            if (turmaAtual.periodos[i].ano == ano && turmaAtual.periodos[i].mes == mes)
                return true;
        return false;
    }

    function proximoPeriodoDisponivel() {
        var turmaAtual = $scope.turmas[$scope.turmaAtual - 1];
        if (turmaAtual.periodos.length == 0) {
            var hoje = new Date();
            return {'ano': hoje.getFullYear(), 'mes': hoje.getMonth() + 1};
        }

        return ultimoPeriodoDaTurmaMaisUmMes(turmaAtual);
    }

    function ultimoPeriodoDaTurmaMaisUmMes(turma) {
        var maior = {'ano': -1, 'mes': -1};

        for (var i in turma.periodos) {
            var mesesPeriodo = turma.periodos[i].ano * 12 + turma.periodos[i].mes;
            var mesesMaior = maior.ano * 12 + maior.mes;
            if (mesesPeriodo > mesesMaior) {
                maior.ano = turma.periodos[i].ano;
                maior.mes = turma.periodos[i].mes;
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
        var turmaAtual = $scope.turmas[$scope.turmaAtual - 1];
        if (confirm('Tem certeza que deseja remover o período de ' + periodo.mes + '/' + periodo.ano + '?')) {
            var indice = turmaAtual.periodos.indexOf(periodo);
            turmaAtual.periodos.splice(indice, 1);
        }
    };

    $scope.removerOferta = function(oferta, periodo) {
        if (confirm('Tem certeza que deseja remover a oferta do componente "' + oferta.nome + '"?')) {
            var indice = periodo.ofertas.indexOf(oferta);
            periodo.ofertas.splice(indice, 1);
        }
    };

    $scope.adicionarNovasOfertas = function(periodo) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'adicionarNovasOfertas.html',
            controller: 'ModalInstanceCtrl',
            size: 'lg',
            resolve: {
                componentesDisponiveis: function() {
                    return filtrarComponentesQueJaEstaoNoPeriodo(todasComponentes, periodo);
                },
                mes: function() { return periodo.mes; },
                ano: function() { return periodo.ano; }
            }
        });

        modalInstance.result.then(function(componentesSelecionados) {
            //~ console.log('componentes selecionados = ' + componentesSelecionados);
            for (var i in componentesSelecionados) {

                componentesSelecionados[i].selecionado = false;

                var ofertaASerAdicionada = angular.copy(novaOferta);
                ofertaASerAdicionada.componente = componentesSelecionados[i];
                ofertaASerAdicionada.ano = periodo.ano;
                ofertaASerAdicionada.mes = periodo.mes;
                ofertaASerAdicionada.nome = componentesSelecionados[i].nome + ' ' + periodo.mes + '/' + periodo.ano;
                ofertaASerAdicionada.turma = $scope.turmaAtual,
                ofertaASerAdicionada.idHtml = $scope.turmaAtual + '_' + 
                        componentesSelecionados[i].id + '_' +
                        periodo.ano + '_' +
                        periodo.mes;

                periodo.ofertas.push(ofertaASerAdicionada);

            }
        });
    };

    function filtrarComponentesQueJaEstaoNoPeriodo(componentes, periodo) {
        var copiaComponentes = componentes.slice();

        for (i in periodo.ofertas) {
            for (j in copiaComponentes) {
                if (copiaComponentes[j].id == periodo.ofertas[i].componente.id) {
                    copiaComponentes.splice(j, 1);
                    break;
                }
            }
        }

//        console.log('Retornando ' + JSON.stringify(copiaComponentes));
        return copiaComponentes;
    }

    $scope.associarDocentesETutores = function(oferta) {
        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'associarDocentesETutores.html',
            controller: 'associarDocentesController',
            size: 'lg',
            resolve: {
                ofertaSelecionada: function() { return oferta; },
                docentesDisponiveis: function() {
                    return filtrarDocentesQueJaEstaoAssociados(todosDocentes, oferta);
                },
                tutoresDisponiveis: function() {
                    return filtrarTutoresQueJaEstaoAssociados(todosTutores, oferta);
                },
                mes: function() { return oferta.mes; },
                ano: function() { return oferta.ano; }
            }
        });

        modalInstance.result.then(function(resposta) {
            //~ console.log('componentes selecionados = ' + componentesSelecionados);
            var docentesSelecionados = resposta.docentesSelecionados;
            var tutoresSelecionados = resposta.tutoresSelecionados;
            for (var i in docentesSelecionados) {
                docentesSelecionados[i].selecionado = false;
                oferta.docentes.push(docentesSelecionados[i]);
            }
            for (var i in tutoresSelecionados) {
                tutoresSelecionados[i].selecionado = false;
                oferta.tutores.push(tutoresSelecionados[i]);
            }
        });
        
    }

    function filtrarDocentesQueJaEstaoAssociados(todosDocentes, oferta) {
        var docentesAssociados = oferta.docentes;
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

    function filtrarTutoresQueJaEstaoAssociados(todosTutores, oferta) {
        var tutoresAssociados = oferta.tutores;
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

    $scope.desassociarDocente = function(docente, oferta) {
        if (confirm('Tem certeza que deseja desassociar o docente "' + docente.nome + ' ' + docente.sobrenome + '" desta oferta?')) {
            var indiceDoDocente = oferta.docentes.indexOf(docente);
            oferta.docentes.splice(indiceDoDocente, 1);
        }
    }

    $scope.desassociarTutor = function(tutor, oferta) {
        if (confirm('Tem certeza que deseja desassociar o tutor "' + tutor.nome + ' ' + tutor.sobrenome + '" desta oferta?')) {
            var indiceDoTutor = oferta.tutores.indexOf(tutor);
            oferta.tutores.splice(indiceDoTutor, 1);
        }
    }

    $scope.adicionarInfoMoodle = function(oferta) {

        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'adicionarInfoMoodle.html',
            controller: 'adicionarInfoMoodleController',
            size: 'lg',
            resolve: {
                ofertaSelecionada: function() { return oferta; },
                mes: function() { return oferta.mes; },
                ano: function() { return oferta.ano; }
            }
        });

        modalInstance.result.then(function(resposta) {
            oferta.linkMoodle = resposta.linkMoodle;
            oferta.codigoMoodle = resposta.codigoMoodle;
        });

    }

    $scope.gerarListaMoodle = function(oferta) {
        if (oferta.codigoMoodle == '') {
            alert('ATENÇÃO: O código da sala da oferta no Moodle não está preenchido.\n' +
                    'O arquivo gerado não conterá a sala em que os alunos devem ser inscritos.');
        }
        var urlAtual = window.location.href;
        var urlExportador = urlAtual.replace(/oferta\/gerenciar/, 'exportador\/listaCadastroMoodle&ofertaId=' + oferta.id);
        window.location.href = urlExportador;
    }
    
    $scope.gerarListaMoodleTodasOfertas = function() {
        var urlAtual = window.location.href;
        var urlExportador = urlAtual.replace(/oferta\/gerenciar/, 'exportador\/listaAlunosTodasOfertas');
        window.location.href = urlExportador;
    }

    $scope.alterarPeriodoDa = function(oferta) {

        var modalInstance = $uibModal.open({
            animation: true,
            templateUrl: 'alterarPeriodoDaOferta.html',
            controller: 'alterarPeriodoDaOfertaController',
            size: 'lg',
            resolve: {
                ofertaSelecionada: function() { return oferta; },
                mes: function() { return oferta.mes; },
                ano: function() { return oferta.ano; }
            }
        });

        modalInstance.result.then(function(resposta) {
            Oferta.alterarPeriodo({
                id: oferta.id,
                ano: resposta.ano,
                mes: resposta.mes
            }, function() {
                window.location.reload(false);  
            });
        });

    }

    $scope.duplicarTurma = function(turma) {
        if (confirm('Tem certeza que deseja duplicar a turma ' + turma + ' na turma atual (' + $scope.turmaAtual + ')?\n\
Todas as ofertas da turma atual serão sobrescritas.')) {

            $scope.turmas[$scope.turmaAtual - 1] = angular.copy($scope.turmas[turma - 1]);

            var turmaAtual = $scope.turmas[$scope.turmaAtual - 1];
            for (var i in turmaAtual.periodos) {
                var periodo = turmaAtual.periodos[i];
                periodo.podeSerDeletado = true;
                for (var j in periodo.ofertas) {
                    var oferta = periodo.ofertas[j];
                    oferta.podeSerDeletada = true;
                    oferta.linkMoodle = null;
                    oferta.codigoMoodle = null;
//                    oferta.docentes = [];
//                    oferta.tutores = [];
                    oferta.idHtml = $scope.turmaAtual + '_' + 
                        oferta.componente.id + '_' +
                        periodo.ano + '_' +
                        periodo.mes;
                }
            }
        }
    }

}]);

// Diálogo para adicionar uma nova oferta a um período
angular.module('gerenciarOfertasApp').controller('ModalInstanceCtrl', function ($scope, $uibModalInstance, componentesDisponiveis, mes, ano) {
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

// Diálogo para adicionar um novo período de ofertas
angular.module('gerenciarOfertasApp').controller('anoMesController', function ($scope, $uibModalInstance, proximoPeriodo) {
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

// Diálogo para associar docentes e tutores a uma oferta
angular.module('gerenciarOfertasApp').controller('associarDocentesController', function ($scope, $uibModalInstance, ofertaSelecionada, docentesDisponiveis, tutoresDisponiveis, mes, ano) {
    $scope.componenteSelecionada = ofertaSelecionada;
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

// Diálogo para adicionar informações da oferta relativas ao Moodle
angular.module('gerenciarOfertasApp').controller('adicionarInfoMoodleController', function ($scope, $uibModalInstance, ofertaSelecionada, mes, ano) {
    $scope.componenteSelecionada = ofertaSelecionada;
    $scope.linkMoodle = ofertaSelecionada.linkMoodle;
    $scope.codigoMoodle = ofertaSelecionada.codigoMoodle;
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

// Diálogo para alterar o período de uma oferta
angular.module('gerenciarOfertasApp').controller('alterarPeriodoDaOfertaController', function ($scope, $uibModalInstance, ofertaSelecionada, ano, mes) {
    $scope.oferta = ofertaSelecionada;
    $scope.ano = ano;
    $scope.mes = mes;

    $scope.ok = function () {
        var resposta = {ano: $scope.ano, mes: $scope.mes};
        $uibModalInstance.close(resposta);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
});
