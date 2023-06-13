/**
 * Esta diretiva apresenta as tabelas com ofertas, divididas por períodos, em 
 * que um usuário pode se inscrever. Utilizada nas views:
 * - aluno/inscricao
 * - admin/visualizarInscricoes
 * - admin/editarInscricoesInscricao
 * 
 * Recebe como parâmetros os atributos:
 * - periodos - objeto que contém todas as ofertas do sistema organizadas por
 *              períodos, juntamente com as infromaçẽos de em quais ofertas o
 *              aluno está inscrito
 * - habilitacoes - objeto que contém as habilitações escolhidas pelo aluno em
 *                  questão
 * - nivelDeEdicao => "visualizacao"|"aluno"|"admin
 *       O valor "visualizacao" apenas apresenta as informações. O valor "aluno"
 *       deixa as ofertas de períodos abertos serem selecionadas, e o valor
 *       "admin" permite que quaisquer ofertas sejam selecionados, mesmo que já
 *       tenham fechado.
 */
var modulo = angular.module('tabelaDeInscricaoEmOfertas', ['contadorDeComponentes']);

modulo.directive('tabelaDeInscricaoEmOfertas', ['ContadorDeComponentes', function(ContadorDeComponentes) {
    return {
        restrict: 'E',
        replace: 'true',
        templateUrl: 'js/diretivas/tabelaDeInscricaoEmOfertas/templates/index.html',
        scope: {
            'periodos': '=',
            'habilitacoes': '=',
            'nivelDeEdicao': '@',
            'mesAtual': '@',
            'anoAtual': '@',
            'mostrarOfertasPassadas': '@',
            'constantes': '=',
            'tipoDeCurso': '@',
            'selecaoParaCertificados': '@',
            'houveMudanca': '&',
        },
        link: function (scope) {

            scope.ofertaProjetoIntegrador = null;
            scope.periodos.$promise.then(processarOfertaDoProjetoIntegrador);

            function processarOfertaDoProjetoIntegrador(periodos) {
                periodos.forEach(function(periodo) {
                    periodo.ofertas.forEach(function(oferta) {
                        if (periodo.projetoIntegrador) {
                            scope.ofertaProjetoIntegrador = oferta;
                            return;
                        }
                    });
                });
            }

            verificarSeNivelDeEdicaoEhValido();

            function verificarSeNivelDeEdicaoEhValido() {
                var niveisDeEdicaoPossiveis = ['visualizacao', 'aluno', 'admin'];
                if (niveisDeEdicaoPossiveis.indexOf(scope.nivelDeEdicao) == -1) {
                    var mensagemDeErro = 'ERRO: Nível de edição atribuído à diretiva tabela-de-inscricao-em-ofertas é inválido: ' + scope.nivelDeEdicao;
                    alert(mensagemDeErro);
                    throw new Error(mensagemDeErro);
                }
            }

            function excedeuNumeroDeComponentes() {
                const ofertasInscritas = recuperarOfertasInscritas();
                const idsHabilitacoes = scope.habilitacoes.map(function(habilitacao) {
                    return habilitacao.id;
                });
                const cargaHoraria = ContadorDeComponentes.cargaHoraria(ofertasInscritas, idsHabilitacoes, scope.constantes);

                if (scope.tipoDeCurso == 0) {
                    return cargaHoraria.total > scope.constantes.NUMERO_DE_COMPONENTES_OBRIGATORIOAS_PARA_EXTENSAO_E_APERFEICOAMENTO + scope.constantes.NUMERO_DE_COMPONENTES_LIVRES_PARA_EXTENSAO;
                } else if (scope.tipoDeCurso == 1) {
                    return cargaHoraria.total > scope.constantes.NUMERO_DE_COMPONENTES_OBRIGATORIOAS_PARA_EXTENSAO_E_APERFEICOAMENTO + scope.constantes.NUMERO_DE_COMPONENTES_LIVRES_PARA_APERFEICOAMENTO;
                }
                for (let i = 0; i < scope.habilitacoes.length; i++) {
                    if (cargaHoraria.totalHabilitacoes[i + 1] > scope.constantes.NUMERO_MAXIMO_DE_COMPONENTES_POR_HABILITACAO[i + 1]) {
                        return true;
                    }
                }
                return false;
            }

            function recuperarOfertasInscritas() {
                const ofertasInscritas = [];

                scope.periodos.forEach(function(periodo) {
                    periodo.ofertas.forEach(function(oferta) {
                        if (oferta.selecionada) {
                            ofertasInscritas.push(oferta);
                        }
                    });
                });

                return ofertasInscritas;
            };

            scope.selecionar = function (oferta, habilitacao, clicouNaCheckbox) {
                if (!podeSelecionarOferta(oferta)) return;
                if (scope.houveMudanca) scope.houveMudanca();

                // Alunos de extensão e aperfeiçoamento
                if (!habilitacao) {
                    if (clicouNaCheckbox) {
                        oferta.selecionada = !oferta.selecionada;
                    }
                    oferta.selecionada = !oferta.selecionada;
                    return;
                }

                if (clicouNaCheckbox) {
                    oferta.selecionadaParaHabilitacoes[ habilitacao.id ] = !oferta.selecionadaParaHabilitacoes[ habilitacao.id ];
                }
                if (ehOfertaObrigatoria(oferta)) {
                    scope.habilitacoes.forEach(function(h) {
                        oferta.selecionadaParaHabilitacoes[ h.id ] = !oferta.selecionadaParaHabilitacoes[ h.id ];
                    });
                    oferta.selecionada = haAlgumaHabilitacaoSelecionadaParaOferta(oferta);
                } else {
                    oferta.selecionadaParaHabilitacoes[ habilitacao.id ] = !oferta.selecionadaParaHabilitacoes[ habilitacao.id ];
                    oferta.selecionada = haAlgumaHabilitacaoSelecionadaParaOferta(oferta);
                }
                oferta.selecionada = haAlgumaHabilitacaoSelecionadaParaOferta(oferta);
            }

            scope.selecionarComponente = function(oferta) {
                if (!podeSelecionarOferta(oferta)) return;
                if (scope.houveMudanca) scope.houveMudanca();

                // Alunos de extensão e aperfeiçoamento
                if (scope.habilitacoes.length == 0) {
                    oferta.selecionada = !oferta.selecionada;
                    return;
                }

                const proximoEstadoDaOferta = !oferta.selecionada;
                scope.habilitacoes.forEach(function(habilitacao) {
                    oferta.selecionadaParaHabilitacoes[ habilitacao.id ] = proximoEstadoDaOferta;
                })
                oferta.selecionada = haAlgumaHabilitacaoSelecionadaParaOferta(oferta);
                oferta.selecionada = haAlgumaHabilitacaoSelecionadaParaOferta(oferta);                
            }

            function ehOfertaObrigatoria(oferta) {
                return oferta.componente.prioridades[0].prioridade === 0;
            }

            function haAlgumaHabilitacaoSelecionadaParaOferta(oferta) {
                for (i in oferta.selecionadaParaHabilitacoes) {
                    if (oferta.selecionadaParaHabilitacoes[i]) return true;
                }
                return false;
            }

            scope.mudarEstiloSeOPeriodoEstiverBloqueado = function (periodo) {
                if (scope.selecaoParaCertificados === 'true') return false;
                return periodo.bloqueado && scope.nivelDeEdicao != 'visualizacao';
            }

            function podeSelecionarOferta(oferta) {
                if (scope.nivelDeEdicao == 'visualizacao') return false;
                if (scope.nivelDeEdicao == 'aluno') return !oferta.bloqueada;
                if (scope.nivelDeEdicao == 'admin') return true;
                return false;
            }

            scope.podeCriarCheckbox = function () {
                if (scope.nivelDeEdicao == 'visualizacao') return false;
                if (scope.nivelDeEdicao == 'aluno') return true;
                if (scope.nivelDeEdicao == 'admin') return true;
                return false;
            }

            scope.podeMostrarCheckbox = function (oferta) {
                if (!oferta) return false;
                if (scope.nivelDeEdicao == 'visualizacao') return false;
                if (scope.nivelDeEdicao == 'aluno') return !oferta.bloqueada;
                if (scope.nivelDeEdicao == 'admin') return true;
                return false;
            }

            scope.podeMostrarTick = function (oferta) {
                if (!oferta || !oferta.selecionada) return false;

                if (scope.nivelDeEdicao == 'visualizacao') return true;
                if (scope.nivelDeEdicao == 'aluno') return oferta.bloqueada;
                if (scope.nivelDeEdicao == 'admin') return false;

                return false;
            }

            scope.deveMostrarPeriodo = function(periodo) {
                return scope.mostrarOfertasPassadas === 'true' || ehHojeOuDepoisDeHoje(periodo);
            }

            function ehHojeOuDepoisDeHoje(periodo) {
                return periodo.ano * 12 + periodo.mes >= parseInt(scope.anoAtual) * 12 + parseInt(scope.mesAtual);
            }

            scope.recuperarLetraParaHabilitacao = function(oferta, habilitacaoId) {
                if (!oferta) return '-';
                let prioridade = oferta.componente.prioridades.find(function(prioridade) {
                    return prioridade.id == habilitacaoId;
                });
                return prioridade ? prioridade.letra : '-';
            };

            scope.recuperarCor = function(oferta, habilitacaoId) {
                if (!oferta) return null;
                let prioridade = oferta.componente.prioridades.find(function(prioridade) {
                    return prioridade.id == habilitacaoId;
                });
                if (oferta.selecionadaParaHabilitacoes[habilitacaoId]) {
                    if (prioridade.cor) {
                        return LightenDarkenColor(prioridade.cor, -20);
                    } else {
                        // Verde um pouco mais escuro
                        return '#c9dd81';
                    }
                }
                return prioridade.cor || null;
            };

            scope.recuperarFonte = function(oferta, habilitacaoId) {
                if (!oferta) return null;
                return oferta.selecionadaParaHabilitacoes[habilitacaoId] ? 'bold' : null;
            }

            // https://css-tricks.com/snippets/javascript/lighten-darken-color/
            function LightenDarkenColor(color, amount) {
                var usePound = false;

                if (color[0] == "#") {
                    color = color.slice(1);
                    usePound = true;
                }

                var num = parseInt(color,16);
                var r = (num >> 16) + amount;

                if (r > 255) r = 255;
                else if  (r < 0) r = 0;

                var b = ((num >> 8) & 0x00FF) + amount;

                if (b > 255) b = 255;
                else if  (b < 0) b = 0;

                var g = (num & 0x0000FF) + amount;

                if (g > 255) g = 255;
                else if (g < 0) g = 0;

                return (usePound?"#":"") + (g | (b << 8) | (r << 16)).toString(16);
            }

            function recuperarOfertaSelecionadaEmOutroPeriodo(oferta) {
                for (i in scope.periodos) {
                    if (!scope.periodos[i].ofertas) continue;
                    const ofertaSelecionadaEmOutroPeriodo = scope.periodos[i].ofertas.find(function(outraOferta) {
                        return outraOferta.id != oferta.id
                            && outraOferta.componente.id === oferta.componente.id
                            && outraOferta.selecionada;
                    });
                    if (ofertaSelecionadaEmOutroPeriodo) {
                        return ofertaSelecionadaEmOutroPeriodo;
                    }
                }
                return null;
            }

            scope.componenteEstaSelecionadoEmOutroPeriodo = function(oferta) {
                return recuperarOfertaSelecionadaEmOutroPeriodo(oferta) != null;
            };

            scope.periodoEmQueComponenteEstaSelecionado = function(oferta) {
                const ofertaSelecionadaEmOutroPeriodo = recuperarOfertaSelecionadaEmOutroPeriodo(oferta);
                if (ofertaSelecionadaEmOutroPeriodo) {
                    return ofertaSelecionadaEmOutroPeriodo.mes + '/' + ofertaSelecionadaEmOutroPeriodo.ano;
                }
            }

            scope.componenteEhObrigatorio = function(oferta) {
                if (!scope.constantes || !scope.constantes.COMPONENTES_OBRIGATORIAS_PARA_ESPECIALIZACAO) return;
                return scope.tipoDeCurso == 2
                    ? scope.constantes.COMPONENTES_OBRIGATORIAS_PARA_ESPECIALIZACAO.includes(oferta.componente.id)
                    : scope.constantes.COMPONENTES_OBRIGATORIAS_PARA_EXTENSAO_E_APERFEICOAMENTO.includes(oferta.componente.id);
            }

        }
    };
}]);
