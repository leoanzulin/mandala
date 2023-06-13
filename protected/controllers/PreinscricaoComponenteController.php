<?php

/**
 * Controlador que implementa uma interface REST para o modelo
 * PreinscricaoComponente.
 * 
 * Métodos:
 * 
 * preinscricaoComponente/todos
 * - Retorna um vetor contendo todas as pré-inscrições em componentes de todas
 *   as inscrições feitas.
 * 
 * preinscricaoComponente/get
 * - Retorna um vetor contendo as pré-inscrições em componentes feitas pelo
 *   aluno logado no momento.
 * 
 */
class PreinscricaoComponenteController extends Controller
{

    public function actionTodos()
    {
        $preinscricaoComponentes = PreinscricaoComponente::model()->findAll();
        $preinscricaoComponentesArray = array_map(function($preinscricaoComponente) {
            return $preinscricaoComponente->asArray();
        }, $preinscricaoComponentes);
        $this->respostaJSON(json_encode($preinscricaoComponentesArray));
    }

    public function actionGet()
    {
        $inscricao = Inscricao::model()->findByAttributes(array(
            'cpf' => Yii::app()->user->name,
        ));
        // TODO: Fazer isso usando as relations da classe de modelo Inscricao
        $preinscricaoComponentes = PreinscricaoComponente::model()->findAllByAttributes(array(
            'inscricao_id' => $inscricao->id,
        ));
        $preinscricaoComponentesArray = array_map(function($preinscricaoComponente) {
            return $preinscricaoComponente->asArray();
        }, $preinscricaoComponentes);
        $this->respostaJSON(json_encode($preinscricaoComponentesArray));
    }

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
    
}
