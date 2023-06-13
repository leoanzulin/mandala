<?php

class GeradorDeHistorico extends GeradorDePdf
{

    /**
     * Gera o arquivo PDF do histórico de um aluno e o apresenta no navegador.
     * Se o parâmetro $limpo estiver setado, apenas a última oferta cursada de
     * cada componente é mantida (ofertas reprovadas são removidas).
     * TODO: Os geradores não deveriam fazer a busca dos dados, mas apenas
     *       recebê-los externamente.
     *
     * @param Inscricao $inscricao
     */
    public static function gerar($inscricao, $limpo = false)
    {
        $exportador = new GeradorDeHistorico();
        $exportador->definirUrlBase('/modelos_declaracoes/historico/');
        $exportador->carregarModelo('historico.html');
        $exportador->carregarCabecalho('../cabecalho.html');
        $exportador->carregarFolhaDeEstilo(Yii::app()->getBasePath() . '/../css/exportador.css', true);

        $exportador->processarUrlBase();
        $exportador->processarCabecalho($inscricao);
        $exportador->processarHabilitacoes($inscricao);
        $exportador->processarPeriodos($inscricao, $limpo);

        $nomeArquivo = "historico_{$inscricao->cpf}_edutec.pdf";
        $exportador->definirTitulo($nomeArquivo);
        $exportador->apresentarPdf($nomeArquivo);
    }

    private function processarCabecalho($inscricao)
    {
        $placeholders = array('{NOME}', '{CPF}', '{DATA}');
        $substituicoes = array(
            $inscricao->nomeCompleto,
            $inscricao->cpf,
            date('d/m/Y'),
        );

        $this->html = str_replace($placeholders, $substituicoes, $this->html);
    }

    private function processarHabilitacoes($inscricao)
    {
        $htmlHabilitacoes = '';
        $htmlHabilitacoesCabecalho = '';
        foreach ($inscricao->recuperarHabilitacoes() as $i => $habilitacao) {
            $htmlHabilitacoes .= "<li>{$habilitacao}</li>";
            $htmlHabilitacoesCabecalho .= '<th>H' . ($i + 1) . '</th>';
        }
        $this->html = str_replace('{HABILITACOES}', $htmlHabilitacoes, $this->html);
        $this->html = str_replace('{PRIORIDADES}', $htmlHabilitacoesCabecalho, $this->html);
    }

    private function processarPeriodos($inscricao, $limpo)
    {
        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        if ($limpo) {
            $ofertasPorPeriodos = $recuperadorDeOfertas
                    ->daInscricao($inscricao->id)
                    ->manterApenasInscritas()
                    ->manterApenasOfertasPassadasEPresentes()
                    ->manterApenasOfertasAprovadas()
                    ->recuperar();
        } else {
            $ofertasPorPeriodos = $recuperadorDeOfertas
                    ->daInscricao($inscricao->id)
                    ->manterApenasInscritas()
                    ->manterApenasOfertasPassadasEPresentes()
                    ->recuperar();
        }

        $htmlPeriodos = '';

		foreach ($ofertasPorPeriodos as $periodo) {
            $htmlPeriodos .= $this->processarPeriodosCabecalhoPeriodo($periodo, $inscricao->habilitacoes);
            $htmlComponentes = '';
            foreach ($periodo['ofertas'] as $oferta) {
                $htmlComponentes .= $this->processarOferta($oferta, $inscricao);
            }
            $htmlPeriodos = str_replace('{COMPONENTES}', $htmlComponentes, $htmlPeriodos);
		}

        $this->html = str_replace('{PERIODOS_E_OFERTAS}', $htmlPeriodos, $this->html);
    }

    private function processarPeriodosCabecalhoPeriodo($periodo, $habilitacoes)
    {
        $colspan = 5 + count($habilitacoes);
        $placeholders = array('{COLSPAN}', '{ANO}', '{MES}');
        $substituicoes = array($colspan, $periodo['ano'], $periodo['mes']);

        $htmlCabecalhoPeriodo = $this->carregarArquivo('_periodo.html');
        return str_replace($placeholders, $substituicoes, $htmlCabecalhoPeriodo);
    }

    private function processarOferta($oferta, $inscricao)
    {
        $htmlPrioridades = '';
        foreach ($inscricao->habilitacoes as $habilitacao) {
            $htmlPrioridades .= '<td>' . $this->recuperarPrioridadeDaOfertaParaHabilitacao($oferta, $habilitacao) . '</td>';
        }

        $placeholders = array('{NOME}',
            '{PRIORIDADES}',
            '{NOTA_VIRTUAL}', '{NOTA_PRESENCIAL}', '{MEDIA}', '{FREQUENCIA}'
        );
        $substituicoes = array(
            $oferta['componente']['nome'],
            $htmlPrioridades,
            $oferta['nota_virtual'], $oferta['nota_presencial'], $oferta['media'], $oferta['frequencia']
        );

        $htmlComponente = $this->carregarArquivo('_periodoComponente.html');
        return str_replace($placeholders, $substituicoes, $htmlComponente);
    }

    private function recuperarPrioridadeDaOfertaParaHabilitacao($oferta, $habilitacao) {
        foreach ($oferta['componente']['prioridades'] as $prioridadeHabilitacao) {
            if ($prioridadeHabilitacao['id'] == $habilitacao->id) {
                return $prioridadeHabilitacao['letra'];
            }
        }
    }

}
