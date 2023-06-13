<?php

/**
 * Controlador que implementa uma interface REST para o modelo
 * InscricaoOferta.
 * 
 * Métodos:
 * 
 * inscricaoOferta/todos
 * - Retorna um vetor contendo todas as inscrições em ofertas de todas as
 *   inscrições feitas.
 * 
 * inscricaoOferta/get
 * - Retorna um vetor contendo as ofertas em que o aluno logado no momento
 *   está inscrito.
 * 
 */
class InscricaoOfertaController extends Controller
{

    public function actionTodos()
    {
        $inscricaoOferta = InscricaoOferta::model()->findAll();
        $inscricaoOfertaArray = array_map(function($inscricaoOferta) {
            return $inscricaoOferta->asArray();
        }, $inscricaoOferta);
        $this->respostaJSON(json_encode($inscricaoOfertaArray));
    }

    public function actionGet()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $ofertasArray = array_map(function($oferta) {
            return $oferta->asArray();
        }, $inscricao->ofertas);
        $this->respostaJSON(json_encode($ofertasArray));
    }

    public function actionInscricoesDoId($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);
        $ofertasArray = array_map(function($oferta) {
            return $oferta->asArray();
        }, $inscricao->ofertas);
        $this->respostaJSON(json_encode($ofertasArray));
    }

}
