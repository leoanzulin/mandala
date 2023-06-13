<?php

class ValidadorDeInscricoesEmOfertas
{

    private $inscricao;
    private $habilitacoes;
    private $idsDasOfertasEHabilitacoesASeremInscritas;
    private $mensagensDeErro;

    public function __construct(Inscricao $inscricao, $habilitacoes, $idsDasOfertasEHabilitacoesASeremInscritas)
    {
        $this->inscricao = $inscricao;
        $this->habilitacoes = $habilitacoes;
        $this->idsDasOfertasEHabilitacoesASeremInscritas = $idsDasOfertasEHabilitacoesASeremInscritas;
    }

    // Este código de validação repete o código de recuperarInscricoesEmOfertas, lembrar de alterar aqui
    // se lá for alterado
    public function validar()
    {
        // Segundo e-mail do Mill de 05/06/2020, o aluno deve poder salvar sua trilha do jeito que estiver
        return true;

        $componentesASeremInscritos = $this->recuperarComponentesEHabilitacoesASeremInscritos();
        $componentesComPrioridades = $this->recuperarComponentesComPrioridades();

        $contagem = [];
        $problemas = [];

        if ($this->inscricao->ehAlunoDeEspecializacao()) {

            $contagem = $this->contarComponentesPorHabilitacao($componentesASeremInscritos, $componentesComPrioridades);

            foreach ($this->habilitacoes as $i => $habilitacao) {
                if (!$this->validarNumeroMinimoDeComponentesObrigatoriosPorHabilitacao($contagem, $i)) {
                    $problemas[] = 'O número mínimo de componentes obrigatórios para a habilitação ' . ($i + 1) . ' não foi atendido';
                }
                if (!$this->validarNumeroMinimoDeComponentesOptativosPorHabilitacao($contagem, $i)) {
                    $problemas[] = 'O número mínimo de componentes optativos para a habilitação ' . ($i + 1) . ' não foi atendido';
                }
                if (!$this->validarNumeroMinimoDeComponentesNovas($contagem, $i)) {
                    $problemas[] = 'O número mínimo de componentes novas (' . Constantes::NUMERO_MINIMO_DE_COMPONENTES_NOVAS_POR_HABILITACAO . ') para a habilitação ' . ($i + 1) . ' não foi atendido';
                }
                if (!$this->validarNumeroMinimoDeComponentesTotais($contagem, $i)) {
                    $problemas[] = 'O número mínimo (' . Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES($i + 1) . ') de componentes totais para a habilitação ' . ($i + 1) . ' não foi atendido';
                }
            }
            if (!$this->validarNumeroMinimoDeComponentesTotaisDeTodasHabilitacoes($contagem)) {
                $problemas[] = 'O número mínimo (' . Constantes::NUMERO_MINIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES(count($this->habilitacoes)) . ') de componentes distintos somando todas as habilitações não foi atendido';
            }

        } else {

            $contagem = $this->contarComponentesSemHabilitacao($componentesASeremInscritos);

            $idsComponentesObrigatorios = Constantes::COMPONENTES_OBRIGATORIAS_PARA_EXTENSAO_E_APERFEICOAMENTO();
            $idsComponentesASeremInscritos = array_map(function($componente) {
                return $componente['componente_id'];
            }, $componentesASeremInscritos);

            $componentesObrigatoriosEstaoSelecionados = true;
            foreach ($idsComponentesObrigatorios as $idComponenteObrigatorio) {
                if (!in_array($idComponenteObrigatorio, $idsComponentesASeremInscritos)) {
                    $componentesObrigatoriosEstaoSelecionados = false;
                }
            }
            if (!$componentesObrigatoriosEstaoSelecionados) {
                $problemas[] = Constantes::STRING_COMPONENTES_OBRIGATORIOS_PARA_EXTENSAO_E_APERFEICOAMENTO;
            }
            $numeroDeComponentesLivres = $this->inscricao->recuperarNumeroDeComponentesLivresQuePodeSeInscrever();
            if ($contagem['livres'] < $numeroDeComponentesLivres) {
                $problemas[] = "Você deve selecionar pelo menos {$numeroDeComponentesLivres} componentes livres";
            }

        }

        if (!empty($problemas)) {
            $this->mensagensDeErro = $problemas;
            return false;
        }

        return true;
    }

    private function recuperarComponentesEHabilitacoesASeremInscritos()
    {
        $idsOfertas = array_keys($this->idsDasOfertasEHabilitacoesASeremInscritas);

        $idsOfertasString = implode(",", $idsOfertas);
        $sql = "SELECT cc.id AS componente_id, o.id AS oferta_id FROM oferta o JOIN componente_curricular cc ON o.componente_curricular_id = cc.id WHERE o.id IN ({$idsOfertasString}) ORDER BY o.ano, o.mes";
        $resultados = Yii::app()->db->createCommand($sql)->queryAll();
        $componentesASeremInscritos = array_map(function($resultado) {
            return [
                'componente_id' => $resultado['componente_id'],
                'oferta_id' => $resultado['oferta_id']
            ];
        }, $resultados);

        foreach ($componentesASeremInscritos as &$componenteASerInscrito) {
            $componenteASerInscrito['inscricoes_habilitacoes'] = $this->idsDasOfertasEHabilitacoesASeremInscritas[ $componenteASerInscrito['oferta_id'] ];
        }

        $componentesASeremInscritos = $this->removerComponentesDuplicados($componentesASeremInscritos);

        return $componentesASeremInscritos;
    }

    private function removerComponentesDuplicados($componentes)
    {
        $idsComponentesJaVistos = [];
        $componentesSemRepeticao = [];

        foreach ($componentes as $componente) {
            if (!empty($idsComponentesJaVistos[$componente['componente_id']])) {
                continue;
            }
            $idsComponentesJaVistos[$componente['componente_id']] = true;
            $componentesSemRepeticao[] = $componente;
        }

        return $componentesSemRepeticao;
    }

    private function recuperarComponentesComPrioridades()
    {
        $sql = "SELECT cc.id, ARRAY_AGG(ch.habilitacao_id || '-' || ch.prioridade) AS prioridades
        FROM componente_curricular cc JOIN componente_habilitacao ch ON ch.componente_curricular_id = cc.id
        GROUP BY cc.id";
        $resultados = Yii::app()->db->createCommand($sql)->queryAll();
        $componentesComPrioridades = [];
        foreach ($resultados as $resultado) {
            $componentesComPrioridades[ $resultado['id'] ] = $this->processarPrioridades($resultado['prioridades']);
        }

        return $componentesComPrioridades;
    }

    private function contarComponentesPorHabilitacao($componentesASeremInscritos, $componentesComPrioridades)
    {
        foreach ($this->habilitacoes as $habilitacao) {
            $contagemDeComponentesPorHabilitacao = [
                'novos' => [ 0, 0, 0 ],
                'repetidos' => [ 0, 0, 0 ],
            ];
            $contagem[] = $contagemDeComponentesPorHabilitacao;
        }
        $contagem['total'] = count($componentesASeremInscritos);

        // TCC
        foreach ($this->habilitacoes as $i => $habilitacao) {
            if ($i == 0) $contagem[0]['novos'][0] = 1;
            else $contagem[$i]['repetidos'][0] = 1;
        }

        foreach ($componentesASeremInscritos as $componente) {

            foreach ($this->habilitacoes as $i => $habilitacao) {
                if (!in_array($habilitacao->id, $componente['inscricoes_habilitacoes'])) continue;

                $prioridade = $this->recuperarPrioridadePara($componente, $componentesComPrioridades, $habilitacao);
                if ($this->ehComponenteNova($componente, $i)) {
                    $contagem[$i]['novos'][$prioridade]++;
                } else {
                    $contagem[$i]['repetidos'][$prioridade]++;
                }
            }
        }

        foreach ($this->habilitacoes as $i => $habilitacao) {
            $contagem[$i]['total'] = $contagem[$i]['novos'][0] + $contagem[$i]['repetidos'][0]
                + $contagem[$i]['novos'][1] + $contagem[$i]['repetidos'][1]
                + $contagem[$i]['novos'][2] + $contagem[$i]['repetidos'][2];
        }

        return $contagem;
    }

    private function contarComponentesSemHabilitacao($componentesASeremInscritos)
    {
        $contagem = [
            'obrigatorios' => 0,
            'livres' => 0,
        ];

        $idsComponentesObrigatorios = Constantes::COMPONENTES_OBRIGATORIAS_PARA_EXTENSAO_E_APERFEICOAMENTO();
        $idsComponentesASeremInscritos = array_map(function($componente) {
            return $componente['componente_id'];
        }, $componentesASeremInscritos);
        foreach ($idsComponentesASeremInscritos as $componenteId) {
            if (in_array($componenteId, $idsComponentesObrigatorios)) {
                $contagem['obrigatorios']++;
            } else {
                $contagem['livres']++;
            }
        }

        return $contagem;
    }

    private function recuperarPrioridadePara($componente, $componentesComPrioridades, $habilitacao)
    {
        foreach ($componentesComPrioridades[ $componente['componente_id'] ] as $habilitacaoId => $prioridade) {
            if ($habilitacaoId == $habilitacao->id) {
                return $prioridade;
            }
        }
        return -1;
    }

    private function ehComponenteNova($componente, $indiceHabilitacao)
    {
        for ($i = 0; $i < $indiceHabilitacao; $i++) {
            if (in_array($this->habilitacoes[$i]->id, $componente['inscricoes_habilitacoes'])) {
                return false;
            }
        }
        return true;
    }

    private function processarPrioridades($stringPrioridades)
    {
        $habilitacoesEPrioridades = explode(',', substr($stringPrioridades, 1, -1));
        $prioridadesPorHabilitacao = [];
        foreach ($habilitacoesEPrioridades as $habilitacaoEPrioridade) {
            [ $idHabilitacao, $prioridade ] = explode("-", $habilitacaoEPrioridade);
            $prioridadesPorHabilitacao[ $idHabilitacao ] = $prioridade;
        }
        return $prioridadesPorHabilitacao;
    }

    private function validarNumeroMinimoDeComponentesObrigatoriosPorHabilitacao($contagem, $indiceHabilitacao)
    {
        return $contagem[$indiceHabilitacao]['novos'][0] + $contagem[$indiceHabilitacao]['repetidos'][0] >= Constantes::NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS;
    }

    private function validarNumeroMinimoDeComponentesOptativosPorHabilitacao($contagem, $indiceHabilitacao)
    {
        return $contagem[$indiceHabilitacao]['novos'][1] + $contagem[$indiceHabilitacao]['repetidos'][1] >= Constantes::NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS;
    }

    private function validarNumeroMinimoDeComponentesTotais($cargaHoraria, $indiceHabilitacao)
    {
        return Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES($indiceHabilitacao + 1) <= $cargaHoraria[$indiceHabilitacao]['total'];
    }

    private function validarNumeroMaximoDeComponentesTotais($cargaHoraria, $indiceHabilitacao)
    {
        return $cargaHoraria[$indiceHabilitacao]['total'] <= Constantes::NUMERO_MAXIMO_DE_COMPONENTES_POR_HABILITACAO($indiceHabilitacao + 1);
    }

    private function validarNumeroMinimoDeComponentesNovas($cargaHoraria, $indiceHabilitacao)
    {
        return Constantes::NUMERO_MINIMO_DE_COMPONENTES_NOVAS_POR_HABILITACAO <=
            $cargaHoraria[$indiceHabilitacao]['novos'][0]
            + $cargaHoraria[$indiceHabilitacao]['novos'][1]
            + $cargaHoraria[$indiceHabilitacao]['novos'][2];
    }

    private function validarNumeroMinimoDeComponentesTotaisDeTodasHabilitacoes($cargaHoraria)
    {
        return $cargaHoraria['total'] >= Constantes::NUMERO_MINIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES(count($this->habilitacoes));
    }

    private function validarNumeroMaximoDeComponentesTotaisDeTodasHabilitacoes($cargaHoraria)
    {
        return $cargaHoraria['total'] <= Constantes::NUMERO_MAXIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES(count($this->habilitacoes));
    }

    // function validarComponentesObrigatoriosSemHabilitacao($cargaHoraria, ofertasInscritas) {
    //     const componentesObrigatoriasEstaoSelecionadas = constantes.COMPONENTES_OBRIGATORIAS_PARA_EXTENSAO_E_APERFEICOAMENTO.every(function(idComponenteObrigatorio) {
    //         return ofertasInscritas.some(function(oferta) {
    //             return oferta.componente.id == idComponenteObrigatorio;
    //         });
    //     });
    //     return componentesObrigatoriasEstaoSelecionadas
    //         && $cargaHoraria[componentesObrigatorios == constantes.NUMERO_DE_COMPONENTES_OBRIGATORIOAS_PARA_EXTENSAO_E_APERFEICOAMENTO;
    // }

    // function validarNumeroDeComponentesLivresSemHabilitacao($cargaHoraria) {
    //     const numeroDeComponentesLivres = $scope.tipoDeCurso === 0
    //         ? constantes.NUMERO_DE_COMPONENTES_LIVRES_PARA_EXTENSAO
    //         : constantes.NUMERO_DE_COMPONENTES_LIVRES_PARA_APERFEICOAMENTO;
    //     return $cargaHoraria[componentesLivres == numeroDeComponentesLivres;
    // }

    public function recuperarMensagensDeErro()
    {
        return $this->mensagensDeErro;
    }

}