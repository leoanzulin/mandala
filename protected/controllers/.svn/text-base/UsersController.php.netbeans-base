<?php

class UsersController extends RController
{

    public $layout = '//layouts/column2';

    public function filters()
    {
        return array('rights');
    }

    public function actionView($id)
    {
        $model = $this->loadModel($id);
        
        $this->render('view', array(
            'model' => $model,
        ));
    }

    public function actionCreate()
    {
        $model = new Users();

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Users'])) {
            $model->attributes = $_POST['Users'];
            if ($model->save()) {
                $this->updateRoles($model, $_POST['Users']['user_roles']);
                Yii::app()->user->setFlash('success', "Usuário '{$model->user_id}' criado com sucesso.");
                $this->redirect(array('view', 'id' => $model->user_id));
            }
            else {
                Yii::app()->user->setFlash('error', "Problemas na criação do usuário '{$model->user_id}'.");
            }
        }

        $model->user_password = '';
        $model->user_password_confirm = '';
        
        $this->render('create', array(
            'model' => $model,
        ));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Users'])) {
            $model->attributes = $_POST['Users'];
            if ($model->save()) {
                $this->updateRoles($model, $_POST['Users']['user_roles']);
                Yii::app()->user->setFlash('success', "Usuário '{$model->user_id}' atualizado com sucesso.");
                $this->redirect(array('view', 'id' => $model->user_id));
            }
            else {
                Yii::app()->user->setFlash('error', "Problemas na atualização do usuário '{$model->user_id}'.");
            }
        }

        $model->user_password = '';
        $model->user_password_confirm = '';
        
        $this->render('update', array(
            'model' => $model,
        ));
    }

    /**
     * Atualiza os papéis do usuário passado como parâmetro, removendo todos
     * seus papéis antigos e atribuindo-lhe os papéis contidos no array
     * 'newRoles'.
     * 
     * @param Users $model
     * @param array $newRoles
     */
    private function updateRoles(Users $model, $newRoles)
    {
        // Remove todos os papéis do usuário
        $assignedRoles = Rights::getAssignedRoles($model->user_id);
        foreach ($assignedRoles as $oldRole) {
            Yii::app()->authManager->revoke($oldRole->name, $model->user_id);
        }

        // Atribui os papéis novos
        foreach ($newRoles as $newRole) {
            Yii::app()->authManager->assign($newRole, $model->user_id);
        }
    }
    
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);
        if ($model->delete()) {
            Yii::app()->user->setFlash('success', "Usuário '{$id}' removido com sucesso.");
        }
        else {
            Yii::app()->user->setFlash('error', "Problemas na remoção do usuário '{$id}'.");
        }

        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('index'));
        }
    }

    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Users');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }

    public function actionAdmin()
    {
        $model = new Users('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Users'])) {
            $model->attributes = $_GET['Users'];
        }

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function loadModel($id)
    {
        $model = Users::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'users-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
