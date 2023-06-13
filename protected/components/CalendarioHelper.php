<?php

class CalendarioHelper
{
    public const MESES = [
        'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
        'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro',
    ];

    public static function mesEAnoAtuais()
    {
        return [ (int) date('m'), (int) date('Y') ];
    }

    public static function proximoMesEano()
    {
        [ $mes, $ano ] = self::mesEAnoAtuais();
        return $mes + 1 == 13 ? [ 1, $ano + 1 ] : [ $mes + 1, $ano ];
    }

    // Verifica se esamos no período em que alunos devem confirmar suas inscrições em ofertas
    public static function estaNoPeriodoDeConfirmacao()
    {
        $abertura = Configuracao::model()->findByPk('confirmacao.abertura');
        $fechamento = Configuracao::model()->findByPk('confirmacao.fechamento');
        $abertura = self::adicionarZeros((int)( $abertura->valor ?? 1 ));
        $fechamento = self::adicionarZeros((int)( $fechamento->valor ?? 28 ));

        // [ $proximoMes, $ano ] = self::proximoMesEAno();
        // $hoje = new DateTime();
        // $inicioDoProximoMes = new DateTime("{$ano}-{$proximoMes}-01");
        [ $mesAtual, $anoAtual ] = self::mesEAnoAtuais();

        $mesAtual = self::adicionarZeros( $mesAtual );
        $dataAbertura = "{$anoAtual}-{$mesAtual}-{$abertura}";
        $dataFechamento = "{$anoAtual}-{$mesAtual}-{$fechamento}";
        $dataHoje = date('Y-m-d');

        return $dataAbertura <= $dataHoje && $dataHoje <= $dataFechamento;
    }

    public static function adicionarZeros($numero)
    {
        $nuemro = (string) $numero;
        return (strlen($numero) < 2) ? '0' . $numero : $numero;
    }

    public static function estaDentroDoPeriodoDeConfirmacao($oferta)
    {
        if (empty($oferta['dataInicio'])) return false;

        $dataInicioOferta = date('Y-m-d', strtotime($oferta['dataInicio']));
        $dataAbertura = date('Y-m-d', strtotime($dataInicioOferta . ' - 30 days'));
        $dataFechamento = date('Y-m-d', strtotime($dataInicioOferta . ' - 5 days'));
        $dataHoje = date('Y-m-d');

        return $dataAbertura <= $dataHoje && $dataHoje <= $dataFechamento;
    }

    public static function estaNoPassado($mes, $ano)
    {
        [ $mesAtual, $anoAtual ] = self::mesEAnoAtuais();
        return $ano < $anoAtual || ($ano == $anoAtual && $mes < $mesAtual);
    }

    public static function estaNoPassadoOuPresente($mes, $ano)
    {
        [ $mesAtual, $anoAtual ] = self::mesEAnoAtuais();
        return $ano < $anoAtual || ($ano == $anoAtual && $mes <= $mesAtual);
    }

    public static function nomeDoProximoMes()
    {
        $mes = (int)date('m');
        $proximoMes = $mes + 1 == 13 ? $mes = 1 : $mes + 1;
        return self::MESES[$proximoMes - 1];
    }

    public static function nomeDoMes($mes)
    {
        return self::MESES[$mes - 1];
    }

    public static function dataPorExtenso()
    {
        // Não coloquei São Paulo aqui porque Fortaleza não tem horário de verão, pode prevenir alguns problemas
        date_default_timezone_set('America/Fortaleza');
        return date('d') . ' de ' . self::nomeDoMes((int)date('m')) . ' de ' . date('Y');
    }

    /**
     * Retorna o número absoluto de dias entre duas datas.
     * As datas devem ser informadas no formato "yyyy-mm-dd"
     */
    public static function numeroDeDiasEntre($data1, $data2)
    {
        $data1 = new DateTime($data1);
        $data2 = new DateTime($data2);
        return (int)$data2->diff($data1)->format("%a");
    }

    /**
     * Transforma strings no formato ANO_MES em strings no formato MES/ANO
     */
    public static function transformarStringPeriodo($periodoString)
    {
        [ $ano, $mes ] = explode('_', $periodoString);
        return "{$mes}/{$ano}";
    }

}