<?php

/**
 * Controlador que implementa uma interface REST para o modelo TutorOferta.
 * 
 * Métodos:
 * 
 * tutorOferta/todos
 * - Retorna um vetor contendo todas as associações de tutores com ofertas.
 * 
 */
class TutorOfertaController extends Controller
{

    public function actionTodos()
    {
        $tutorOferta = TutorOferta::model()->findAll();
        $tutorOfertaArray = array_map(function($tutorOferta) {
            return $tutorOferta->asArray();
        }, $tutorOferta);
        $this->respostaJSON(json_encode($tutorOfertaArray));
    }

}
