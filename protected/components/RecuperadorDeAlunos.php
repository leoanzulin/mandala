<?php

/**
 * Classe responsável por recuperar todas as ofertas do sistema e organizá-las
 * em períodos. Opcionalmente, pode-se fornecer o ID de uma inscrição
 * para marcar as ofertas em que ela está isncrita.
 */
class RecuperadorDeAlunos
{
    private $ativos = null;
    private $tipoCurso = null;

    public function ativos()
    {
        $this->ativos = true;
        return $this;
    }

    public function deEspecializacao()
    {
        $this->tipoCurso = 2;
        return $this;
    }




    /**
     * Retorna todas as ofertas do sistema e em quais uma determinada inscrição
     * está inscrita.
     *
     * @param type $inscricaoId
     * @return array Ofertas organizadas por períodos
     */
    public function daInscricao($inscricaoId)
    {
        $this->daInscricao = $inscricaoId;
        return $this;
    }

    public function semCamposRelacionados()
    {
        $this->comCamposRelacionados = false;
        return $this;
    }

    public function doMesEAno($mes, $ano)
    {
        $this->mes = $mes;
        $this->ano = $ano;
        return $this;
    }

    public function manterApenasInscritas()
    {
        $this->manterApenasInscritas = true;
        return $this;
    }

    public function manterApenasOfertasPassadasEPresentes()
    {
        $this->manterApenasOfertasPassadasEPresentes = true;
        return $this;
    }

    public function manterApenasOfertasAprovadas()
    {
        $this->manterApenasOfertasAprovadas = true;
        return $this;
    }

    /**
     * Recupera todas as ofertas do sistema e as organiza em períodos.
     */
    public function recuperar()
    {
        "SELECT * FROM inscricao"
        $sqlOfertas = "
            SELECT
                o.id AS chave_oferta_id1, o.ano, o.mes, o.ano * 12 + o.mes AS meses, o.turma, o.link_moodle, o.codigo_moodle,
                c.id AS componente_curricular_id, c.nome AS componente_curricular,
                (SELECT
                    ARRAY_AGG(c_h.habilitacao_id || '-' || c_h.prioridade || '-' || h.cor)
                    FROM componente_habilitacao c_h
                        JOIN habilitacao h ON h.id = c_h.habilitacao_id
                    WHERE c_h.componente_curricular_id = c.id
                ) AS prioridades_por_habilitacao,
                ARRAY_AGG(DISTINCT d.nome || ' ' || d.sobrenome || '#' || d.cpf) AS docentes,
                ARRAY_AGG(DISTINCT t.nome || ' ' || t.sobrenome || '#' || t.cpf) AS tutores,
                (SELECT TRUE WHERE EXISTS (
                    SELECT * from inscricao_oferta i_o WHERE i_o.oferta_id = o.id)
                ) AS ha_inscricoes,
                (SELECT
                    COUNT(i_o.inscricao_id)
                    FROM inscricao_oferta i_o
                        JOIN inscricao i ON i_o.inscricao_id = i.id
                    WHERE i.status_aluno = 'Ativo' AND i_o.oferta_id = o.id
                ) AS numero_de_inscricoes_ativas
            FROM oferta o
                JOIN componente_curricular c ON o.componente_curricular_id = c.id
                LEFT JOIN docente_oferta d_o ON o.id = d_o.oferta_id
                LEFT JOIN docente d ON d_o.docente_cpf = d.cpf AND d_o.oferta_id = o.id
                LEFT JOIN tutor_oferta t_o ON o.id = t_o.oferta_id
                LEFT JOIN tutor t ON t_o.tutor_cpf = t.cpf AND t_o.oferta_id = o.id
            WHERE
        ";

        $condicoes = [ '1 = 1' ];
        if ($this->mes) $condicoes[] = "o.mes = {$this->mes}";
        if ($this->ano) $condicoes[] = "o.ano = {$this->ano}";
        $sqlOfertas .= implode(' AND ', $condicoes);
        $sqlOfertas .= " GROUP BY o.id, c.id ";

        $sqlFinal = $sqlOfertas;

        if ($this->daInscricao) {
            $sqlInscricoes = "
                SELECT
                    i_o.oferta_id AS chave_oferta_id2, TRUE as inscrito,
                    i_o.confirmada, i_o.media, i_o.status, i_o.frequencia, i_o.nota_virtual, i_o.nota_presencial
                FROM inscricao_oferta i_o
                WHERE i_o.inscricao_id = {$this->daInscricao}
            ";
            $sqlFinal = "
                SELECT * FROM ({$sqlOfertas}) t1 LEFT JOIN ({$sqlInscricoes}) t2
                ON t1.chave_oferta_id1 = t2.chave_oferta_id2
            ";
            $condicoes = array();
            if ($this->manterApenasInscritas) {
                $condicoes[] = "inscrito = TRUE";
            }
            if ($this->manterApenasOfertasPassadasEPresentes) {
                $condicoes[] = "meses <= DATE_PART('year', NOW()) * 12 + DATE_PART('month', NOW())";
            }
            if (!empty($condicoes)) {
                $sqlFinal .= "WHERE " . implode(' AND ', $condicoes);
            }
        }

        $sqlFinal .= " ORDER BY ano, mes, componente_curricular";
        $ofertas = Yii::app()->db->createCommand($sqlFinal)->queryAll();

        $ofertasPorPeriodos = array();

        foreach ($ofertas as $oferta) {

            $ofertaArray = array(
                'id' => $oferta['chave_oferta_id1'],
                'ano' => $oferta['ano'],
                'mes' => $oferta['mes'],
                'nome' => "{$oferta['componente_curricular']} {$oferta['mes']}/{$oferta['ano']}",
                'componente' => $this->processarComponente($oferta),
                'turma' => $oferta['turma'],
                'linkMoodle' => $oferta['link_moodle'],
                'codigoMoodle' => $oferta['codigo_moodle'],

                'podeSerDeletada' => empty($oferta['ha_inscricoes']),
                'bloqueada' => $this->ehAntesDeHoje($oferta['mes'], $oferta['ano']),
                'nomesDocentes' => $this->processarNomesDeDocentes($oferta['docentes']),

                'docentes' => $this->comCamposRelacionados ? $this->processarPessoas($oferta['docentes']) : array(),
                'tutores' => $this->comCamposRelacionados ? $this->processarPessoas($oferta['tutores']) : array(),

                'selecionada' => !empty($oferta['inscrito']) ? $oferta['inscrito'] == true : null,
                'confirmada' => isset($oferta['confirmada']) ? $oferta['confirmada'] : null,
                'nota_virtual' => !empty($oferta['nota_virtual']) ? $oferta['nota_virtual'] : null,
                'nota_presencial' => !empty($oferta['nota_presencial']) ? $oferta['nota_presencial'] : null,
                'media' => !empty($oferta['media']) ? $oferta['media'] : null,
                'frequencia' => !empty($oferta['frequencia']) ? $oferta['frequencia'] : null,
                'numeroDeInscricoesAtivas' => $oferta['numero_de_inscricoes_ativas'],
            );

            if ($this->manterApenasOfertasAprovadas
                    && $ofertaArray['selecionada']
                    && !InscricaoOferta::foiAprovada($ofertaArray['media'], $ofertaArray['frequencia'])) continue;
            if ($this->manterApenasOfertasPassadasEPresentes && !$ofertaArray['bloqueada']) continue;

            $periodo = &$this->periodoCorrespondenteAOferta($oferta, $ofertasPorPeriodos);

            if ($ofertaArray['podeSerDeletada'] == false) {
                $periodo['podeSerDeletado'] = false;
            }

            $periodo['ofertas'][] = $ofertaArray;
        }

        return $ofertasPorPeriodos;
    }

    private function processarComponente($oferta)
    {
        $componente = array(
            'id' => $oferta['componente_curricular_id'],
            'nome' => $oferta['componente_curricular'],
            'prioridades' => $this->processarPrioridades($oferta['prioridades_por_habilitacao']),
            'cargaHoraria' => 20,
        );
        $componente['ehNecessaria'] = $this->algumaPrioridadeEhNecessaria($componente['prioridades']);
        return $componente;
    }

    private function processarPrioridades($stringPrioridades)
    {
        $prioridades = array();
        $prioridadesPorHabilitacao = explode(',', substr($stringPrioridades, 1, -1));

        foreach ($prioridadesPorHabilitacao as $prioridadePorHabilitacao) {
            $campos = explode('-', $prioridadePorHabilitacao);
            $prioridade = array(
                'id' => (int)$campos[0],
                'prioridade' => (int)$campos[1],
                'cor' => (int)$campos[1] <= Constantes::PRIORIDADE_OPTATIVA ? $campos[2] : '',
                'classeCss' => (int)$campos[1] <= Constantes::PRIORIDADE_OPTATIVA ? 'prioridade' : '',
                'letra' => Constantes::PRIORIDADE_PARA_LETRA((int)$campos[1]),
            ); 
            $prioridades[] = $prioridade;
        }

        usort($prioridades, function($a, $b) {
            $order = [ 3, 4, 1, 2, 5 ];
            foreach ($order as $id) {
                if ($a['id'] == $id) return -1;
                if ($b['id'] == $id) return 1;
            }
            return $a['id'] < $b['id'] ? -1 : 1;
        });

        return $prioridades;
    }

    private function algumaPrioridadeEhNecessaria($prioridades)
    {
        foreach ($prioridades as $prioridade)
            if ($prioridade['prioridade'] == Constantes::PRIORIDADE_NECESSARIA) return true;
        return false;
    }

    private function ehAntesDeHoje($mes, $ano)
    {
        $mesesHoje = date("Y") * 12 + date("m");
        $mesesData = $ano * 12 + $mes;
        return $mesesData < $mesesHoje;
    }

    private function processarNomesDeDocentes($arrayNomes)
    {
        if ($arrayNomes === '{NULL}') return '';

        $arrayNomesSemChaves = substr($arrayNomes, 1, -1);
        $arrayNomesSemChavesEAspas = str_replace('"', '', $arrayNomesSemChaves);
        $nomes = explode(',', $arrayNomesSemChavesEAspas);
        foreach ($nomes as &$nome) {
            $partes = explode('#', $nome);
            $nome = $partes[0];
        }
        return implode(', ', $nomes);
    }

    private function processarPessoas($pessoas)
    {
        if ($pessoas === '{NULL}') return array();

        $pessoasSemChaves = substr($pessoas, 1, -1);
        $pessoasSemChavesEAspas = str_replace('"', '', $pessoasSemChaves);
        $pessoasComCpf = explode(',', $pessoasSemChavesEAspas);
        return array_map(function($pessoaComCpf) {
            $partes = explode('#', $pessoaComCpf);
            return array(
                'nomeCompleto' => $partes[0],
                'cpf' => $partes[1],
            );
        }, $pessoasComCpf);
    }

    private function &periodoCorrespondenteAOferta($oferta, &$periodos)
    {
        $indiceDoUltimoPeriodo = count($periodos) - 1;

        if ($indiceDoUltimoPeriodo == -1 ||
                $periodos[$indiceDoUltimoPeriodo]['ano'] != $oferta['ano'] ||
                $periodos[$indiceDoUltimoPeriodo]['mes'] != $oferta['mes']) {
            $periodos[] = $this->novoPeriodo($oferta);
        }

        return $periodos[count($periodos) - 1];
    }

    private function novoPeriodo($oferta)
    {
        return array(
            'ano' => $oferta['ano'],
            'mes' => $oferta['mes'],
            'ofertas' => array(),
            'bloqueado' => $this->ehAntesDeHoje($oferta['mes'], $oferta['ano']),
            'podeSerDeletado' => true,
        );
    }

    /**
     * Retorna apenas as ofertas em que uma inscrição está inscrita.
     */
    public function ofertasDaInscricao($inscricaoId)
    {
        $view = 'inscricoes_em_ofertas_por_aluno';

        $sql = "SELECT * FROM {$view} WHERE inscricao_id = {$inscricaoId}";
        $ofertas = Yii::app()->db->createCommand($sql)->queryAll();

        return $ofertas;
    }

}
