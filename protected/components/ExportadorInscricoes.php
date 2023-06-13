<?php

class ExportadorInscricoes
{

    public static function relatorio($inscricao, $tipo)
    {
        self::verificarSeTipoEhValido($tipo);

        $mpdf = Yii::app()->ePdf->mpdf();

        $stylesheet = file_get_contents(Yii::app()->createAbsoluteUrl('/') . '/css/edtec.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $html = self::incluirTemplate("{$tipo}.html");
        $html = self::processarCabecalho($html, $inscricao);
        $html = self::processarHabilitacoes($html, $inscricao);
        $html = self::processarPeriodos($html, $inscricao, $tipo);
        $html = self::processarResumo($html, $inscricao, $tipo);

        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    private static function verificarSeTipoEhValido($tipo)
    {
        $tiposValidos = array('simulador', 'inscricoes');
        if (!in_array($tipo, $tiposValidos)) {
            throw new Exception("Tipo de relatório inválido: {$tipo}");
        }
        return true;
    }

    private static function incluirTemplate($arquivo)
    {
        ob_start();
        include 'relatorios/inscricoes/' . $arquivo;
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

    private static function processarPeriodos($html, $inscricao, $tipo)
    {
        $componentesPorPeriodos = $inscricao->componentesPorPeriodo($tipo);

        $htmlPeriodos = '';

        foreach ($componentesPorPeriodos as $periodo => $componentes) {

            $htmlPeriodos .= self::processarPeriodosCabecalhoPeriodo($componentes, $periodo);

            $htmlComponentes = '';
            if (empty($componentes)) {
                $htmlComponentes = '<tr><td colspan="7"><i>Não há nenhuma componente neste período</i></td></tr>';
            } else {
                foreach ($componentes as $componente) {
                    $htmlComponentes .= self::processarComponente($componente);
                }
            }

            $htmlPeriodos = str_replace('{COMPONENTES}', $htmlComponentes, $htmlPeriodos);
        }

        return str_replace('{PERIODOS_E_OFERTAS}', $htmlPeriodos, $html);
    }

    private static function processarPeriodosCabecalhoPeriodo($componentes, $periodoAtual)
    {
        $partes = explode('_', $periodoAtual);
        $cargaHoraria = array_reduce($componentes, function($soma, $componente) {
            return $soma + $componente->carga_horaria;
        });

        $placeholders = array('{ANO}', '{MES}', '{NUMERO_COMPONENTES}', '{CARGA_HORARIA}');
        $substituicoes = array($partes[0], $partes[1], count($componentes), $cargaHoraria,);

        $htmlCabecalhoPeriodo = self::incluirTemplate('_simuladorPeriodo.html');
        return str_replace($placeholders, $substituicoes, $htmlCabecalhoPeriodo);
    }

    private static function processarComponente($componente)
    {
        $prioridades = $componente->prioridades();

        $placeholders = array('{NOME}',
            '{CLASSE1}', '{CLASSE2}', '{CLASSE3}', '{CLASSE4}', '{CLASSE5}',
            '{PRIORIDADE1}', '{PRIORIDADE2}', '{PRIORIDADE3}', '{PRIORIDADE4}', '{PRIORIDADE5}',
            '{CARGA_HORARIA}',
        );
        $substituicoes = array(
            $componente->nome,
            $prioridades['classes'][1], $prioridades['classes'][2], $prioridades['classes'][3], $prioridades['classes'][4], $prioridades['classes'][5],
            $prioridades['prioridadesLetra'][1], $prioridades['prioridadesLetra'][2], $prioridades['prioridadesLetra'][3], $prioridades['prioridadesLetra'][4], $prioridades['prioridadesLetra'][5],
            $componente->carga_horaria,
        );

        $htmlComponente = self::incluirTemplate('_simuladorPeriodoComponente.html');
        return str_replace($placeholders, $substituicoes, $htmlComponente);
    }

    private static function processarResumo($html, $inscricao, $tipo)
    {
        $contagemDeComponentes = ContadorDeComponentes::contar($inscricao, $tipo);

        $html = str_replace('{NUMERO_TOTAL}', $contagemDeComponentes['total_geral'], $html);

        $htmlResumo = '';
        foreach ($inscricao->recuperarHabilitacoes() as $numero => $habilitacao) {
            $htmlResumo .= self::processarResumoHabilitacao($numero, $habilitacao, $contagemDeComponentes[$numero]);
        }

        return str_replace('{RESUMOS_POR_HABILITACAO}', $htmlResumo, $html);
    }

    private static function processarResumoHabilitacao($numero, $habilitacao, $contagemDeComponentesDaHabilitacao)
    {
        $ordinais = Constantes::ORDINAIS();
        $numeroMinimoParaCumprirEstaHabilitacao = Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES($numero);
        $iconeV = CHtml::image(Yii::app()->baseUrl . '/images/v.png');
        $iconeX = CHtml::image(Yii::app()->baseUrl . '/images/x.png');

        $nomeHabilitacao = "{$habilitacao->nome} ({$habilitacao->letra})";
        $iconeNecessarias = array_key_exists(Constantes::LETRA_PRIORIDADE_NECESESARIA . '_OK', $contagemDeComponentesDaHabilitacao) ? $iconeV : $iconeX;
        $iconeObrigatorias = array_key_exists(Constantes::LETRA_PRIORIDADE_OPTATIVA . '_OK', $contagemDeComponentesDaHabilitacao) ? $iconeV : $iconeX;
        $iconeLivres = array_key_exists(Constantes::LETRA_PRIORIDADE_LIVRE . '_OK', $contagemDeComponentesDaHabilitacao) ? $iconeV : $iconeX;
        $iconeTotal = array_key_exists('total_OK', $contagemDeComponentesDaHabilitacao) ? $iconeV : $iconeX;

        $placeholders = array(
            '{CONSTANTE_NUMERO_NECESSARIAS}',
            '{CONSTANTE_NUMERO_NECESSARIAS_MENOS_UM}',
            '{CONSTANTE_NUMERO_OPTATIVAS}',
            '{CONSTANTE_NUMERO_LIVRES}',
            '{ORDINAL}', '{NUMERO_MINIMO_PARA_ESTA_HABILITACAO}',
            //
            '{HABILITACAO}', '{NUMERO}', '{ICONE_V}',
            '{NUMERO_NECESSARIAS}', '{ICONE_NECESSARIAS}',
            '{NUMERO_OBRIGATORIAS}', '{ICONE_OBRIGATORIAS}',
            '{NUMERO_LIVRES}', '{ICONE_LIVRES}',
            '{NUMERO_TOTAL}', '{ICONE_TOTAL}',
        );
        $substituicoes = array(
            Constantes::NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS,
            Constantes::NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS - 1,
            Constantes::NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS,
            Constantes::NUMERO_MINIMO_DE_COMPONENTES_LIVRES,
            $ordinais[$numero], $numeroMinimoParaCumprirEstaHabilitacao,
            //
            $nomeHabilitacao, $numero, $iconeV,
            $contagemDeComponentesDaHabilitacao[Constantes::LETRA_PRIORIDADE_NECESESARIA], $iconeNecessarias,
            $contagemDeComponentesDaHabilitacao[Constantes::LETRA_PRIORIDADE_OPTATIVA], $iconeObrigatorias,
            $contagemDeComponentesDaHabilitacao[Constantes::LETRA_PRIORIDADE_LIVRE], $iconeLivres,
            $contagemDeComponentesDaHabilitacao['total'], $iconeTotal,
        );

        $htmlResumoHabilitacao = self::incluirTemplate('_simuladorResumo.html');
        return str_replace($placeholders, $substituicoes, $htmlResumoHabilitacao);
    }

}
