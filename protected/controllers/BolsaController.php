<?php

/**
 * Controlador que implementa uma interface REST para o modelo Bolsa.
 * 
 * Métodos:
 * 
 * bolsa/todas
 * - Retorna um vetor contendo todos os modelos Bolsa do sistema em formato JSON.
 * 
 */
class BolsaController extends Controller
{

    public function actionTodas()
    {
        $bolsas = Bolsa::model()->findAll();
        $bolsasArray = array_map(function($bolsa) {
            return $bolsa->asArray();
        }, $bolsas);
        // http://stackoverflow.com/questions/16764177/angular-orderby-number-sorting-as-text-in-ng-repeat
//        $this->respostaJSON(json_encode($bolsasArray, JSON_NUMERIC_CHECK));
        $this->respostaJSON(json_encode($bolsasArray));
    }

}
