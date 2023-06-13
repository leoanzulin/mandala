<?php

class ExportadorController extends Controller
{

    public function actionRelatorioDeInscricoesComStatus($status = 0, $formato = 'xls')
    {
        $inscricoes = Inscricao::model()->findAllByAttributes(array(
            'status' => $status,
        ));

        $cabecalho = array('cpf', 'numero_ufscar', 'nome', 'sobrenome', 'email', 'celular', 'cidade', 'estado', 'habilitacao1', 'habilitacao2', 'modalidade', 'status', 'tipo_curso', 'data_conclusao', 'processo_proex');
        $dados = array();
        foreach ($inscricoes as $inscricao) {
            array_push($dados, array(
                'cpf' => $inscricao->cpf,
                'numero_ufscar' => $inscricao->numero_ufscar,
                'nome' => $inscricao->nome,
                'sobrenome' => $inscricao->sobrenome,
                'email' => $inscricao->email,
                'celular' => $inscricao->telefone_celular,
                'cidade' => $inscricao->cidade,
                'estado' => $inscricao->estado,
                'habilitacao1' => $inscricao->habilitacao1,
                'habilitacao2' => $inscricao->habilitacao2,
                'modalidade' => $inscricao->modalidade,
                'status' => $inscricao->status,
                'tipo_curso' => $inscricao->tipoDeCursoPorExtenso(),
                'data_conclusao' => $inscricao->recuperarDataConclusao(),
                'processo_proex' => $inscricao->processo_proex,
            ));
        }
        switch ($status) {
            case Inscricao::STATUS_DOCUMENTOS_SENDO_ANALISADOS:
                $filename = 'Lista de inscrições com documentos sendo analisados';
                break;
            case Inscricao::STATUS_DOCUMENTOS_VERIFICADOS:
                $filename = 'Lista de inscrições com documentos verificados';
                break;
            case Inscricao::STATUS_MATRICULADO:
                $filename = 'Lista de alunos matriculados';
                break;
        }
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    /**
     * Gera a lista de cadastro de alunos no Moodle de uma determinada oferta.
     * 
     * @param integer $ofertaId
     */
    public function actionListaCadastroMoodle($ofertaId)
    {
        $oferta = Oferta::model()->findByPk($ofertaId);
        $inscricoesApenasAlunosAtivos = array_filter($oferta->inscricoes, function($inscricao) {
            return $inscricao->status_aluno === 'Ativo';
        });
        $inscricoesConfirmadas = InscricaoOferta::model()->findAllByAttributes([
            'oferta_id' => $ofertaId,
            'confirmada' => true,
        ]);
        $inscricoesApenasConfirmadas = array_filter($inscricoesApenasAlunosAtivos, function($inscricao) use ($inscricoesConfirmadas) {
            return true;
            foreach ($inscricoesConfirmadas as $inscricaoConfirmada) {
                if ($inscricaoConfirmada->inscricao_id === $inscricao->id) {
                    return true;
                }
            }
            return false;
        });

        $cabecalho = array('username', 'numero_ufscar', 'password', 'firstname', 'lastname', 'email', 'city', 'auth', 'course1');
        $dados = array();
        foreach ($inscricoesApenasConfirmadas as $inscricao) {
            array_push($dados, array(
                'username' => $inscricao->cpf,
                'numero_ufscar' => $inscricao->numero_ufscar,
                'password' => '@Mudar' . date('Y'),
                'firstname' => $inscricao->nome,
                'lastname' => $inscricao->sobrenome,
                'email' => $inscricao->email,
                'city' => 'Sao Carlos',
                'auth' => 'manual',
                'course1' => $oferta->codigo_moodle,
            ));
        }
        $filename = "lista_cadastro_moodle_oferta_{$oferta->recuperarNomeParaArquivo()}";
        Exportador::exportar($cabecalho, $dados, $filename, 'xls');
    }

    /**
     * Gera uma lista das inscrições de todos os alunos em todas as ofertas do sistema.
     */
    public function actionListaAlunosTodasOfertas()
    {
        $cabecalho = array('cpf', 'numero_ufscar', 'nome', 'sobrenome', 'email', 'status_aluno', 'mes', 'ano', 'componente_curricular');
        $sql = 'SELECT DISTINCT i.cpf, i.numero_ufscar, i.nome, i.sobrenome, i.email, i.status_aluno, o.mes, o.ano, cc.nome AS "componente_curricular" FROM inscricao AS i, inscricao_oferta AS io, oferta AS o, componente_curricular AS cc WHERE i.id = io.inscricao_id AND io.oferta_id = o.id AND o.componente_curricular_id = cc.id ORDER BY o.ano, o.mes, cc.nome, i.nome, i.sobrenome;';
        $filename = "lista_alunos_todas_ofertas";
        Exportador::exportar($cabecalho, $sql, $filename, 'xls');
    }

    public function actionRelatorioPersonalizado($parametros)
    {
        $cabecalho = explode(',', $parametros);
        $sql = 'SELECT ' . implode(', ', $this->transformarCampoHabilitacoesDo($cabecalho)) . ' FROM inscricao i WHERE status = 3';
        $filename = 'Lista de alunos personalizada';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $sql, $filename, $formato);
    }

    private function transformarCampoHabilitacoesDo($cabecalho)
    {
        $cabecalhoProcessado = array();
        foreach ($cabecalho as $coluna) {
            if ($coluna === 'habilitacoes') {
                $coluna = "(SELECT ARRAY_AGG(ih.ordem || '-' || h.nome) FROM habilitacao h JOIN inscricao_habilitacao ih on ih.habilitacao_id = h.id JOIN inscricao i2 ON ih.inscricao_id = i2.id WHERE i.id = i2.id) AS habilitacoes";
            }
            $cabecalhoProcessado[] = $coluna;
        }
        return $cabecalhoProcessado;
    }

    public function actionRelatorioDeComponentes()
    {
        $componentes = ComponenteCurricular::model()->findAllByAttributes(['ativo' => true], ['order' => 'nome']);

        $cabecalho = ['componente', 'ofertas e docentes', 'ementa', 'bibliografia'];
        $dados = [];
        foreach ($componentes as $componente) {
            $dados[] = [
                'componente' => $componente->nome,
                'ofertas e docentes' => $this->formatarOfertasEDocentes($componente),
                'ementa' => $componente->ementa,
                'bibliografia' => $componente->bibliografia,
            ];
        }
        $filename = 'Lista de componentes';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    private function formatarOfertasEDocentes($componente)
    {
        $string = '';
        foreach ($componente->ofertas as $oferta) {
            $string .= "{$oferta->mes}/{$oferta->ano} - docentes: {$oferta->recuperarNomesDeDocentes()}\n";
        }
        return $string;
    }

    public function actionPlanilhaDePagamentosDeAlunos()
    {
        $pagamentos = PagamentoAluno::model()->findAllByAttributes([], ['order' => 'inscricao_id ASC, data ASC']);

        $cabecalho = ['aluno', 'bolsa', 'data', 'tipo', 'valor', 'observações'];
        $dados = [];
        foreach ($pagamentos as $pagamento) {
            $dados[] = [
                'aluno' => $pagamento->inscricao->nomeCompleto,
                'bolsa' => $pagamento->inscricao->tipo_bolsa,
                'data' => $pagamento->data,
                'tipo' => $pagamento->tipo,
                'valor' => number_format($pagamento->valor, 2, ',', ''),
                'observações' => $pagamento->observacoes,
            ];
        }
        $filename = 'Pagamentos de alunos';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionPlanilhaDePagamentosDeColaboradores()
    {
        $pagamentos = PagamentoColaborador::model()->findAllByAttributes([], ['order' => 'data ASC']);

        $cabecalho = ['colaborador', 'data_pagamento', 'serviços (função)', 'serviços (descrição)', 'serviços (valor)', 'valor_total', 'valor_pago', 'sobra', 'forma_pagamento'];
        $dados = [];
        foreach ($pagamentos as $pagamento) {

            foreach ($pagamento->servicos as $i => $servico) {
                if ($i == 0) {
                    $dados[] = [
                        'colaborador' => $pagamento->getColaborador()->nomeCompleto,
                        'data_pagamento' => $pagamento->data,
                        'serviços (função)' => $servico->funcao,
                        'serviços (descrição)' => $servico->descricao,
                        'serviços (valor)' => number_format($servico->valor, 2, ',', ''),
                        'valor_total' => number_format($pagamento->valor_total, 2, ',', ''),
                        'valor_pago' => number_format($pagamento->valor_pago, 2, ',', ''),
                        'sobra' => number_format($pagamento->valor_pago - $pagamento->valor_total, 2, ',', ''),
                        'forma_pagamento' => $pagamento->forma_pagamento,
                    ];
                } else {
                    $dados[] = [
                        'colaborador' => null,
                        'data_pagamento' => null,
                        'serviços (função)' => $servico->funcao,
                        'serviços (descrição)' => $servico->descricao,
                        'serviços (valor)' => $servico->valor,
                        'valor_total' => null,
                        'valor_pago' => null,
                        'sobra' => null,
                        'forma_pagamento' => null,
                    ];
                }
            }
        }
        $filename = 'Pagamentos de colaboradores';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionPlanilhaDePagamentosDeColaboradoresDoMesEAno($mes, $ano)
    {
        $proximoMes = $mes + 1;
        $proximoAno = $ano;
        if ($proximoMes == 13) {
            $proximoMes = 1;
            $proximoAno = $ano + 1;
        }
        $mes = $mes < 10 ? "0{$mes}" : $mes;
        $dataInicial = "{$ano}-{$mes}-01";
        $dataFinal = "{$proximoAno}-{$proximoMes}-01";
        $pagamentos = PagamentoColaborador::model()->findAll(
            [
                'condition' => 'data >= :dataInicial and data < :dataFinal',
                'order' => 'data ASC',
                'params' =>             [
                    ':dataInicial' => $dataInicial,
                    ':dataFinal' => $dataFinal
                ]
            ]
        );

        $cabecalho = ['colaborador', 'data_pagamento', 'serviços (função)', 'serviços (descrição)', 'serviços (valor)', 'valor_total', 'valor_pago', 'sobra', 'forma_pagamento'];
        $dados = [];
        foreach ($pagamentos as $pagamento) {

            foreach ($pagamento->servicos as $i => $servico) {
                if ($i == 0) {
                    $dados[] = [
                        'colaborador' => $pagamento->getColaborador()->nomeCompleto,
                        'data_pagamento' => $pagamento->data,
                        'serviços (função)' => $servico->funcao,
                        'serviços (descrição)' => $servico->descricao,
                        'serviços (valor)' => number_format($servico->valor, 2, ',', ''),
                        'valor_total' => number_format($pagamento->valor_total, 2, ',', ''),
                        'valor_pago' => number_format($pagamento->valor_pago, 2, ',', ''),
                        'sobra' => number_format($pagamento->valor_pago - $pagamento->valor_total, 2, ',', ''),
                        'forma_pagamento' => $pagamento->forma_pagamento,
                    ];
                } else {
                    $dados[] = [
                        'colaborador' => null,
                        'data_pagamento' => null,
                        'serviços (função)' => $servico->funcao,
                        'serviços (descrição)' => $servico->descricao,
                        'serviços (valor)' => $servico->valor,
                        'valor_total' => null,
                        'valor_pago' => null,
                        'sobra' => null,
                        'forma_pagamento' => null,
                    ];
                }
            }
        }
        $filename = 'Pagamentos de colaboradores no mês de ' . CalendarioHelper::nomeDoMes($mes) . ' de ' . $ano;
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionPlanilhaDeViagens()
    {
        $viagens = Viagem::model()->findAllByAttributes([], ['order' => 'data_ida ASC']);

        $cabecalho = ['colaborador', 'data_ida', 'data_volta', 'local', 'despesas (tipo)', 'despesas (valor)'];
        $dados = [];
        foreach ($viagens as $viagem) {

            foreach ($viagem->despesas as $i => $despesa) {
                if ($i == 0) {
                    $dados[] = [
                        'colaborador' => $viagem->getColaborador()->nomeCompleto,
                        'data_ida' => $viagem->data_ida,
                        'data_volta' => $viagem->data_volta,
                        'local' => $viagem->local,
                        'despesas (tipo)' => $despesa->tipo,
                        'despesas (valor)' => number_format($despesa->valor, 2, ',', ''),
                    ];
                } else {
                    $dados[] = [
                        'colaborador' => null,
                        'data_ida' => null,
                        'data_volta' => null,
                        'local' => null,
                        'despesas (tipo)' => $despesa->tipo,
                        'despesas (valor)' => number_format($despesa->valor, 2, ',', ''),
                    ];
                }
            }
        }
        $filename = 'Viagens';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionPlanilhaDeCompras()
    {
        $compras = Compra::model()->findAllByAttributes([], ['order' => 'data ASC']);

        $cabecalho = ['colaborador', 'data', 'descricao', 'local', 'valor'];
        $dados = [];
        foreach ($compras as $compra) {
            $dados[] = [
                'colaborador' => $compra->getColaborador()->nomeCompleto,
                'data' => $compra->data,
                'descricao' => $compra->descricao,
                'local' => $compra->local,
                'valor' => $compra->valor,
            ];
        }
        $filename = 'Compras';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionRelatorioDeAlunosComOfertas()
    {
        $sql = "
SELECT
    i.numero_ufscar, i.cpf, i.nome || ' ' || i.sobrenome AS aluno,
    c.nome AS componente_curricular,
    o.ano, o.mes, i_o.media, i_o.frequencia
FROM oferta o
    JOIN componente_curricular c ON o.componente_curricular_id = c.id
    JOIN inscricao_oferta i_o ON i_o.oferta_id = o.id
    JOIN inscricao i ON i_o.inscricao_id = i.id
WHERE
    i.status = 3
ORDER BY
    i.id, o.ano, o.mes, c.nome
;
        ";
        $alunosComOfertas = Yii::app()->db->createCommand($sql)->queryAll();

        $cabecalho = ['numero_ufscar', 'cpf', 'aluno', 'componente_curricular', 'ano', 'mes', 'media', 'frequencia'];
        $dados = [];
        foreach ($alunosComOfertas as $alunoComOferta) {
            $dados[] = [
                'numero_ufscar' => $alunoComOferta['numero_ufscar'],
                'cpf' => $alunoComOferta['cpf'],
                'aluno' => $alunoComOferta['aluno'],
                'componente_curricular' => $alunoComOferta['componente_curricular'],
                'ano' => $alunoComOferta['ano'],
                'mes' => $alunoComOferta['mes'],
                'media' => $alunoComOferta['media'],
                'frequencia' => $alunoComOferta['frequencia'],
            ];
        }
        $filename = 'Lista de alunos com ofertas cursadas';
        $formato = 'csv';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionListaDePresenca($id)
    {
        $alunos = EncontroPresencialInscricao::model()->with('aluno')->findAllByAttributes([
            'encontro_presencial_id' => $id,
        ], [
            'order' => 'aluno.nome, aluno.sobrenome',
        ]);

        $cabecalho = ['aluno', 'presente'];
        $dados = [];
        foreach ($alunos as $aluno) {
            $dados[] = [
                'aluno' => $aluno->aluno->nomeCompleto,
                'presente' => is_null($aluno->presente) ? '' : ($aluno->presente ? 'V' : 'X'),
            ];
        }
        $filename = 'Lista de presença';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionEncontrosPresenciais()
    {
        $encontros = EncontroPresencial::model()->with('alunos')->findAllByAttributes([
            'ativo' => true,
        ]);

        $cabecalho = ['local', 'tipo', 'data', 'atividades', 'responsaveis', 'aluno', 'presente'];
        $dados = [];
        foreach ($encontros as $encontro) {

            $idsAlunosPresentes = array_map(
                function($aluno) { return $aluno->inscricao_id; },
                EncontroPresencialInscricao::model()->findAllByAttributes([
                    'encontro_presencial_id' => $encontro->id,
                    'presente' => true,
                ])
            );

            if (empty($encontro->alunos)) {
                $dados[] = [
                    'local' => $encontro['local'],
                    'tipo' => $encontro['tipo'],
                    'data' => $encontro['data'],
                    'atividades' => $encontro['atividades'],
                    'responsaveis' => $this->getResponsaveis($encontro),
                    'aluno' => '',
                    'presente' => '',
                ];
            }

            foreach ($encontro->alunos as $i => $aluno) {
                if ($i == 0) {
                    $dados[] = [
                        'local' => $encontro['local'],
                        'tipo' => $encontro['tipo'],
                        'data' => $encontro['data'],
                        'atividades' => $encontro['atividades'],
                        'responsaveis' => $this->getResponsaveis($encontro),
                        'aluno' => $aluno->nomeCompleto,
                        'presente' => in_array($aluno->id, $idsAlunosPresentes) ? 'V' : 'X',
                    ];
                } else {
                    $dados[] = [
                        'local' => '',
                        'tipo' => '',
                        'data' => '',
                        'atividades' => '',
                        'responsaveis' => '',
                        'aluno' => $aluno->nomeCompleto,
                        'presente' => in_array($aluno->id, $idsAlunosPresentes) ? 'V' : 'X',
                    ];
                }
            }

        }
        $filename = 'Encontros presenciais';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    private function getResponsaveis($encontro)
    {
        $responsaveis = [];
        foreach ($encontro->responsaveis as $responsavel) {
            $responsaveis[] = $responsavel['nome'];
        }
        return implode(', ', $responsaveis);
    }

    public function actionNotasProex()
    {
        $sql = "
SELECT
    i.cpf, i_o.media, i_o.frequencia, o.ano, o.mes, c.nome
FROM oferta o
    JOIN componente_curricular c ON o.componente_curricular_id = c.id
    JOIN inscricao_oferta i_o ON i_o.oferta_id = o.id
    JOIN inscricao i ON i_o.inscricao_id = i.id
WHERE
    i.status = 3
    AND o.ativo IS TRUE
ORDER BY
    o.ano, o.mes, c.nome, i.nome
;
        ";
        $ofertasComNotas = Yii::app()->db->createCommand($sql)->queryAll();

        $cabecalho = ['componente', 'cpf', 'nota', 'frequencia', 'aprovado'];
        $dados = [];
        $ofertaAtual = '';
        foreach ($ofertasComNotas as $ofertaComNota) {
            $nomeComponente = "{$ofertaComNota['nome']}_{$ofertaComNota['ano']}_{$ofertaComNota['mes']}";
            if ($ofertaAtual != $nomeComponente) {
                $ofertaAtual = $nomeComponente;
                $dados[] = [
                    'componente' => $nomeComponente,
                    'cpf' => '',
                    'nota' => '',
                    'frequencia' => '',
                    'aprovado' => '',
                ];
            }
            $dados[] = [
                'componente' => '',
                'cpf' => $ofertaComNota['cpf'],
                'nota' => $ofertaComNota['media'],
                'frequencia' => $ofertaComNota['frequencia'],
                'aprovado' => $ofertaComNota['media'] >= Constantes::MEDIA_MINIMA && $ofertaComNota['frequencia'] >= Constantes::FREQUENCIA_MINIMA
                    ? 'aprovado' : '',
            ];
        }
        $filename = 'Lista de notas para ProExWeb';
        $formato = 'csv';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionNotasProexOferta($ofertaId)
    {
        $oferta = Oferta::model()->findByPk($ofertaId);
        $sql = "
SELECT
    i.cpf, i_o.media, i_o.frequencia, o.ano, o.mes, c.nome
FROM oferta o
    JOIN componente_curricular c ON o.componente_curricular_id = c.id
    JOIN inscricao_oferta i_o ON i_o.oferta_id = o.id
    JOIN inscricao i ON i_o.inscricao_id = i.id
WHERE
    i.status = 3
    AND o.ativo IS TRUE
    AND c.ativo IS TRUE
    AND o.id = {$ofertaId}
;
        ";
        $ofertasComNotas = Yii::app()->db->createCommand($sql)->queryAll();

        $texto = '';
        foreach ($ofertasComNotas as $nota) {
            $dados = [
                'cpf' => '"' . $nota['cpf'] . '"',
                'media' => '"' . $nota['media'] . '"',
                'frequencia' => '"' . $nota['frequencia'] . '"',
                'aprovado' => InscricaoOferta::model()->foiAprovada($nota['media'], $nota['frequencia']) ? '"aprovado"' : '""',
            ];
            $texto .= implode(',', $dados) . "\n";
        }

        $filename = "{$oferta->componenteCurricular->nome}_{$oferta->mes}/{$oferta->ano}.txt";

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($texto));
        header("Content-type: application/text");
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        echo $texto;
        return;
    }

    public function actionTccs()
    {
        $tccs = Tcc::model()->with('inscricao')->with('habilitacao')->findAllByAttributes([
            'ativo' => true,
        ]);

        $cabecalho = [
            'titulo', 'aluno', 'habilitacao', 'orientador_inicial', 'data_entrega_versao_inicial',
            'data_entrega_versao_banca', 'banca_data_apresentacao', 'banca_membro_1', 'banca_membro_2', 'banca_membro_3',
            'orientador', 'coorientador', 'data_entrega_versao_final'
        ];
        $dados = [];
        foreach ($tccs as $tcc) {
            $dados[] = [
                'titulo' => $tcc->titulo,
                'aluno' => $tcc->inscricao->nomeCompleto,
                'habilitacao' => $tcc->habilitacao->nome,
                'orientador_inicial' => $tcc->orientador_provisorio ? $tcc->orientador_provisorio->nome : '',
                'data_entrega_versao_inicial' => $tcc->validacao_data_entrega,
                'data_entrega_versao_banca' => $tcc->banca_data_entrega,
                'banca_data_apresentacao' => $tcc->banca_data_apresentacao,
                'banca_membro_1' => $tcc->banca_membro1 ? $tcc->banca_membro1->nomeCompleto : '',
                'banca_membro_2' => $tcc->banca_membro2 ? $tcc->banca_membro2->nomeCompleto : '',
                'banca_membro_3' => $tcc->banca_membro3 ? $tcc->banca_membro3->nomeCompleto : '',
                'orientador' => $tcc->orientador_final ? $tcc->orientador_final->nomeCompleto : '',
                'coorientador' => $tcc->coorientador_final ? $tcc->coorientador_final->nomeCompleto : '',
                'data_entrega_versao_final' => $tcc->final_data_entrega,
            ];
        }
        $filename = 'Lista de TCCs';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionEstudantesConcluintes()
    {
        $alunos = Inscricao::model()->findAllByAttributes([
            'ativo' => true,
        ]);

        $cabecalho = ['aluno', 'data_matricula', 'data_conclusao'];
        $dados = [];
        foreach ($alunos as $aluno) {
            if (!$aluno->estaFormadoOuProntoParaFormar()) continue;
            $dados[] = [
                'aluno' => $aluno->nomeCompleto,
                'data_matricula' => $aluno->data_matricula,
                'data_conclusao' => $aluno->recuperarDataConclusao(),
            ];
        }
        $filename = 'Lista de estudantes concluintes';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionListaDocentes()
    {
        $docentes = Docente::model()->findAll();

        $cabecalho = array('cpf', 'nome', 'sobrenome', 'titulo', 'email', 'telefone', 'endereco', 'numero', 'bairro', 'complemento', 'cep', 'mestrando_ou_doutorando_ufscar');
        $dados = array();
        foreach ($docentes as $docente) {
            array_push($dados, array(
                'cpf' => $docente->cpf,
                'nome' => $docente->nome,
                'sobrenome' => $docente->sobrenome,
                'titulo' => $docente->titulo,
                'email' => $docente->email,
                'telefone' => $docente->telefone,
                'endereco' => $docente->endereco,
                'numero' => $docente->numero,
                'bairro' => $docente->bairro,
                'complemento' => $docente->complemento,
                'cep' => $docente->cep,
                'mestrando_ou_doutorando_ufscar' => $docente->mestrando_ou_doutorando_ufscar ? 'Sim' : 'Não',
            ));
        }
        $filename = 'Lista de docentes';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionListaTutores()
    {
        $tutores = Tutor::model()->findAll();

        $cabecalho = array('cpf', 'nome', 'sobrenome', 'titulo', 'email', 'telefone', 'endereco', 'numero', 'bairro', 'complemento', 'cep', 'mestrando_ou_doutorando_ufscar');
        $dados = array();
        foreach ($tutores as $tutor) {
            array_push($dados, array(
                'cpf' => $tutor->cpf,
                'nome' => $tutor->nome,
                'sobrenome' => $tutor->sobrenome,
                'titulo' => $tutor->titulo,
                'email' => $tutor->email,
                'telefone' => $tutor->telefone,
                'endereco' => $tutor->endereco,
                'numero' => $tutor->numero,
                'bairro' => $tutor->bairro,
                'complemento' => $tutor->complemento,
                'cep' => $tutor->cep,
                'mestrando_ou_doutorando_ufscar' => $tutor->mestrando_ou_doutorando_ufscar ? 'Sim' : 'Não',
            ));
        }
        $filename = 'Lista de tutores';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionListaColaboradores()
    {
        $colaboradores = Colaborador::model()->findAll();

        $cabecalho = array('cpf', 'nome', 'sobrenome', 'email');
        $dados = array();
        foreach ($colaboradores as $colaborador) {
            array_push($dados, array(
                'cpf' => $colaborador->cpf,
                'nome' => $colaborador->nome,
                'sobrenome' => $colaborador->sobrenome,
                'email' => $colaborador->email,
            ));
        }
        $filename = 'Lista de colaboradores';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionComponentesPorHabilitacao()
    {
        $sql = "
SELECT
    h.nome AS habilitacao, c.nome AS componente, hc.prioridade
FROM habilitacao h
    JOIN componente_habilitacao hc ON hc.habilitacao_id = h.id
    JOIN componente_curricular c ON hc.componente_curricular_id = c.id
WHERE
    c.ativo IS TRUE
ORDER BY
    h.nome, c.nome
;
        ";
        $componentesPorHabilitacao = Yii::app()->db->createCommand($sql)->queryAll();

        $cabecalho = array('habilitacao', 'componente', 'prioridade');
        $dados = array();
        foreach ($componentesPorHabilitacao as $componentePorHabilitacao) {
            array_push($dados, array(
                'habilitacao' => $componentePorHabilitacao['habilitacao'],
                'componente' => $componentePorHabilitacao['componente'],
                'prioridade' => Constantes::PRIORIDADE_PARA_LETRA($componentePorHabilitacao['prioridade']),
            ));
        }
        $filename = 'Lista de componentes por habilitação';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionListaCertificados()
    {
        $statusTrancado = InscricaoOferta::STATUS_TRANCADO;
        $sql = "
SELECT
    i.numero_ufscar, i.cpf, i.nome || ' ' || i.sobrenome AS nome, i.data_matricula,
    h.nome AS habilitacao,
    c.nome AS componente, c.carga_horaria, o.ano, o.mes, io.media, io.frequencia, io.status,
    t.titulo as titulo_tcc, t.final_orientador_cpf AS orientador,
    ih.data_conclusao, ih.processo_proex
FROM
    inscricao i
    JOIN inscricao_habilitacao ih ON ih.inscricao_id = i.id
    JOIN habilitacao h ON ih.habilitacao_id = h.id
    JOIN inscricao_oferta io ON io.inscricao_id = i.id
    JOIN oferta o ON io.oferta_id = o.id
    JOIN componente_curricular c ON o.componente_curricular_id = c.id
    JOIN habilitacao_inscricao_oferta hio ON (hio.inscricao_id = io.inscricao_id AND hio.oferta_id = io.oferta_id) AND (hio.habilitacao_id = ih.habilitacao_id AND hio.inscricao_id = ih.inscricao_id)
    LEFT JOIN tcc t ON t.inscricao_id = i.id AND t.habilitacao_id = h.id
WHERE
    o.ativo IS TRUE
    AND c.ativo IS TRUE
    AND i.ativo IS TRUE
    AND io.status != '{$statusTrancado}'
ORDER BY
    i.nome,
    i.sobrenome,
    h.nome,
    o.ano,
    o.mes,
    c.nome
;
        ";
        $registros = Yii::app()->db->createCommand($sql)->queryAll();

        $cabecalho = ['numero_ufscar', 'cpf', 'aluno', 'data_matricula', 'habilitacao', 'componente', 'ano', 'mes', 'media', 'frequencia', 'carga_horaria_habilitacao', 'media_habilitacao', 'frequencia_habilitacao', 'titulo_tcc', 'orientador_tcc', 'data_conclusao', 'processo_proex'];
        $dados = [];

        $ultimaInscricao = '';
        $ultimaHabilitacao = '';
        $numeroComponentes = 0;
        $somaNotas = 0;
        $somaFrequencia = 0;
        $somaCargaHoraria = 0;

        foreach ($registros as $registro) {

            if (!empty($ultimaInscricao) && $numeroComponentes > 0 && ($ultimaInscricao != $registro['cpf'] || $ultimaHabilitacao != $registro['habilitacao'])) {

                $dados[] = [
                    'numero_ufscar' => '',
                    'cpf' => $ultimaInscricao,
                    'aluno' => 'MÉDIA',
                    'data_matricula' => '',
                    'habilitacao' => $ultimaHabilitacao,
                    'componente' => '',
                    'ano' => '',
                    'mes' => '',
                    'media' => '',
                    'frequencia' => '',
                    'carga_horaria_habilitacao' => $somaCargaHoraria,
                    'media_habilitacao' => round($somaNotas / $numeroComponentes, 1),
                    'frequencia_habilitacao' => round($somaFrequencia / $numeroComponentes, 1),
                    'titulo_tcc' => '',
                    'orientador_tcc' => '',
                    'data_conclusao' => '',
                    'processo_proex' => '',
                ];

                $somaFrequencia = 0;
                $somaNotas = 0;
                $numeroComponentes = 0;
                $somaCargaHoraria = 0;
            }

            if ($this->deveIgnorarRegistro($registro)) continue;

            $dados[] = [
                'numero_ufscar' => $registro['numero_ufscar'],
                'cpf' => $registro['cpf'],
                'aluno' => $registro['nome'],
                'data_matricula' => $registro['data_matricula'],
                'habilitacao' => $registro['habilitacao'],
                'componente' => $registro['componente'],
                'ano' => $registro['ano'],
                'mes' => $registro['mes'],
                'media' => $registro['media'],
                'frequencia' => $registro['frequencia'],
                'carga_horaria_habilitacao' => '',
                'media_habilitacao' => '',
                'frequencia_habilitacao' => '',
                'titulo_tcc' => $registro['titulo_tcc'],
                'orientador_tcc' => $registro['orientador'],
                'data_conclusao' => $registro['data_conclusao'],
                'processo_proex' => $registro['processo_proex'],
            ];

            $somaFrequencia += $registro['frequencia'];
            $somaNotas += $registro['media'];
            $somaCargaHoraria += $registro['carga_horaria'];
            $numeroComponentes++;

            $ultimaHabilitacao = $registro['habilitacao'];
            $ultimaInscricao = $registro['cpf'];
        }
        $filename = 'Lista de informações de certificados';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    private function deveIgnorarRegistro($registro)
    {
        if ($registro['status'] == InscricaoOferta::STATUS_TRANCADO) return true;
        if (!InscricaoOferta::foiAprovadaStatus($registro['status'])
            || !InscricaoOferta::foiAprovada($registro['media'], $registro['frequencia'])) return true;
        return false;
    }

    public function actionListaTodosAlunos()
    {
        $inscricoes = Inscricao::model()->findAll();

        $cabecalho = array('cpf', 'numero_ufscar', 'nome', 'sobrenome', 'email', 'celular', 'cidade', 'estado', 'modalidade', 'status', 'status_aluno', 'tipo_curso', 'data_conclusao', 'processo_proex');
        $dados = array();
        foreach ($inscricoes as $inscricao) {
            array_push($dados, array(
                'cpf' => $inscricao->cpf,
                'numero_ufscar' => $inscricao->numero_ufscar,
                'nome' => $inscricao->nome,
                'sobrenome' => $inscricao->sobrenome,
                'email' => $inscricao->email,
                'celular' => $inscricao->telefone_celular,
                'cidade' => $inscricao->cidade,
                'estado' => $inscricao->estado,
                'modalidade' => $inscricao->modalidade,
                'status' => $inscricao->statusPorExtenso,
                'status_aluno' => $inscricao->status_aluno,
                'tipo_curso' => $inscricao->tipoDeCursoPorExtenso(),
                'data_conclusao' => $inscricao->recuperarDataConclusao(),
                'processo_proex' => $inscricao->processo_proex,
            ));
        }
        $filename = 'Lista de todos os alunos';
        $formato = 'xls';
        Exportador::exportar($cabecalho, $dados, $filename, $formato);
    }

    public function actionCertificadosConclusaoTxt()
    {
        $view = 'inscricoes_em_ofertas_por_aluno';
        $statusMatriculado = Inscricao::STATUS_MATRICULADO;
        $statusDesistente = Inscricao::STATUS_ALUNO_DESISTENTE;
        $statusAprovado = InscricaoOferta::STATUS_APROVADO;
        $sql = "SELECT * FROM {$view} WHERE
status_inscricao = {$statusMatriculado}
AND status_aluno NOT IN ('{$statusDesistente}')
AND status_inscricao_oferta = '{$statusAprovado}'";

        $ofertasENotas = Yii::app()->db->createCommand($sql)->queryAll();

        $criteria = new CDbCriteria();
        $criteria->addCondition("status = 3 AND status_aluno NOT IN ('Desistente')");
        $inscricoes = Inscricao::model()->with('habilitacoes')->findAll($criteria);
        $inscricoesPorId = [];
        foreach ($inscricoes as $inscricao) {
            if ($inscricao->estaFormadoOuProntoParaFormar()) {
                $inscricoesPorId[$inscricao->id] = $inscricao;
            }
        }

        $tccs = Tcc::model()->findAll();
        $tccsPorInscricaoEHabilitacao = [];
        foreach ($tccs as $tcc) {
            $chave = "{$tcc->inscricao_id}_{$tcc->habilitacao_id}";
            $tccsPorInscricaoEHabilitacao[$chave] = $tcc;
        }

        $ofertas = Oferta::model()->with('docentes')->findAll();
        $ofertasPorId = [];
        foreach ($ofertas as $oferta) {
            $ofertasPorId[$oferta->id] = $oferta;
        }

        $texto = '';

        $i = 0;
        while ($i < count($ofertasENotas)) {

            $inscricao = $inscricoesPorId[$ofertasENotas[$i]['inscricao_id']] ?? null;
            if (empty($inscricao)) {
                $i++;
                continue;
            }

            $ofertasAcumuladas = [];

            for ($j = $i; $j < count($ofertasENotas); $j++) {

                if ($ofertasENotas[$i]['inscricao_id'] != $ofertasENotas[$j]['inscricao_id']) {
                    if (empty($ofertasAcumuladas)) continue;

                    if ($inscricao->ehAlunoDeEspecializacao()) {
                        foreach ($inscricao->habilitacoes as $habilitacao) {
                            $chaveTcc = $inscricao->id . '_' . $habilitacao->id;
                            $tcc = $tccsPorInscricaoEHabilitacao[$chaveTcc] ?? null;
                            $texto .= GeradorDeCertificado::gerarCertificadoTxt($inscricao, $habilitacao, $ofertasAcumuladas, $tcc);
                            $texto .= "\n\n-----\n\n";
                        }
                    } else {
                        $texto .= GeradorDeCertificado::gerarCertificadoTxt($inscricao, null, $ofertasAcumuladas, null);
                        $texto .= "\n\n-----\n\n";
                    }

                    $i = $j;
                    break;
                }

                $objetoOferta = [
                    'ano' => $ofertasENotas[$j]['ano'],
                    'mes' => $ofertasENotas[$j]['mes'],
                    'media' => $ofertasENotas[$j]['media'],
                    'frequencia' => $ofertasENotas[$j]['frequencia'],
                    'docentes' => [],
                    'componente' => [
                        'nome' => $ofertasENotas[$j]['componente_curricular'],
                    ],
                ];
                foreach ($ofertasPorId[$ofertasENotas[$j]['oferta_id']]->docentes as $docente) {
                    $objetoOferta['docentes'][] = [
                        'nomeCompleto' => $docente->nomeCompleto
                    ];
                };

                $ofertasAcumuladas[] = $objetoOferta;
            }
        }

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($texto));
        header("Content-type: application/text");
        header("Content-Disposition: attachment; filename=\"todos_certificados.txt\"");
        echo $texto;
        return;
    }

}
