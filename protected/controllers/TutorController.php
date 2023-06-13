<?php

/**
 * Controlador que implementa uma interface REST para o modelo Tutor.
 * 
 * Métodos:
 * 
 * tutor/todos
 * - Retorna um vetor contendo todos os tutores do sistema
 * 
 */
class TutorController extends Controller
{

    /**
     * Action REST
     */
    public function actionTodos()
    {
        $tutores = Tutor::model()->findAllByAttributes([ 'ativo' => true ]);
        $tutoresArray = array_map(function($tutor) {
            return $tutor->asArray();
        }, $tutores);
        $this->respostaJSON(json_encode($tutoresArray));
    }

    public function actionGerenciar()
    {
        $model = new Tutor('search');
        $model->unsetAttributes();
        if (isset($_GET['Tutor'])) {
            $model->attributes = $_GET['Tutor'];
        }

        $this->render('gerenciar', array(
            'model' => $model,
        ));
    }

    public function actionCadastrar()
    {
        $model = new Tutor();

        if (isset($_POST['Tutor'])) {
            $model->attributes = $_POST['Tutor'];
            if ($model->save()) {
                Yii::app()->user->setFlash('notificacao', "Tutor {$model->nome} {$model->sobrenome} cadastrado com sucesso!");
                Yii::log("{$model} foi cadastrado no sistema.", 'info', 'system.controllers.AdminController');
                $model = new Tutor();
            }
        }

        $this->adicionarArquivosJavascript('/js/mascara_celular');

        $this->render('cadastrar', array(
            'model' => $model,
        ));
    }

    public function actionEditar($cpf)
    {
        $model = Tutor::model()->findByPk($cpf);

        if (isset($_POST['desativar'])) {
            $model->desativar();
            Yii::app()->user->setFlash('notificacao', "Tutor {$model->nomeCompleto} desativado com sucesso!");
            Yii::log("{$model} foi desativado.", 'info', 'system.controllers.TutorController');
            $this->redirect(array('gerenciar'));
        } else if (isset($_POST['Tutor'])) {
            $model->attributes = $_POST['Tutor'];
            if ($model->save()) {
                Yii::app()->user->setFlash('notificacao', "Tutor {$model->nome} {$model->sobrenome} atualizado com sucesso!");
                Yii::log("{$model} foi atualizado.", 'info', 'system.controllers.AdminController');
                $this->redirect(array('gerenciar'));
            }
        }

        $this->adicionarArquivosJavascript('/js/mascara_celular');

        $this->render('editar', array(
            'model' => $model,
        ));
    }

    public function actionVisualizar($cpf)
    {
        $model = Tutor::model()->findByPk($cpf);

        $this->render('visualizar', array(
            'model' => $model,
        ));
    }

}
