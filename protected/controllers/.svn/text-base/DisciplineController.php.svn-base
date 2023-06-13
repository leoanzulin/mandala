<?php

class DisciplineController extends RController
{

    public $layout = '//layouts/column2';

    public function filters()
    {
        return array('rights');
    }

    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Discipline');
        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }
    
    public function actionView($id)
    {
        $dataProviderOffers = new CActiveDataProvider('Offer', array(
            'criteria' => array(
                'condition' => 'offer_discipline_id = ' . $id,
            )
        ));
        
        $this->render('view', array(
            'model' => $this->loadModel($id),
            'dataProviderOffers' => $dataProviderOffers,
        ));
    }

    public function actionCreateDisciplineForCourse($courseId)
    {
        $course = Course::model()->findByPk($courseId);
        $discipline = new Discipline();
        $discipline->course_id = $courseId;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Discipline'])) {
            $discipline->attributes = $_POST['Discipline'];
            if ($discipline->save()) {
                Yii::app()->user->setFlash('success', "Disciplina '{$discipline->discipline_name}' criada com sucesso.");
                $this->redirect(array('view', 'id' => $discipline->discipline_id));
            }
            else {
                Yii::app()->user->setFlash('error', "Problemas na criação da disciplina '{$discipline->discipline_name}'.");
            }
        }

        $this->render('createDiscipline', array(
            'course' => $course,
            'model' => $discipline,
        ));
    }
    
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Discipline'])) {
            $model->attributes = $_POST['Discipline'];
            if ($model->save()) {
                Yii::app()->user->setFlash('success', "Disciplina '{$model->discipline_name}' atualizada com sucesso.");
                $this->redirect(array('view', 'id' => $model->discipline_id));
            }
            else {
                Yii::app()->user->setFlash('error', "Problemas na atualização da disciplina '{$discipline->discipline_name}'.");
            }
        }

        $this->render('update', array(
            'model' => $model,
        ));
    }

    public function actionDelete($id)
    {
        $model = $this->loadModel($id);
        $courseId = $model->course_id;
        $disciplineName = $model->discipline_name;
        if (!$model->delete()) {
            Yii::app()->user->setFlash('error', "Problemas na remoção da disciplina '{$disciplineName}'.");
        }

        Yii::app()->user->setFlash('success', "Disciplina '{$disciplineName}' removida com sucesso.");
        
        if (!isset($_GET['ajax'])) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('course/view', 'id' => $courseId));
        }
    }

    public function actionAdmin()
    {
        $model = new Discipline('search');
        $model->unsetAttributes();  // clear any default values
        if (isset($_GET['Discipline'])) {
            $model->attributes = $_GET['Discipline'];
        }

        $this->render('admin', array(
            'model' => $model,
        ));
    }

    public function loadModel($id)
    {
        $model = Discipline::model()->findByPk($id);
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $model;
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'discipline-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
