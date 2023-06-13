<?php

/**
 * Controlador que implementa uma interface REST para o modelo
 * InscricaoSimulador.
 * 
 * Métodos:
 * 
 * inscricaoSimulador/get
 * - Retorna um vetor contendo as inscrições em ofertas feitas pelo
 *   aluno logado no momento.
 * 
 */
class InscricaoSimuladorController extends Controller
{

    public function actionGet()
    {
        $inscricao = Inscricao::model()->findByAttributes(array(
            'cpf' => Yii::app()->user->name,
        ));

        // TODO: Fazer isso usando as relations da classe de modelo Inscricao
        $inscricoesSimulador = InscricaoSimulador::model()->findAllByAttributes(array(
            'inscricao_id' => $inscricao->id,
        ));
        $inscricoesSimuladorArray = array_map(function($inscricaoSimulador) {
            return $inscricaoSimulador->asArray();
        }, $inscricoesSimulador);
        $this->respostaJSON(json_encode($inscricoesSimuladorArray));
    }
/*
    public function actionDoPeriodoAberto()
    {
        $inscricao = Inscricao::model()->findByAttributes(array(
            'cpf' => Yii::app()->user->name,
        ));
        
        $deMes = Configuracao::model()->findByPk('inscricoes.inicio_periodo_aberto.mes')->valor;
        $deAno = Configuracao::model()->findByPk('inscricoes.inicio_periodo_aberto.ano')->valor;
        $ateMes = Configuracao::model()->findByPk('inscricoes.fim_periodo_aberto.mes')->valor;
        $ateAno = Configuracao::model()->findByPk('inscricoes.fim_periodo_aberto.ano')->valor;

        $preinscricaoComponentes = PreinscricaoComponente::model()->findAllByAttributes(array(
            'inscricao_id' => $inscricao->id,
        ));
        $preinscricaoComponentesArray = array_map(function($preinscricaoComponente) {
            return $preinscricaoComponente->asArray();
        }, $preinscricaoComponentes);
        $this->respostaJSON(json_encode($preinscricaoComponentesArray));
    }
    */
}
