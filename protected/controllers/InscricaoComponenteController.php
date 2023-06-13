<?php

/**
 * Controlador que implementa uma interface REST para o modelo
 * InscricaoComponente.
 * 
 * Métodos:
 * 
 * inscricaoComponente/todos
 * - Retorna um vetor contendo todas as inscrições em componentes de todas
 *   as inscrições feitas.
 * 
 * inscricaoComponente/get
 * - Retorna um vetor contendo as inscrições em componentes feitas pelo
 *   aluno logado no momento.
 * 
 */
class InscricaoComponenteController extends Controller
{

    public $layout = '//layouts/column2';

    public function filters()
    {
        return array('rights');
    }

    public function actionTodos()
    {
        $inscricaoComponentes = InscricaoComponente::model()->findAll();
        $inscricaoComponentesArray = array_map(function($inscricaoComponente) {
            return $inscricaoComponente->asArray();
        }, $inscricaoComponentes);
        $this->respostaJSON(json_encode($inscricaoComponentesArray));
    }

    public function actionGet()
    {
        $cpf = Yii::app()->user->name;
        $inscricao = Inscricao::model()->findByAttributes(array(
            'cpf' => $cpf,
        ));
        $inscricaoComponentes = InscricaoComponente::model()->findAllByAttributes(array(
            'inscricao_id' => $inscricao->id,
        ));
        $inscricaoComponentesArray = array_map(function($inscricaoComponente) {
            return $inscricaoComponente->asArray();
        }, $inscricaoComponentes);
        $this->respostaJSON(json_encode($inscricaoComponentesArray));
    }

}
