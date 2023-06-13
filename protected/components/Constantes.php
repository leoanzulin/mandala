<?php

/**
 * Classe que contém constantes utilizadas em todo o sistema.
 */
class Constantes
{
    const VERSAO = '1.13.2';
    const TURMA_ABERTA = 4;

    // Estes números não devem ser alterados pois estão no core do sistema
    const PRIORIDADE_SEM_PRIORIDADE = -1;
    const PRIORIDADE_NECESSARIA = 0;
    const PRIORIDADE_OPTATIVA = 1;
    const PRIORIDADE_LIVRE = 2;
    //
    const LETRA_SEM_PRIORIDADE = '-';
    const LETRA_PRIORIDADE_NECESESARIA = 'NFO';
    const LETRA_PRIORIDADE_OPTATIVA = 'NFE';
    const LETRA_PRIORIDADE_LIVRE = 'NFC';
    const NOME_PRIORIDADE_NECESSARIA = 'Núcleo Formativo Obrigatório';
    const NOME_PRIORIDADE_OPTATIVA = 'Núcleo Formativo Específico';
    const NOME_PRIORIDADE_LIVRE = 'Núcleo Formativo Complementar';
    // Especialização
    const NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS = 4;
    const NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS = 10;
    const NUMERO_MINIMO_DE_COMPONENTES_LIVRES = 5;
    const NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1 = 20;
    const NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS = 20;
    const NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS_NOVAS_POR_HABILITACAO = 7; // Não é mais usado
    const NUMERO_MINIMO_DE_COMPONENTES_NOVAS_POR_HABILITACAO = 7;
    // Extensão e aperfeiçoamento
    const NUMERO_DE_COMPONENTES_OBRIGATORIOAS_PARA_EXTENSAO_E_APERFEICOAMENTO = 2;
    const STRING_COMPONENTES_OBRIGATORIOS_PARA_EXTENSAO_E_APERFEICOAMENTO = 'Os componentes "Ambientação e Letramento Digital" e "Educação e Tecnologias: Introdução ao Curso" são obrigatórios';
    const NUMERO_DE_COMPONENTES_LIVRES_PARA_EXTENSAO = 3;
    const NUMERO_DE_COMPONENTES_LIVRES_PARA_APERFEICOAMENTO = 8;
    // Componentes adicionais que o aluno pode fazer por habilitação
    const COMPONENTES_BONUS_POR_HABILITACAO = 1;
    //
    const NUMERO_MINIMO_DE_HORAS_PARA_SER_ESPECIALISTA = 400;
    const COMPONENTE_DESENVOLVIMENTO_DE_PROJETO_INTEGRADOR = 'Desenvolvimento de projeto integrador';

    //
    // Valores mínimos para um aluno ser aprovado em uma oferta
    // Utilizados em InscricaoOferta
    const MEDIA_MINIMA = 7;
    const FREQUENCIA_MINIMA = 75;
    // Papéis no sistema
    const PAPEL_COLABORADOR = 'Colaborador';
    const PAPEL_PROFESSOR = 'Professor';
    const PAPEL_TUTOR = 'Tutor';
    const PAPEL_ORIENTADOR = 'Orientador';
    const PAPEL_ALUNO = 'Aluno';

    public static function NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES($numero = null)
    {
        $numeroMinimoDeComponentesPorHabilitacao = array(
            1 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1,
            2 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
            3 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
            4 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
            5 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
            6 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
            7 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
            8 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
            9 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
            10 => self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS,
        );
        return empty($numero) ? $numeroMinimoDeComponentesPorHabilitacao : $numeroMinimoDeComponentesPorHabilitacao[$numero];
    }

    public static function NUMERO_MAXIMO_DE_COMPONENTES_POR_HABILITACAO($habilitacao = null)
    {
        $numero = [];
        for ($i = 1; $i <= 10; $i++) {
            $numero[$i] = $i == 1 ? self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1 : self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES_ADICIONAIS;
            $numero[$i] += self::COMPONENTES_BONUS_POR_HABILITACAO;
        }
        return empty($habilitacao) ? $numero : $numero[$habilitacao];
    }

    public static function NUMERO_MINIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES($numeroDeHabilitacoes = null)
    {
        $numero = [ 0, self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1 ];
        for ($i = 2; $i <= 10; $i++) {
            $numero[$i] = $numero[$i - 1] + self::NUMERO_MINIMO_DE_COMPONENTES_NOVAS_POR_HABILITACAO;
        }
        return empty($numeroDeHabilitacoes) ? $numero : $numero[$numeroDeHabilitacoes];
    }

    public static function NUMERO_MAXIMO_DE_COMPONENTES_TOTAIS_SOMANDO_TODAS_HABILITACOES($numeroDeHabilitacoes = null)
    {
        $numero = [ 0, self::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1 + self::COMPONENTES_BONUS_POR_HABILITACAO ];
        for ($i = 2; $i <= 10; $i++) {
            $numero[$i] = $numero[$i - 1] + self::NUMERO_MINIMO_DE_COMPONENTES_NOVAS_POR_HABILITACAO + self::COMPONENTES_BONUS_POR_HABILITACAO;
        }
        return empty($numeroDeHabilitacoes) ? $numero : $numero[$numeroDeHabilitacoes];
    }

    public static function NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS_NOVAS_POR_HABILITACAO($habilitacao = null)
    {
        $numero = [];
        for ($i = 1; $i <= 10; $i++) {
            $numero[$i] = $i == 1 ? self::NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS : self::NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS_NOVAS_POR_HABILITACAO;
        }
        return empty($habilitacao) ? $numero : $numero[$habilitacao];
    }

    public static function ORDINAIS($numero = null)
    {
        $ordinais = array(
            1 => 'primeira',
            2 => 'segunda',
            3 => 'terceira',
            4 => 'quarta',
            5 => 'quinta',
            6 => 'sexta',
            7 => 'sétima',
            8 => 'oitava',
            9 => 'nona',
            10 => 'décima',
        );
        return empty($numero) ? $ordinais : $ordinais[$numero];
    }

    public static function TODAS_AS_LETRAS()
    {
        return array(
            self::LETRA_PRIORIDADE_NECESESARIA,
            self::LETRA_PRIORIDADE_OPTATIVA,
            self::LETRA_PRIORIDADE_LIVRE,
        );
    }

    public static function NOMES_HABILITACOES($numero = null)
    {
        $habilitacoes = Habilitacao::model()->findAll(array('order' => 'id'));
        $nomesHabilitacoes = array_map(function($habilitacao) {
            return $habilitacao->nome;
        }, $habilitacoes);

        // TODO: Verificar se checar '-' é necessário
        if ($numero == '-') {
            $numero = 0;
        }

        return empty($numero) ? $nomesHabilitacoes[0] : $nomesHabilitacoes[$numero];
    }

    public static function NUMERO_DE_HABILITACOES()
    {
        return count(Habilitacao::model()->findAllValid());
    }

    public static function PRIORIDADE_PARA_LETRA($prioridade)
    {
        $prioridadesParaLetras = array(
            self::PRIORIDADE_SEM_PRIORIDADE => self::LETRA_SEM_PRIORIDADE,
            self::PRIORIDADE_NECESSARIA => self::LETRA_PRIORIDADE_NECESESARIA,
            self::PRIORIDADE_OPTATIVA => self::LETRA_PRIORIDADE_OPTATIVA,
            self::PRIORIDADE_LIVRE => self::LETRA_PRIORIDADE_LIVRE,
        );
        return $prioridadesParaLetras[$prioridade];
    }

    public static function LETRA_PARA_PRIORIDADE($letra)
    {
        $letrasParaPrioridades = array(
            self::LETRA_SEM_PRIORIDADE => self::PRIORIDADE_SEM_PRIORIDADE,
            self::LETRA_PRIORIDADE_NECESESARIA => self::PRIORIDADE_NECESSARIA,
            self::LETRA_PRIORIDADE_OPTATIVA => self::PRIORIDADE_OPTATIVA,
            self::LETRA_PRIORIDADE_LIVRE => self::PRIORIDADE_LIVRE,
        );
        return $letrasParaPrioridades[$letra];
    }

    public static function COMPONENTES_OBRIGATORIAS_PARA_ESPECIALIZACAO()
    {
        // 1 - Ambientação e letramento digital
        // 4 - Educação e Tecnologias: uma introdução ao curso
        // 5 - Metodologia de pesquisa e produção científica 1
        // 6 - Metodologia de pesquisa e produção científica 2
        return [ 1, 4, 5, 6 ];
    }

    public static function COMPONENTES_OBRIGATORIAS_PARA_EXTENSAO_E_APERFEICOAMENTO()
    {
        // 1 - Ambientação e letramento digital
        // 4 - Educação e Tecnologias: uma introdução ao curso
        return [ 1, 4 ];
    }

    public static function COMPOMENTE_EH_OBRIGATORIA_PARA_EXTENSAO_E_APERFEICOAMENTO($compomenteId)
    {
        return in_array($compomenteId, self::COMPONENTES_OBRIGATORIAS_PARA_EXTENSAO_E_APERFEICOAMENTO());
    }



}
