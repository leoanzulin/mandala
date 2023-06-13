<?php

class ExportadorHistorico
{

    public static function relatorio($inscricao)
    {
        $mpdf = Yii::app()->ePdf->mpdf();

        $stylesheet = file_get_contents(Yii::app()->createAbsoluteUrl('/') . '/css/edtec.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $html = self::incluirTemplate("historico.html");
        $html = self::processarCabecalho($html, $inscricao);
        $html = self::processarHabilitacoes($html, $inscricao);
        $html = self::processarPeriodos($html, $inscricao);

        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    private static function incluirTemplate($arquivo)
    {
        ob_start();
        include 'relatorios/historico/' . $arquivo;
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }

    private static function processarCabecalho($html, $inscricao)
    {
        $placeholders = array('{IMAGEM_CABECALHO}', '{NOME}', '{CPF}', '{DATA}');
        $substituicoes = array(
            CHtml::image(Yii::app()->theme->baseUrl . '/img/logo_menor.png'),
            $inscricao->nomeCompleto,
            $inscricao->cpf,
            date('d/m/Y'),
        );

        return str_replace($placeholders, $substituicoes, $html);
    }

    private static function processarHabilitacoes($html, $inscricao)
    {
        $htmlHabilitacoes = '';
        foreach ($inscricao->recuperarHabilitacoes() as $habilitacao) {
            $htmlHabilitacoes .= "<li>{$habilitacao}</li>";
        }
        return str_replace('{HABILITACOES}', $htmlHabilitacoes, $html);
    }

    private static function processarPeriodos($html, $inscricao)
    {
        $ofertasPorPeriodos = Oferta::model()->inscritasPorPeriodoParaAInscricao($inscricao->id);

        $htmlPeriodos = '';

        foreach ($ofertasPorPeriodos as $periodo) {

            if (self::periodoEstaNoFuturo($periodo)) {
                continue;
            }

            $htmlPeriodos .= self::processarPeriodosCabecalhoPeriodo($periodo);

            $htmlComponentes = '';
            foreach ($periodo['ofertas'] as $oferta) {
                $htmlComponentes .= self::processarOferta($oferta);
            }

            $htmlPeriodos = str_replace('{COMPONENTES}', $htmlComponentes, $htmlPeriodos);
        }

        return str_replace('{PERIODOS_E_OFERTAS}', $htmlPeriodos, $html);
    }

    private static function periodoEstaNoFuturo($periodo)
    {
        $mesesAtual = date('Y') * 12 + date('n');
        $mesesDoPeriodo = $periodo['ano'] * 12 + $periodo['mes'];
        return $mesesDoPeriodo > $mesesAtual;
    }

    private static function processarPeriodosCabecalhoPeriodo($periodo)
    {
        $placeholders = array('{ANO}', '{MES}');
        $substituicoes = array($periodo['ano'], $periodo['mes']);

        $htmlCabecalhoPeriodo = self::incluirTemplate('_historicoPeriodo.html');
        return str_replace($placeholders, $substituicoes, $htmlCabecalhoPeriodo);
    }

    private static function processarOferta($oferta)
    {
        $placeholders = array('{NOME}',
            '{NOTA_VIRTUAL}', '{NOTA_PRESENCIAL}', '{MEDIA}', '{FREQUENCIA}'
        );
        $substituicoes = array(
            $oferta['componente_curricular'],
            $oferta['nota_virtual'], $oferta['nota_presencial'], $oferta['media'], $oferta['frequencia']
        );

        $htmlComponente = self::incluirTemplate('_historicoPeriodoComponente.html');
        return str_replace($placeholders, $substituicoes, $htmlComponente);
    }

}
