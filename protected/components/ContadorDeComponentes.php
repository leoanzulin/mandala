<?php

/**
 * Classe responsável por fazer a contagem de componentes em que um
 * determinado aluno está inscrito.
 */
class ContadorDeComponentes
{

    public static function contar($inscricao)
    {
        $habilitacoes = $inscricao->recuperarHabilitacoes();
        $ofertasInscritas = Oferta::model()->ofertasDaInscricao($inscricao->id);

        $idsDasComponentesInscritas = self::recuperarIdsDasComponentesInscritas($ofertasInscritas);
        $idsDasComponentesInscritas = array_unique($idsDasComponentesInscritas);
        self::adicionarComponenteDoTcc($idsDasComponentesInscritas);

        $componentes = self::recuperarObjetosComponentes($idsDasComponentesInscritas);

        $numeroDeComponentesPorHabilitacoes = self::inicializarContagem($habilitacoes);
        $numeroDeComponentesPorHabilitacoes['total_geral'] = count($componentes);

        self::contarComponentesPorHabilitacoes($componentes, $habilitacoes, $numeroDeComponentesPorHabilitacoes);
        self::ajustarSobrasDasComponentesOptativas($habilitacoes, $numeroDeComponentesPorHabilitacoes);
        self::ajustarSobrasDosTotaisDasHabilitacoes($habilitacoes, $numeroDeComponentesPorHabilitacoes);
        self::verificarQuaisComponentesAlcancaramNumeroMinimo($habilitacoes, $numeroDeComponentesPorHabilitacoes);

        return $numeroDeComponentesPorHabilitacoes;
    }

    private static function recuperarIdsDasComponentesInscritas($ofertasInscritas)
    {
        return array_map(function($oferta) {
            return $oferta['componente_curricular_id'];
        }, $ofertasInscritas);
    }

    private static function adicionarComponenteDoTcc(&$idsDasComponentesInscritas)
    {
        $ID_DO_COMPONENTE_DE_TCC = 8;
        $idsDasComponentesInscritas[] = $ID_DO_COMPONENTE_DE_TCC;
    }

    private static function recuperarObjetosComponentes($idsDasComponentesInscritas)
    {
        return array_map(function($idComponente) {
            return ComponenteCurricular::model()->findByPk($idComponente);
        }, $idsDasComponentesInscritas);
    }

    private static function inicializarContagem($habilitacoes)
    {
        $numeroDeComponentesPorHabilitacoes = array();
        foreach ($habilitacoes as $numero => $habilitacao) {
            $numeroDeComponentesPorHabilitacoes[$numero] = array(
                Constantes::LETRA_PRIORIDADE_NECESESARIA => 0,
                Constantes::LETRA_PRIORIDADE_OPTATIVA => 0,
                Constantes::LETRA_PRIORIDADE_LIVRE => 0,
                '-' => 0, // TODO: Verificar se isto ainda é necessário
            );
        }
        return $numeroDeComponentesPorHabilitacoes;
    }

    private static function contarComponentesPorHabilitacoes($componentes, $habilitacoes, &$numeroDeComponentesPorHabilitacoes)
    {
        foreach ($componentes as $componente) {
            foreach ($habilitacoes as $numero => $habilitacao) {
                $prioridadeDaComponente = $componente->prioridadeParaHabilitacao($habilitacao);
                $numeroDeComponentesPorHabilitacoes[$numero][$prioridadeDaComponente] ++;
            }
        }
    }

    /**
     * O que "sobra" das componentes opcionais vai para as componetnes livres.
     */
    private static function ajustarSobrasDasComponentesOptativas($habilitacoes, &$numeroDeComponentesPorHabilitacoes)
    {
        foreach ($habilitacoes as $numero => $habilitacao) {
            $sobraDeComponentesOptativas = max(array(
                0,
                $numeroDeComponentesPorHabilitacoes[$numero][Constantes::LETRA_PRIORIDADE_OPTATIVA] - Constantes::NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS
            ));
            $numeroDeComponentesPorHabilitacoes[$numero][Constantes::LETRA_PRIORIDADE_OPTATIVA] -= $sobraDeComponentesOptativas;
            $numeroDeComponentesPorHabilitacoes[$numero][Constantes::LETRA_PRIORIDADE_LIVRE] += $sobraDeComponentesOptativas;
        }
    }

    private static function ajustarSobrasDosTotaisDasHabilitacoes($habilitacoes, &$numeroDeComponentesPorHabilitacoes)
    {
        $totalComponentes = $numeroDeComponentesPorHabilitacoes['total_geral'];

        if (count($habilitacoes) > 2) {
            $numeroDeComponentesPorHabilitacoes[1]['total'] = min(
                    Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1, //
                    $totalComponentes
            );
            $numeroDeComponentesPorHabilitacoes[2]['total'] = max(
                    $totalComponentes - Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1, //
                    0
            );
            $numeroDeComponentesPorHabilitacoes[3]['total'] = max(
                    $totalComponentes - (constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1 + Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO2), //
                    0
            );
        } else if (count($habilitacoes) > 1) {
            $numeroDeComponentesPorHabilitacoes[1]['total'] = min(
                    Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1, //
                    $totalComponentes
            );
            $numeroDeComponentesPorHabilitacoes[2]['total'] = max(
                    $totalComponentes - Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACAO1, //
                    0
            );
        } else {
            $numeroDeComponentesPorHabilitacoes[1]['total'] = $totalComponentes;
        }
    }

    private static function verificarQuaisComponentesAlcancaramNumeroMinimo($habilitacoes, &$numeroDeComponentesPorHabilitacoes)
    {
        $indicesASeremVerificados = array(
            Constantes::LETRA_PRIORIDADE_NECESESARIA => Constantes::NUMERO_MINIMO_DE_COMPONENTES_NECESSARIAS,
            Constantes::LETRA_PRIORIDADE_OPTATIVA => Constantes::NUMERO_MINIMO_DE_COMPONENTES_OPTATIVAS,
            Constantes::LETRA_PRIORIDADE_LIVRE => Constantes::NUMERO_MINIMO_DE_COMPONENTES_LIVRES,
        );
        foreach ($habilitacoes as $numero => $habilitacao) {
            foreach ($indicesASeremVerificados as $letra => $numeroMinimo) {
                if ($numeroDeComponentesPorHabilitacoes[$numero][$letra] >= $numeroMinimo) {
                    $numeroDeComponentesPorHabilitacoes[$numero]["{$letra}_OK"] = true;
                }
            }
            if ($numeroDeComponentesPorHabilitacoes[$numero]['total'] >= Constantes::NUMERO_MINIMO_DE_COMPONENTES_PARA_CUMPRIR_HABILITACOES($numero)) {
                $numeroDeComponentesPorHabilitacoes[$numero]['total_OK'] = true;
            }
        }
    }

}
