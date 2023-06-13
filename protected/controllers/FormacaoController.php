<?php

/**
 * Controlador que implementa uma interface REST para o modelo Formacao.
 * 
 * Métodos:
 * 
 * formacao/get&id=X
 * - Retorna um vetor com a(s) formação(ões) da inscrição passada como
 *   parâmetro.
 * 
 */
class FormacaoController extends Controller
{

    public function actionGet($id)
    {
        $formacoes = Formacao::model()->findAllByAttributes(array(
            'inscricao_id' => $id,
        ));
        $formacoesArray = array_map(function($formacao) {
            return $formacao->asArray();
        }, $formacoes);
        $this->respostaJSON(json_encode($formacoesArray));
    }

}
