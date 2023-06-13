<?php

class MultiLoginController extends Controller
{

    public function actionIndex()
    {
        $inscricoes = Inscricao::model()->findAllByAttributes(['cpf' => Yii::app()->user->id]);
        $this->render('index', [
            'inscricoes' => $inscricoes,
        ]);
    }

    public function actionAcessar($id)
    {
        Yii::app()->session['inscricao_id'] = $id;
        $this->redirect(Yii::app()->createUrl('aluno'));
    }

}
