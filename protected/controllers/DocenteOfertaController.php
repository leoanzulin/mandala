<?php

/**
 * Controlador que implementa uma interface REST para o modelo
 * DocenteOferta.
 * 
 * Métodos:
 * 
 * docenteOferta/todos
 * - Retorna um vetor contendo todas as associações de docnetes com ofertas.
 * 
 */
class DocenteOfertaController extends Controller
{

    public function actionTodos()
    {
        $docenteOferta = DocenteOferta::model()->findAll();
        $docenteOfertaArray = array_map(function($docenteOferta) {
            return $docenteOferta->asArray();
        }, $docenteOferta);
        $this->respostaJSON(json_encode($docenteOfertaArray));
    }

}
