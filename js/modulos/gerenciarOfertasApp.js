//INCLUDE /js/bower_components/angular/angular.min
//INCLUDE /js/ui-bootstrap-tpls-1.3.3.min
//INCLUDE /js/modulos/servicos

/**
 * Controla o gerenciamento de ofertas de componentes curriculares por período.
 * 
 * Usado na view oferta/gerenciar.
 */
angular.module('gerenciarOfertasApp', ['ui.bootstrap', 'servicos']);

angular.module('gerenciarOfertasApp').controller("controlador", ["$scope", "$uibModal", "Componente", "Oferta", "Docente", "Tutor",
    function ($scope, $uibModal, Componente, Oferta, Docente, Tutor) {

        // Armazena os ids das ofertas que deverão ser deletadas
        $scope.idsASeremDeletados = [];
        $scope.hoje = new Date();
        $scope.mostrarOfertasPassadas = false;
        var todasComponentes = [];
        var todosDocentes = [];
        var todosTutores = [];

        var novaOferta = {
            // Campos que deverão ser preenchidos
            'componente': null,
            'ano': 0,
            'mes': 0,
            'nome': '',
            // Campos que não precisam ser preenchidos
            'podeSerDeletada': true,
            'bloqueada': false,
            'dataInicio': null,
            'linkMoodle': null,
            'codigoMoodle': null,
            'docentes': [],
            'tutores': [],
            'inscricoes': []
        };

        $scope.periodos = Oferta.todasPorPeriodo(function () {
            todasComponentes = Componente.query();
            todosDocentes = Docente.query();
            todosTutores = Tutor.query();
        });

        $scope.ativarOfertasPassadas = function() {
            $scope.mostrarOfertasPassadas = !$scope.mostrarOfertasPassadas;
        }

        $scope.deveMostrarPeriodo = function(periodo) {
            return $scope.mostrarOfertasPassadas || ehHojeOuDepoisDeHoje(periodo);
        }

        function ehHojeOuDepoisDeHoje(periodo) {
            return periodo.ano * 12 + periodo.mes >= $scope.hoje.getFullYear() * 12 + ($scope.hoje.getMonth() + 1);
        }

        $scope.adicionarNovoPeriodo = function () {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'adicionarNovoPeriodo.html',
                controller: 'anoMesController',
                size: 'lg',
                resolve: {
                    proximoPeriodo: proximoPeriodoDisponivel()
                }
            });

            modalInstance.result.then(function (anomes) {
                if (!periodoExiste(anomes.ano, anomes.mes)) {
                    $scope.periodos.push({
                        'ofertas': [],
                        'ano': anomes.ano,
                        'mes': anomes.mes,
                        'podeSerDeletado': true
                    });
                }
            });
        }

        function periodoExiste(ano, mes) {
            for (var i in $scope.periodos)
                if ($scope.periodos[i].ano == ano && $scope.periodos[i].mes == mes)
                    return true;
            return false;
        }

        function proximoPeriodoDisponivel() {
            if ($scope.periodos.length == 0) {
                var hoje = new Date();
                return {'ano': hoje.getFullYear(), 'mes': hoje.getMonth() + 1};
            }

            return ultimoPeriodoMaisUmMes();
        }

        function ultimoPeriodoMaisUmMes() {
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

        $scope.removerPeriodo = function (periodo) {
            if (confirm('Tem certeza que deseja remover o período de ' + periodo.mes + '/' + periodo.ano + '?')) {
                removerOfertasDoPeriodo(periodo);
                var indice = $scope.periodos.indexOf(periodo);
                $scope.periodos.splice(indice, 1);
            }
        };

        function removerOfertasDoPeriodo(periodo) {
            for (var i in periodo.ofertas) {
                if (periodo.ofertas[i].hasOwnProperty('id')) {
                    $scope.idsASeremDeletados.push(periodo.ofertas[i].id);
                }
            }
        }

        $scope.removerOferta = function (oferta, periodo) {
            if (confirm('Tem certeza que deseja remover a oferta do componente "' + oferta.nome + '"?')) {
                var indice = periodo.ofertas.indexOf(oferta);
                periodo.ofertas.splice(indice, 1);
                if (oferta.hasOwnProperty('id')) {
                    $scope.idsASeremDeletados.push(oferta.id);
                }
            }
        };

        $scope.adicionarNovasOfertas = function (periodo) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'adicionarNovasOfertas.html',
                controller: 'ModalInstanceCtrl',
                size: 'lg',
                resolve: {
                    componentesDisponiveis: function () {
                        return filtrarComponentesQueJaEstaoNoPeriodo(todasComponentes, periodo);
                    },
                    mes: function () {
                        return periodo.mes;
                    },
                    ano: function () {
                        return periodo.ano;
                    }
                }
            });

            modalInstance.result.then(function (componentesSelecionados) {
                //~ console.log('componentes selecionados = ' + componentesSelecionados);
                for (var i in componentesSelecionados) {

                    componentesSelecionados[i].selecionado = false;

                    var ofertaASerAdicionada = angular.copy(novaOferta);
                    ofertaASerAdicionada.componente = componentesSelecionados[i];
                    ofertaASerAdicionada.ano = periodo.ano;
                    ofertaASerAdicionada.mes = periodo.mes;
                    ofertaASerAdicionada.nome = componentesSelecionados[i].nome + ' ' + periodo.mes + '/' + periodo.ano;

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

            return copiaComponentes;
        }

        $scope.associarDocentesETutores = function (oferta) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'associarDocentesETutores.html',
                controller: 'associarDocentesController',
                size: 'lg',
                resolve: {
                    ofertaSelecionada: function () {
                        return oferta;
                    },
                    docentesDisponiveis: function () {
                        return filtrarPessoasQueJaEstaoAssociadas(todosDocentes, oferta.docentes);
                    },
                    tutoresDisponiveis: function () {
                        return filtrarPessoasQueJaEstaoAssociadas(todosTutores, oferta.tutores);
                    },
                    mes: function () {
                        return oferta.mes;
                    },
                    ano: function () {
                        return oferta.ano;
                    }
                }
            });

            modalInstance.result.then(function (resposta) {
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

        function filtrarPessoasQueJaEstaoAssociadas(todasPessoas, pessoasDaOferta) {
            var copiaTodasPessoas = todasPessoas.slice();

            for (i in pessoasDaOferta) {
                for (j in copiaTodasPessoas) {
                    if (copiaTodasPessoas[j].cpf == pessoasDaOferta[i].cpf) {
                        copiaTodasPessoas.splice(j, 1);
                        break;
                    }
                }
            }

            return copiaTodasPessoas;
        }

        $scope.desassociarDocente = function (docente, oferta) {
            if (confirm('Tem certeza que deseja desassociar o docente "' + docente.nomeCompleto + '" desta oferta?')) {
                var indiceDoDocente = oferta.docentes.indexOf(docente);
                oferta.docentes.splice(indiceDoDocente, 1);
            }
        }

        $scope.desassociarTutor = function (tutor, oferta) {
            if (confirm('Tem certeza que deseja desassociar o tutor "' + tutor.nomeCompleto + '" desta oferta?')) {
                var indiceDoTutor = oferta.tutores.indexOf(tutor);
                oferta.tutores.splice(indiceDoTutor, 1);
            }
        }

        $scope.adicionarInfoMoodle = function (oferta) {

            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'adicionarInfoMoodle.html',
                controller: 'adicionarInfoMoodleController',
                size: 'lg',
                resolve: {
                    ofertaSelecionada: function () {
                        return oferta;
                    },
                    mes: function () {
                        return oferta.mes;
                    },
                    ano: function () {
                        return oferta.ano;
                    }
                }
            });

            modalInstance.result.then(function (resposta) {
                oferta.dataInicio = resposta.dataInicio;
                oferta.linkMoodle = resposta.linkMoodle;
                oferta.codigoMoodle = resposta.codigoMoodle;
            });

        }

        $scope.gerarListaMoodle = function (oferta) {
            if (oferta.codigoMoodle == '') {
                alert('ATENÇÃO: O código da sala da oferta no Moodle não está preenchido.\n' +
                        'O arquivo gerado não conterá a sala em que os alunos devem ser inscritos.');
            }
            var urlAtual = window.location.href;
            var urlExportador = urlAtual.replace(/oferta\/gerenciar/, 'exportador\/listaCadastroMoodle&ofertaId=' + oferta.id);
            window.location.href = urlExportador;
        }

        $scope.gerarListaMoodleTodasOfertas = function () {
            var urlAtual = window.location.href;
            var urlExportador = urlAtual.replace(/oferta\/gerenciar/, 'exportador\/listaAlunosTodasOfertas');
            window.location.href = urlExportador;
        }

        $scope.alterarPeriodoDa = function (oferta) {

            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'alterarPeriodoDaOferta.html',
                controller: 'alterarPeriodoDaOfertaController',
                size: 'lg',
                resolve: {
                    ofertaSelecionada: function () {
                        return oferta;
                    },
                    mes: function () {
                        return oferta.mes;
                    },
                    ano: function () {
                        return oferta.ano;
                    }
                }
            });

            modalInstance.result.then(function (resposta) {
                if (ofertaDoComponenteJaExisteNoPeriodo(oferta.componente.id, resposta.ano, resposta.mes)) {
                    alert('Uma oferta desse componente já existe nesse período!');
                    return;
                }
                Oferta.alterarPeriodo({
                    id: oferta.id,
                    ano: resposta.ano,
                    mes: resposta.mes
                }, function () {
                    window.location.reload(false);
                });
            });

        }

        function ofertaDoComponenteJaExisteNoPeriodo(componenteId, ano, mes) {
            for (var i in $scope.periodos) {
                if ($scope.periodos[i].ano == ano && $scope.periodos[i].mes == mes) {
                    for (var j in $scope.periodos[i].ofertas) {
                        if ($scope.periodos[i].ofertas[j].componente.id == componenteId) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }

        $scope.serializar = function (oferta) {
            var json = angular.toJson(oferta);
            var jsonEncodado = encodeURIComponent(json);
            return jsonEncodado;
        }

        $scope.cargaHorariaDoPeriodo = function (periodo) {
            return periodo.ofertas.reduce(function (cargaHorariaTotal, oferta) {
                return cargaHorariaTotal + oferta.componente.cargaHoraria;
            }, 0);
        }

    }]);

// Diálogo para adicionar uma nova oferta a um período
angular.module('gerenciarOfertasApp').controller('ModalInstanceCtrl', function ($scope, $uibModalInstance, componentesDisponiveis, mes, ano) {
    $scope.componentesDisponiveis = componentesDisponiveis;
    $scope.ano = ano;
    $scope.mes = mes;

    $scope.ok = function () {
        var componentesSelecionadas = $scope.componentesDisponiveis.filter(function (componente) {
            return componente.selecionado == true;
        });
        $uibModalInstance.close(componentesSelecionadas);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };

    $scope.selecionar = function (componente) {
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
        var docentesSelecionados = $scope.docentesDisponiveis.filter(function (docente) {
            return docente.selecionado == true;
        });
        var tutoresSelecionados = $scope.tutoresDisponiveis.filter(function (tutor) {
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

    $scope.selecionar = function (pessoa) {
        pessoa.selecionado = !pessoa.selecionado;
    };
});

// Diálogo para adicionar informações da oferta relativas ao Moodle
angular.module('gerenciarOfertasApp').controller('adicionarInfoMoodleController', function ($scope, $uibModalInstance, ofertaSelecionada, mes, ano) {
    $scope.componenteSelecionada = ofertaSelecionada;
    $scope.dataInicio = ofertaSelecionada.dataInicio;
    $scope.linkMoodle = ofertaSelecionada.linkMoodle;
    $scope.codigoMoodle = ofertaSelecionada.codigoMoodle;
    $scope.ano = ano;
    $scope.mes = mes;

    $scope.ok = function () {
        $uibModalInstance.close({
            "dataInicio": $scope.dataInicio,
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
