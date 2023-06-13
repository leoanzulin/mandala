<?php

/**
 * Gera atestados em formato PDF
 */
class GeradorDeAtestado extends GeradorDePdf
{

    public static function deMatricula($id)
    {
        self::gerar($id, 'matricula');
    }

    public static function deEspecializacao($id, $habilitacaoId)
    {
        self::gerar($id, 'especializacao', $habilitacaoId);
    }

    public static function deExtensao($id)
    {
        self::gerar($id, 'extensao');
    }

    public static function deAperfeicoamento($id)
    {
        self::gerar($id, 'aperfeicoamento');
    }

    public static function deorientacao($id, $tccId)
    {
        self::gerar($id, 'orientacao', null, $tccId);
    }

    public static function deMembroDeBanca($id, $tccId)
    {
        self::gerar($id, 'membroDeBanca', null, $tccId);
    }

    public static function deDocencia($id, $ofertaId)
    {
        self::gerar($id, 'docencia', null, null, $ofertaId);
    }

    public static function deTutoria($id, $ofertaId)
    {
        self::gerar($id, 'tutoria', null, null, $ofertaId);
    }

    private static function gerar($id, $tipo, $habilitacaoId = null, $tccId = null, $ofertaId = null)
    {
        $exportador = new GeradorDeAtestado();
        $exportador->definirUrlBase('/modelos_atestados_pdf/');
        $exportador->carregarModelo("atestado_modelo.html");
        $exportador->carregarFolhaDeEstilo(Yii::app()->getBasePath() . "/components/modelos_atestados_pdf/atestado.css", true);

        if ($tipo === 'matricula') {

            $inscricao = Inscricao::model()->findByPk($id);
            [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();

            $texto = "Atestamos, para os devidos fins, que {$inscricao->nomeCompleto}, portador(a) do CPF: {$inscricao->cpf},
            RA: {$inscricao->ra} está regularmente matriculado(a) no período letivo de {$mes}/{$ano}, no curso de Educação e
            Tecnologias, em nível de {$inscricao->tipoDeCursoPorExtenso()}, oferecido pela Universidade Federal de São Carlos (UFSCar),
            por meio do Grupo Horizonte (Grupo de Estudos e Pesquisas sobre Inovação em Educação, Tecnologias e Linguagens),
            conforme do processo: {PROCESSO} registrado na PROEX-UFSCar (Pró-Reitoria de Extensão)";

            $placeholders = ['{TITULO}', '{TEXTO}', '{DATA}'];
            $substituicoes = ['Atestado de matrícula', $texto, CalendarioHelper::dataPorExtenso()];
            $exportador->html = str_replace($placeholders, $substituicoes, $exportador->html);

        } else if ($tipo === 'especializacao') {

            $inscricao = Inscricao::model()->findByPk($id);
            [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();
            $habilitacao = Habilitacao::model()->findByPk($habilitacaoId);
            $inscricaoHabilitacao = InscricaoHabilitacao::model()->findByAttributes([
                'inscricao_id' => $id,
                'habilitacao_id' => $habilitacaoId,
            ]);

            $texto = "Declaramos, para os devidos fins, que {$inscricao->nomeCompleto}, portador(a) do CPF: {$exportador->formatarCpf($inscricao->cpf)},
            RA: {$inscricao->ra} concluiu o curso de Especialização em Educação e Tecnologias: {$habilitacao->nome} (400 horas),
            realizado no período de {PERIODO_INICIO} a {PERIODO_FIM} (conforme histórico em anexo), oferecido pela Universidade Federal de São Carlos
            (UFSCar), por meio do Grupo Horizonte (Grupo de Estudos e Pesquisas sobre Inovação em Educação, Tecnologias e Linguagens),
            conforme processo: {PROCESSO} registrado na PROEX-UFSCar (Pró-Reitoria de Extensão).
            <span style=\"text-decoration: underline\">Declaramos também que o(a) estudante está aguardando a emissão do certificado</span>.";

            $placeholders = ['{TITULO}', '{TEXTO}', '{DATA}', '{PROCESSO}'];
            $substituicoes = ['Atestado de conclusão de curso', $texto, CalendarioHelper::dataPorExtenso(), $inscricaoHabilitacao->processo_proex];
            $exportador->html = str_replace($placeholders, $substituicoes, $exportador->html);

            $ofertas = $exportador->recuperarOfertasPara($id);
            $exportador->processarPeriodoInicialEFinal($ofertas);

        } else if ($tipo === 'extensao') {

            $inscricao = Inscricao::model()->findByPk($id);
            [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();

            $texto = "ATESTAMOS, para os devidos fins, que {$inscricao->nomeCompleto}, portador(a) do CPF:
            {$exportador->formatarCpf($inscricao->cpf)}, RA: {$inscricao->ra} concluiu o curso de Extensão em Educação e Tecnologias (100
            horas), realizado no período de {PERIODO_INICIO} a {PERIODO_FIM} (conforme histórico em anexo), oferecido pela
            Universidade Federal de São Carlos (UFSCar), por meio do Grupo Horizonte (Grupo de Estudos e
            Pesquisas sobre Inovação em Educação, Tecnologias e Linguagens), conforme processo:
            {PROCESSO} registrado na PROEX-UFSCar (Pró-Reitoria de Extensão).<br>
            <span style=\"text-decoration: underline\">Declaramos também que o(a) estudante está aguardando a emissão do certificado</span>.";

            $placeholders = ['{TITULO}', '{TEXTO}', '{DATA}', '{PROCESSO}'];
            $substituicoes = ['Atestado de conclusão de curso',$texto, CalendarioHelper::dataPorExtenso(), $inscricao->processo_proex];
            $exportador->html = str_replace($placeholders, $substituicoes, $exportador->html);

            $ofertas = $exportador->recuperarOfertasPara($id);
            $exportador->processarPeriodoInicialEFinal($ofertas);

        } else if ($tipo === 'aperfeicoamento') {

            $inscricao = Inscricao::model()->findByPk($id);
            [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();

            $texto = "Declaramos, para os devidos fins, que {$inscricao->nomeCompleto}, portador(a) do CPF:
            {$exportador->formatarCpf($inscricao->cpf)}, RA: {$inscricao->ra} concluiu o curso de Aperfeiçoamento em Educação e
            Tecnologias (180 horas), realizado no período de {PERIODO_INICIO} a {PERIODO_FIM} (conforme histórico em anexo),
            oferecido pela Universidade Federal de São Carlos (UFSCar), por meio do Grupo Horizonte (Grupo de
            Estudos e Pesquisas sobre Inovação em Educação, Tecnologias e Linguagens), conforme processo:
            {PROCESSO} registrado na PROEX-UFSCar (Pró-Reitoria de Extensão).<br>
            <span style=\"text-decoration: underline\">Declaramos também que o(a) estudante está aguardando a emissão do certificado</span>.";

            $placeholders = ['{TITULO}', '{TEXTO}', '{DATA}', '{PROCESSO}'];
            $substituicoes = ['Atestado de conclusão de curso', $texto, CalendarioHelper::dataPorExtenso(), $inscricao->processo_proex];
            $exportador->html = str_replace($placeholders, $substituicoes, $exportador->html);

            $ofertas = $exportador->recuperarOfertasPara($id);
            $exportador->processarPeriodoInicialEFinal($ofertas);

        } else if ($tipo === 'orientacao') {

            $docente = Docente::model()->findByPk($id);
            [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();
            $tcc = Tcc::model()->findByPk($tccId);

            $texto = "ATESTAMOS, para os devidos fins, que {$docente->nomeCompleto}, portador(a) do CPF: {$docente->cpf}, atuou como
            orientador de TCC (Trabalho de Conclusão de Curso) no curso de Especialização em Educação e Tecnologias
            oferecido pela Universidade Federal de São Carlos (UFSCar), por meio do Grupo Horizonte (Grupo de Estudos
            e Pesquisas sobre Inovação em Educação, Tecnologias e Linguagens), conforme do processo: {PROCESSO}
            registrado na PROEX-UFSCar (Pró-Reitoria de Extensão), acompanhando as atividades abaixo descritas:
            <ul>
                <li>Estudante: {$tcc->inscricao->nomeCompleto}</li>
                <li>Título do Trabalho: {$tcc->titulo}</li>
                <li>Ano de conclusão: {$tcc->anoDeConclusao}</li>
            </ul>
            ";

            $placeholders = ['{TITULO}', '{TEXTO}', '{DATA}'];
            $substituicoes = ['Atestado orientação de trabalho de conclusão de curso (TCC)', $texto, CalendarioHelper::dataPorExtenso()];
            $exportador->html = str_replace($placeholders, $substituicoes, $exportador->html);
        } else if ($tipo === 'membroDeBanca') {

            $docente = Docente::model()->findByPk($id);
            [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();
            $tcc = Tcc::model()->findByPk($tccId);

            $texto = "ATESTAMOS, para os devidos fins, que {$docente->nomeCompleto}, portador(a) do CPF: {$docente->cpf}, atuou como
            membro de banca de conclusão do curso de Especialização em Educação e Tecnologias, oferecido pela Universidade Federal
            de São Carlos (UFSCar), por meio do Grupo Horizonte (Grupo de Estudos e Pesquisas sobre Inovação em Educação, Tecnologias
            e Linguagens), conforme do processo: {PROCESSO} registrado na PROEX-UFSCar (Pró-Reitoria de Extensão), auxiliando no seguinte trabalho:
            <ul>
                <li>Estudante: {$tcc->inscricao->nomeCompleto}</li>
                <li>Título do Trabalho: {$tcc->titulo}</li>
                <li>Ano de conclusão: {$tcc->anoDeConclusao}</li>
            </ul>
            ";

            $placeholders = ['{TITULO}', '{TEXTO}', '{DATA}'];
            $substituicoes = ['Atestado membro de banca', $texto, CalendarioHelper::dataPorExtenso()];
            $exportador->html = str_replace($placeholders, $substituicoes, $exportador->html);
        } else if ($tipo === 'docencia') {

            $docente = Docente::model()->findByPk($id);
            [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();
            $oferta = Oferta::model()->findByPk($ofertaId);

            $texto = "Declaramos, para os devidos fins, que {$docente->nomeCompleto}, portador(a) do CPF:
            {$exportador->formatarCpf($docente->cpf)}, atuou como docente no curso de Especialização em Educação e Tecnologias
            oferecido pela Universidade Federal de São Carlos (UFSCar), por meio do Grupo Horizonte (Grupo de
            Estudos e Pesquisas sobre Inovação em Educação, Tecnologias e Linguagens), conforme do
            processo: {PROCESSO} registrado na PROEX-UFSCar (Pró-Reitoria de Extensão),
            desenvolvendo atividades no componente curricular: {$oferta->componenteCurricular->nome}, ofertado no período de
            {$oferta->recuperarPeriodo()} (carga horária: {$oferta->componenteCurricular->carga_horaria} horas).";

            $placeholders = ['{TITULO}', '{TEXTO}', '{DATA}'];
            $substituicoes = ['Atestado de docência', $texto, CalendarioHelper::dataPorExtenso()];
            $exportador->html = str_replace($placeholders, $substituicoes, $exportador->html);

        } else if ($tipo === 'tutoria') {

            $docente = Docente::model()->findByPk($id);
            [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();
            $oferta = Oferta::model()->findByPk($ofertaId);

            $texto = "Declaramos, para os devidos fins, que {$docente->nomeCompleto}, portador(a) do CPF:
            {$exportador->formatarCpf($docente->cpf)}, atuou como tutor(a) no curso de Especialização em Educação e Tecnologias
            oferecido pela Universidade Federal de São Carlos (UFSCar), por meio do Grupo Horizonte (Grupo de
            Estudos e Pesquisas sobre Inovação em Educação, Tecnologias e Linguagens), conforme do
            processo: {PROCESSO} registrado na PROEX-UFSCar (Pró-Reitoria de Extensão),
            desenvolvendo atividades no componente curricular: {$oferta->componenteCurricular->nome}, ofertado no período de
            {$oferta->recuperarPeriodo()} (carga horária: {$oferta->componenteCurricular->carga_horaria} horas).";

            $placeholders = ['{TITULO}', '{TEXTO}', '{DATA}'];
            $substituicoes = ['Atestado de tutoria', $texto, CalendarioHelper::dataPorExtenso()];
            $exportador->html = str_replace($placeholders, $substituicoes, $exportador->html);

        }

        $exportador->html = str_replace('{PROCESSO}', '23112.003256/2018-44', $exportador->html);

        $parametros = [
            'format' => 'A4',
            'orientation' => 'P',
            'margin_top' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0,
            'margin_left' => 0,
        ];
        $exportador->definirParametros($parametros);
        $nomeArquivo = "atestado_{$tipo}_edutec.pdf";
        $exportador->definirTitulo($nomeArquivo);
        $exportador->apresentarPdf($nomeArquivo);
    }

    private function formatarCpf($cpf)
    {
        $parte1 = substr($cpf, 0, 3);
        $parte2 = substr($cpf, 3, 3);
        $parte3 = substr($cpf, 6, 3);
        $digitos = substr($cpf, 9, 2);
        return "{$parte1}.{$parte2}.{$parte3}-{$digitos}";
    }

    private function recuperarOfertasPara($inscricaoId)
    {
        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        return $recuperadorDeOfertas
            ->daInscricao($inscricaoId)
            ->manterApenasInscritas()
            ->manterApenasOfertasAprovadas()
            ->recuperar();
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

}
