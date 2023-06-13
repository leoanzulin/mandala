<?php

/**
 * Gera um certificado para um aluno específico em formato PDF
 */
class GeradorDeCertificado extends GeradorDePdf
{

    public static function deExtensao($id, $formatoTexto = false)
    {
        self::gerar($id, 'extensao', null, $formatoTexto);
    }

    public static function deAperfeicoamento($id, $formatoTexto = false)
    {
        self::gerar($id, 'aperfeicoamento', null, $formatoTexto);
    }

    public static function deEspecializacao($id, $formatoTexto = false)
    {
        self::gerar($id, 'especializacao', null, $formatoTexto);
    }

    public static function deEspecializacaoParaHabilitacao($id, $habilitacaoId, $formatoTexto = false)
    {
        self::gerar($id, 'especializacao', $habilitacaoId, $formatoTexto);
    }

    private static function gerar($id, $tipo, $habilitacaoId = null, $formatoTexto = false)
    {
        $exportador = new GeradorDeCertificado();
        $exportador->definirUrlBase('/modelos_certificados/');
        $exportador->carregarModelo("{$tipo}_modelo.html");
        $exportador->carregarFolhaDeEstilo(Yii::app()->getBasePath() . "/components/modelos_certificados/{$tipo}.css", true);

        $inscricao = Inscricao::model()->findByPk($id);
        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        // Primeiro checamos se as componentes foram selecionadas manualmente para esta habilitacao
        $ofertasPorPeriodos = $recuperadorDeOfertas
            ->daInscricao($inscricao->id)
            ->manterApenasInscritas()
            ->manterApenasOfertasAprovadas()
            // Conforme item 20 da lista de 18/01/2021, os componentes de cada habilitação irão seguir
            // as escolhas feitas na montagem da trilha, desconsiderando a tabela habilitacao_inscricao_oferta_certificados
            // ->escolherComponentesParaCertificados()
            ->recuperar();
        if (empty($ofertasPorPeriodos)) {
            die("Esse aluno não tem inscriçẽos em ofertas válidas ou não foi aprovado em nenhuma oferta");
        }
        $ofertas = $exportador->achatarOfertasPorPeriodos($ofertasPorPeriodos);
        if (!empty($habilitacaoId)) {
            $ofertas = $exportador->filtrarOfertasSelecionadasParaEstaHabilitacao($ofertas, $habilitacaoId, $tipo);
        }

        if (empty($ofertas)) {
            $recuperadorDeOfertas = new RecuperadorDeOfertas();
            $ofertasPorPeriodos = $recuperadorDeOfertas
                    ->daInscricao($inscricao->id)
                    ->manterApenasInscritas()
                    ->manterApenasOfertasAprovadas()
                    ->recuperar();

            $ofertas = $exportador->achatarOfertasPorPeriodos($ofertasPorPeriodos);
            // if (!empty($habilitacaoId)) {
            //     $ofertas = $exportador->filtrarOfertasParaEstaHabilitacao($ofertas, $habilitacaoId, $tipo);
            // }
        } else {
            // $ofertas = $exportador->achatarOfertasPorPeriodos($ofertasPorPeriodos);
            // if (!empty($habilitacaoId)) {
            //     $ofertas = $exportador->filtrarOfertasSelecionadasParaEstaHabilitacao($ofertas, $habilitacaoId, $tipo);
            // }
        }
        // $ofertas = $exportador->achatarOfertasPorPeriodos($ofertasPorPeriodos);
        // Não sei mais se isto ainda faz sentido. Estamos imprimindo todos os componentes em que o aluno se
        // inscreveu e foi aprovado, acho que é o mais lógico. Se ele tiver cursado mais de 23, ignoramos as
        // excedentes para não quebrar o layout do certificado.
        // if (!empty($habilitacaoId)) {
        //     $ofertas = $exportador->filtrarOfertasParaEstaHabilitacao($ofertas, $habilitacaoId, $tipo);
        // }

        $tcc = !empty($habilitacaoId)
            ? Tcc::model()->findByAttributes([
                'inscricao_id' => $inscricao->id,
                'habilitacao_id' => $habilitacaoId,
            ])
            : null;

        // GAMBIARRA: Isto deveria estar em outra classe
        if ($formatoTexto) {
            self::gerarEmForamtoTxt($inscricao, $ofertas, $tcc, $habilitacaoId);
            // self::gerarEmFormatoXls($inscricao, $ofertas, $tcc);
            return;
        }

        $exportador->processarUrlBase();
        $exportador->processarAluno($inscricao);
        $exportador->processarHabilitacoes($inscricao, $habilitacaoId);
        $exportador->processarOfertas($ofertas, $habilitacaoId);
        $exportador->processarPeriodoInicialEFinal($ofertasPorPeriodos);
        $exportador->processarDadosFinais($ofertas);
        $exportador->processarTcc($tcc);

        $nomeArquivo = "certificado_{$tipo}_edutec.pdf";
        $exportador->definirTitulo($nomeArquivo);

        $parametros = [
            'format' => 'A4',
            'orientation' => 'L',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ];
        $exportador->definirParametros($parametros);
        $exportador->apresentarPdf($nomeArquivo);
    }

    private static function gerarEmForamtoTxt($inscricao, $ofertas, $tcc, $habilitacaoId)
    {
        $habilitacao = Habilitacao::model()->findByPk($habilitacaoId);

        $texto = self::gerarCertificadoTxt($inscricao, $habilitacao, $ofertas, $tcc);

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($texto));
        header("Content-type: application/text");
        header("Content-Disposition: attachment; filename=\"certificado_{$inscricao->nomeCompleto}_{$inscricao->cpf}.txt\"");
        echo $texto;
        return;
    }

    public static function gerarCertificadoTxt($inscricao, $habilitacao, $ofertas, $tcc)
    {
        // TODO: Deveria ser mudado para contar a carga_horaria da oferta ao invés de multiplicar por 20
        // Isto só será um problema se eventualmente algum componente no futuro tiver carga horária diferente de 20
        $cargaHoraria = 20 * count($ofertas);
        [ $anoInicio, $anoConclusao ] = self::recuperarAnoInicioEConclusao($ofertas);
        $nomeHabilitacao = !empty($habilitacao) ? ': ' . $habilitacao->nome : '';

        $texto =
"Certificamos que {$inscricao->nomeCompleto}, CPF n° {$inscricao->cpf}, concluiu o
Curso de {$inscricao->tipoDeCursoPorExtenso()} em Educação e Tecnologias{$nomeHabilitacao},
oferecido pelo Grupo Horizonte (Grupo de Estudos e Pesquisas emEducação,
Tecnologias e Linguagens) em colaboração com a SEaD (Secretaria Geral de
Educação a Distância) da Universidade Federal de São Carlos, no período de
{$anoInicio} a {$anoConclusao}, em um total de {$cargaHoraria} horas/aula.\n\n";

        $texto .= "COMPONENTES:\n";
        $somatorioDeNotas = 0;
        $somatorioDeFrequencia = 0;
        foreach ($ofertas as $oferta) {
            $nomesDocentes = [];
            foreach ($oferta['docentes'] as $docente) {
                $nomesDocentes[] = $docente['nomeCompleto'];
            }
            $nomesDocentes = implode(';', $nomesDocentes);
            $texto .= "{$oferta['componente']['nome']} - Docente(s) {$nomesDocentes} - Média {$oferta['media']} - Frequência {$oferta['frequencia']}\n";
            $somatorioDeNotas += $oferta['media'];
            $somatorioDeFrequencia += $oferta['frequencia'];
        }
        $texto .= "\n";
        $mediaFinal = round($somatorioDeNotas / count($ofertas), 1);
        $frequenciaFinal = ceil($somatorioDeFrequencia / count($ofertas));

        if ($inscricao->ehAlunoDeEspecializacao()) {
            $titulo = $tcc ? $tcc->titulo : '-';
            $orientador = $tcc ? $tcc->recuperarOrientador() : '';
            $processo = $tcc ? $tcc->recuperarProcessoProExWeb() : '';
            $texto .=
"Trabalho de Conclusão de Curso: {$titulo}
Orientador/a: {$orientador}
Processo Proexweb: {$processo}\n";
        }

        $texto .=
"Frequência final: {$frequenciaFinal}
Média final: {$mediaFinal}";

        return $texto;
    }

    private static function recuperarAnoInicioEConclusao($ofertas)
    {
        $menorAno = 9999;
        $maiorAno = 1;
        foreach ($ofertas as $oferta) {
            if (!empty($oferta['projetoIntegrador'])) continue;

            if ($oferta['ano'] < $menorAno) {
                $menorAno = $oferta['ano'];
            }
            if ($oferta['ano'] > $maiorAno) {
                $maiorAno = $oferta['ano'];
            }
        }
        return [ $menorAno, $maiorAno ];
    }

    private static function gerarEmFormatoXls($inscricao, $ofertas, $tcc)
    {
        $somatorioDeNotas = 0;
        $somatorioDeFrequencia = 0;

        $cabecalho = array('componente', 'media', 'frequencia');
        $dados = [];
        foreach ($ofertas as $oferta) {
            $dados[] = [
                'componente' => $oferta['componente']['nome'],
                'media' => $oferta['media'],
                'frequencia' => $oferta['frequencia'],
            ];
            $somatorioDeNotas += $oferta['media'];
            $somatorioDeFrequencia += $oferta['frequencia'];
        }
        $mediaFinal = round($somatorioDeNotas / count($ofertas), 1);
        $frequenciaFinal = round($somatorioDeFrequencia / count($ofertas), 1);

        $dados[] = [ 'componente' => '--------------------------------------------------------', 'media' => '', 'frequencia' => '' ];
        $dados[] = [ 'componente' => 'Média final', 'media' => $mediaFinal, 'frequencia' => '' ];
        $dados[] = [ 'componente' => 'Frequência final', 'media' => $frequenciaFinal, 'frequencia' => '' ];

        if ($inscricao->ehAlunoDeEspecializacao()) {
            $dados[] = [ 'componente' => '--------------------------------------------------------', 'media' => '', 'frequencia' => '' ];
            $dados[] = [ 'componente' => 'TCC', 'media' => '', 'frequencia' => '' ];
            if (!empty($tcc)) {
                $dados[] = [ 'componente' => 'Título', 'media' => $tcc->titulo, 'frequencia' => '' ];
                $dados[] = [ 'componente' => 'Orientadores', 'media' => $tcc->orientadores, 'frequencia' => '' ];
                $dados[] = [ 'componente' => 'Aprovado?', 'media' => $tcc->aprovado ? 'SIM' : '', 'frequencia' => '' ];
            } else {
                $dados[] = [ 'componente' => 'Ainda não finalizado', 'media' => '', 'frequencia' => '' ];
            }
        }

        Exportador::exportar($cabecalho, $dados, "certificado_{$inscricao->cpf}", 'xls');
    }

    private function achatarOfertasPorPeriodos($ofertasPorPeriodos)
    {
        $ofertas = [];
        foreach ($ofertasPorPeriodos as $periodo) {
            foreach ($periodo['ofertas'] as $oferta) {
                $ofertas[] = $oferta;
            }
        }
        return $ofertas;
    }

    private function filtrarOfertasParaEstaHabilitacao($ofertas, $habilitacaoId, $tipoDeCurso)
    {
        $ofertasFiltradas = [];
        $limiteTotal = $this->quantidadeDeComponentesPara($tipoDeCurso);
        $numeroDeComponentesSelecionadas = 0;
        $numeroDeComponentesOpcionaisSelecionadas = 0;

        foreach ($ofertas as &$oferta) {
            $prioridade = $this->prioridadePara($oferta['componente'], $habilitacaoId);
            if ($prioridade == 0) {
                $oferta['marcada'] = true;
                $ofertasFiltradas[] = $oferta;
                $numeroDeComponentesSelecionadas++;
            } else if ($prioridade == 1 && $numeroDeComponentesOpcionaisSelecionadas < 10) {
                $oferta['marcada'] = true;
                $ofertasFiltradas[] = $oferta;
                $numeroDeComponentesSelecionadas++;
                $numeroDeComponentesOpcionaisSelecionadas++;
            }

            if ($numeroDeComponentesSelecionadas == $limiteTotal) {
                return $ofertasFiltradas;
            }
        }

        foreach ($ofertas as $oferta) {
            if (!empty($oferta['marcada'])) continue;

            $prioridade = $this->prioridadePara($oferta['componente'], $habilitacaoId);
            if ($prioridade == 2 || $prioridade == 1 && !empty($oferta['marcada'])) {
                $ofertasFiltradas[] = $oferta;
                $numeroDeComponentesSelecionadas++;
            }

            if ($numeroDeComponentesSelecionadas == $limiteTotal) {
                return $ofertasFiltradas;
            }
        }

        return $ofertasFiltradas;
    }

    private function quantidadeDeComponentesPara($tipoDeCurso)
    {
        // TODO: COLOCAR CONSTANTES AQUI
        if ($tipoDeCurso === 'especializacao') return 21;
        if ($tipoDeCurso === 'aperfeicoamento') return 10;
        if ($tipoDeCurso === 'extensao') return 5;
    }

    private function prioridadePara($componente, $habilitacaoId)
    {
        foreach ($componente['prioridades'] as $prioridade) {
            if ($prioridade['id'] == $habilitacaoId) {
                return $prioridade['prioridade'];
            }
        }
    }

    private function filtrarOfertasSelecionadasParaEstaHabilitacao($ofertas, $habilitacaoId, $tipoDeCurso)
    {
        $ofertasFiltradas = [];
        foreach ($ofertas as $oferta) {
            if ($oferta['selecionadaParaHabilitacoes'][$habilitacaoId]) {
                $ofertasFiltradas[] = $oferta;
            }
        }
        return $ofertasFiltradas;
    }

    private function processarAluno($aluno)
    {
        $placeholders = array('{NOME}', '{IDENTIDADE}', '{CPF}', '{DATA}');
        $substituicoes = array(
            $aluno->nomeCompleto,
            $aluno->identidade,
            $this->formatarCpf($aluno->cpf),
            $this->gerarDataPorExtenso(),
        );

        $this->html = str_replace($placeholders, $substituicoes, $this->html);
    }

    private function processarHabilitacoes($aluno, $habilitacaoId)
    {
        $nomeHabilitacao = '';
        foreach ($aluno->recuperarHabilitacoes() as $habilitacao) {
            if ($habilitacao->id == $habilitacaoId) {
                $nomeHabilitacao = $habilitacao->nome;
                break;
            }
        }
        $this->html = str_replace('{HABILITACOES}', $nomeHabilitacao, $this->html);
    }

    private function formatarCpf($cpf)
    {
        $parte1 = substr($cpf, 0, 3);
        $parte2 = substr($cpf, 3, 3);
        $parte3 = substr($cpf, 6, 3);
        $digitos = substr($cpf, 9, 2);
        return "{$parte1}.{$parte2}.{$parte3}-{$digitos}";
    }

    private function gerarDataPorExtenso()
    {
        $dia = (int) date('d');
        $ano = (int) date('Y');
        $mes = (int) date('n');
        $nomeMeses = array('', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro');
        $mesAtual = $nomeMeses[$mes];
        return "{$dia} de {$mesAtual} de {$ano}";
    }

    private function processarOfertas($ofertas)
    {
        $htmlOfertas = '';
        foreach ($ofertas as $i => $oferta) {
            // TODO: VERIFICAR O QUE FAZER QUANDO HÁ COMPONENTES DEMAIS, ESTOU SÓ IGNORANDO ELAS AQUI
            if ($i > 22) continue;
            $htmlOfertas .= $this->processarOferta($oferta);
        }
        $this->html = str_replace('{COMPONENTES}', $htmlOfertas, $this->html);
    }

    private function processarOferta($oferta)
    {
        $htmlOferta = '<tr>';
        $htmlOferta .= '<td class="coluna-disciplina">' . $oferta['componente']['nome'] . '</td>';
        $htmlOferta .= '<td class="coluna-docentes">' . $this->processarDocentes($oferta) . '</td>';
        $htmlOferta .= '<td class="nota-carga-horaria">' . $oferta['media'] . '</td>';
        $htmlOferta .= '<td class="nota-carga-horaria">' . $oferta['componente']['cargaHoraria'] . '</td>';
        $htmlOferta .= '</tr>';
        return $htmlOferta;
    }

    private function processarDocentes($oferta)
    {
        $docentes = array();
        foreach ($oferta['docentes'] as $docente) {
            $docentes[] = $docente['nomeCompleto'];
        }
        return implode('; ', $docentes);
    }

    private function processarPeriodoInicialEFinal($ofertasPorPeriodos)
    {
        $menorPeriodo = "9999_12";
        $maiorPeriodo = "0001_01";
        foreach ($ofertasPorPeriodos as $periodo) {
            if (!empty($periodo['projetoIntegrador'])) continue;

            $mes = CalendarioHelper::adicionarZeros($periodo['mes']);
            $periodoString = "{$periodo['ano']}_{$mes}";
            if ($periodoString > $maiorPeriodo) {
                $maiorPeriodo = $periodoString;
            }
            if ($periodoString < $menorPeriodo) {
                $menorPeriodo = $periodoString;
            }
        }
        $periodoInicial = CalendarioHelper::transformarStringPeriodo($menorPeriodo);
        $periodoFinal = CalendarioHelper::transformarStringPeriodo($maiorPeriodo);

        $placeholders = array('{PERIODO_INICIO}', '{PERIODO_FIM}');
        $substituicoes = array($periodoInicial, $periodoFinal);
        $this->html = str_replace($placeholders, $substituicoes, $this->html);
    }

    private function processarDadosFinais($ofertas)
    {
        $numeroDeOfertas = 0;
        $somatorioDeNotas = 0;
        $somatorioDeFrequencia = 0;
        $cargaHorariaTotal = 0;

        foreach ($ofertas as $oferta) {
            $somatorioDeNotas += $oferta['media'];
            $somatorioDeFrequencia += $oferta['frequencia'];
            $cargaHorariaTotal += $oferta['componente']['cargaHoraria'];
            $numeroDeOfertas++;
        }

        $mediaFinal = round($somatorioDeNotas / $numeroDeOfertas, 1);
        $frequenciaFinal = round($somatorioDeFrequencia / $numeroDeOfertas, 1);

        // Segundo mensagem do Glauber via Whatsapp em 07/04/2020
        $conceito = 'REPROVADO';
        if ($mediaFinal >= 8.5) $conceito = 'A';
        else if ($mediaFinal >= 7) $conceito = 'B';

        $placeholders = array('{CONCEITO}', '{FREQUENCIA}', '{MEDIA_FINAL}', '{CARGA_HORARIA_TOTAL}');
        $substituicoes = array($conceito, $frequenciaFinal, $mediaFinal, $cargaHorariaTotal);
        $this->html = str_replace($placeholders, $substituicoes, $this->html);
    }

    private function processarTcc($tcc)
    {
        $placeholders = array('{TCC_TITULO}', '{TCC_ORIENTADOR}', '{TCC_NOTA}');
        $substituicoes = !empty($tcc)
            ? array($tcc->titulo, $tcc->orientadores, $tcc->aprovado ? 'APROVADO' : '')
            : array('', '', '');
        $this->html = str_replace($placeholders, $substituicoes, $this->html);
    }

}
