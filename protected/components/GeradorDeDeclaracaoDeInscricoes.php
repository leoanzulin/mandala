<?php

class GeradorDeDeclaracaoDeInscricoes extends GeradorDePdf
{
    private $habilitacoesEscolhidas;

    /**
     * Gera o arquivo PDF das inscrições em ofertas realizadas por um aluno e o apresenta no navegador.
     *
     * @param Inscricao $inscricao
     */
    public static function gerar($inscricao, $contagemDeComponentes)
    {
        $exportador = new GeradorDeDeclaracaoDeInscricoes();
        $exportador->definirUrlBase('/modelos_declaracoes/inscricoes/');
        $exportador->carregarModelo("index.html");
        $exportador->carregarCabecalho('../cabecalho.html');
        $exportador->carregarFolhaDeEstilo(Yii::app()->getBasePath() . '/../css/exportador.css', true);

        $exportador->processarUrlBase();
        $exportador->processarCabecalho($inscricao);
        $exportador->processarHabilitacoes($inscricao);
        $exportador->processarPeriodos($inscricao);
        $exportador->processarResumo($inscricao, $contagemDeComponentes);

        $nomeArquivo = "inscricoes_{$inscricao->cpf}_edutec.pdf";
        $exportador->definirTitulo($nomeArquivo);
        $exportador->apresentarPdf($nomeArquivo);
    }

    private function processarCabecalho($inscricao)
    {
        $placeholders = array('{TITULO}', '{NOME}', '{CPF}', '{DATA}');
        $substituicoes = array(
            'Inscrições realizadas',
            $inscricao->nomeCompleto,
            $inscricao->cpf,
            date('d/m/Y'),
        );

        $this->html = str_replace($placeholders, $substituicoes, $this->html);
    }

    private function processarHabilitacoes($inscricao)
    {
        $htmlHabilitacoes = '';
        $this->habilitacoesEscolhidas = $inscricao->recuperarHabilitacoes();
        if (!empty($this->habilitacoesEscolhidas)) {
            $htmlHabilitacoes .= '<p><b>Habilitações escolhidas</b></p><ul>';
            foreach ($this->habilitacoesEscolhidas as $habilitacao) {
                $htmlHabilitacoes .= "<li>{$habilitacao}</li>";
            }
            $htmlHabilitacoes .= '</ul>';
        }
        $this->html = str_replace('{HABILITACOES}', $htmlHabilitacoes, $this->html);
    }

    private function processarPeriodos($inscricao)
    {
        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodos = $recuperadorDeOfertas
                ->daInscricao($inscricao->id)
                ->manterApenasInscritas()
                ->recuperar();

        $htmlPeriodos = '';

		foreach ($ofertasPorPeriodos as $periodo) {
            $htmlPeriodos .= $this->processarPeriodosCabecalhoPeriodo($periodo['ofertas'], $periodo);

            $htmlComponentes = '';
            foreach ($periodo['ofertas'] as $oferta) {
                $htmlComponentes .= $this->processarOferta($oferta, $inscricao);
            }

            $htmlPeriodos = str_replace('{COMPONENTES}', $htmlComponentes, $htmlPeriodos);
		}

        $this->html = str_replace('{PERIODOS_E_OFERTAS}', $htmlPeriodos, $this->html);
    }

    private function processarPeriodosCabecalhoPeriodo($ofertas, $periodoAtual)
    {
        $cargaHoraria = array_reduce($ofertas, function($soma, $oferta) {
            return $soma + $oferta['componente']['cargaHoraria'];
        });

        $placeholders = array('{ANO}', '{MES}', '{NUMERO_COMPONENTES}', '{CARGA_HORARIA}', '{LETRAS_HABILITACOES}');
        $letras = '';
        foreach ($this->habilitacoesEscolhidas as $habilitacao) {
            $letras .= "<th class=\"habilitacao\">{$habilitacao->letra}</th>";
        }
        $substituicoes = array($periodoAtual['ano'], $periodoAtual['mes'], count($ofertas), $cargaHoraria, $letras);

        $htmlCabecalhoPeriodo = empty($periodoAtual['projetoIntegrador'])
            ? $this->carregarArquivo('_periodo.html')
            : $this->carregarArquivo('_periodoProjetoIntegrador.html');
        return str_replace($placeholders, $substituicoes, $htmlCabecalhoPeriodo);
    }

    private function processarOferta($oferta, $inscricao)
    {
        $placeholders = array('{NOME}', '{PRIORIDADES}'
            // '{CARGA_HORARIA}',
        );
        $letras = '';
        foreach ($this->habilitacoesEscolhidas as $habilitacao) {
            if (!$this->ofertaEstaSelecionadaParaHabilitacao($oferta, $habilitacao)) {
                $letras .= "<td style=\"width: 30px\"></td>";
                continue;
            }
            $indiceDaHabilitacao = $this->recuperarIndice($oferta, $habilitacao->id);
            $classe = $oferta['componente']['prioridades'][$indiceDaHabilitacao]['classeCss'];
            $letra = $oferta['componente']['prioridades'][$indiceDaHabilitacao]['letra'];
            $letras .= "<td class=\"{$classe}\" style=\"text-align: center; width: 30px\">{$letra}</td>";
        }
        $substituicoes = array(
            $oferta['componente']['nome'],
            $letras,
            // $oferta['componente']['cargaHoraria'],
        );

        $htmlComponente = $this->carregarArquivo('_periodoComponente.html');
        return str_replace($placeholders, $substituicoes, $htmlComponente);
    }

    private function ofertaEstaSelecionadaParaHabilitacao($oferta, $habilitacao)
    {
        return !empty($oferta['selecionadaParaHabilitacoes'][$habilitacao->id]);
    }

    private function recuperarIndice($oferta, $habilitacaoId)
    {
        foreach ($oferta['componente']['prioridades'] as $indice => $prioridade)
            if ($prioridade['id'] == $habilitacaoId) return $indice;
        return -1;
    }

    private function processarResumo($inscricao, $contagemDeComponentes)
    {
        $this->html = str_replace('{NUMERO_TOTAL}', $contagemDeComponentes['total_geral'], $this->html);

        $totalDeComponentesFoiExcedido = false;
        if ($inscricao->tipo_curso != Inscricao::TIPO_CURSO_ESPECIALIZACAO) {
            $iconeV = '<img src="' . __DIR__ . '/../../images/v.png" style="width: 15px">';
            $iconeX = '<img src="' . __DIR__ . '/../../images/x.png" style="width: 15px">';
            $iconeTotal = array_key_exists('total_ok', $contagemDeComponentes) ? $iconeV : $iconeX;
            $this->html = str_replace('{ICONE_TOTAL_EXTENSAO_APERFEICOAMENTO}', $iconeTotal, $this->html);

            if (array_key_exists('total_excedido', $contagemDeComponentes)) {
                $totalDeComponentesFoiExcedido = true;
            }
        } else {
            $this->html = str_replace('{ICONE_TOTAL_EXTENSAO_APERFEICOAMENTO}', '', $this->html);
        }

        $htmlResumo = '';
        foreach ($this->habilitacoesEscolhidas as $numero => $habilitacao) {
            $htmlResumo .= $this->processarResumoHabilitacao($numero, $habilitacao, $contagemDeComponentes[$numero + 1]);
            if (array_key_exists('total_excedido', $contagemDeComponentes[$numero + 1])) {
                $totalDeComponentesFoiExcedido = true;
            }
        }

        $mensagemComponentesExcedidas = '<p style="font-weight: bold">Sua trilha tem mais componentes que o máximo previsto. Se desejar mantê-la assim, <span style="color: red">pode haver cobrança de parcelas extras</span>.</p><br>';
        $this->html = str_replace('{AVISO_TOTAL_PREVISTO_COMPONENTES_EXCEDIDOS}', $totalDeComponentesFoiExcedido ? $mensagemComponentesExcedidas : null, $this->html);
        $this->html = str_replace('{RESUMOS_POR_HABILITACAO}', $htmlResumo, $this->html);
    }

    private function processarResumoHabilitacao($numero, $habilitacao, $contagemDeComponentesDaHabilitacao)
    {
        $iconeV = '<img src="' . __DIR__ . '/../../images/v.png" style="width: 15px">';
        $iconeX = '<img src="' . __DIR__ . '/../../images/x.png" style="width: 15px">';

        $nomeHabilitacao = "{$habilitacao->nome} ({$habilitacao->letra})";
        $iconeNecessarias = array_key_exists('necessarias_ok', $contagemDeComponentesDaHabilitacao) ? $iconeV : $iconeX;
        $iconeObrigatorias = array_key_exists('optativas_ok', $contagemDeComponentesDaHabilitacao) ? $iconeV : $iconeX;
        $iconeLivres = $iconeV;
        $iconeTotal = array_key_exists('total_ok', $contagemDeComponentesDaHabilitacao) ? $iconeV : $iconeX;

        $placeholders = array(
            '{NOME_PRIORIDADE_NECESSARIA}',
            '{NOME_PRIORIDADE_OPTATIVA}',
            '{NOME_PRIORIDADE_LIVRE}',
            '{CONSTANTE_NUMERO_NECESSARIAS}',
            '{CONSTANTE_NUMERO_OPTATIVAS}',
            '{CONSTANTE_NUMERO_LIVRES}',
            '{ORDINAL}', '{NUMERO_MINIMO_PARA_ESTA_HABILITACAO}',
            //
            '{HABILITACAO}', '{NUMERO}', '{ICONE_V}',
            '{NUMERO_NECESSARIAS}', '{ICONE_NECESSARIAS}',
            '{NUMERO_OBRIGATORIAS}', '{ICONE_OBRIGATORIAS}',
            '{NUMERO_LIVRES}', '{ICONE_LIVRES}',
            '{NUMERO_TOTAL}', '{ICONE_TOTAL}',
            '{NOVOS_SE_NAO_FOR_PRIMEIRA_HABILITACAO}',
        );
        $substituicoes = array(
            Constantes::NOME_PRIORIDADE_NECESSARIA,
            Constantes::NOME_PRIORIDADE_OPTATIVA,
            Constantes::NOME_PRIORIDADE_LIVRE,
            Constantes::NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS,
            Constantes::NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS,
            Constantes::NUMERO_MINIMO_DE_COMPONENTES_LIVRES,
            Constantes::ORDINAIS($numero + 1), Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES($numero + 1),
            //
            $nomeHabilitacao, $numero, $iconeV,
            $contagemDeComponentesDaHabilitacao['necessarias'], $iconeNecessarias,
            $contagemDeComponentesDaHabilitacao['optativas'], $iconeObrigatorias,
            $contagemDeComponentesDaHabilitacao['livres'], $iconeLivres,
            $contagemDeComponentesDaHabilitacao['total'], $iconeTotal,
            $numero > 0 ? 'novos ' : '',
        );

        $htmlResumoHabilitacao = $this->carregarArquivo('_resumo.html');
        return str_replace($placeholders, $substituicoes, $htmlResumoHabilitacao);
    }

}
