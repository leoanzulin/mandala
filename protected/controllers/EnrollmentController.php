<?php

class EnrollmentController extends RController
{

    public $layout = '//layouts/column2';

//    public function filters()
//    {
//        return array('rights');
//    }

    public function actionIndex()
    {
        $dataProviderCursos = new CActiveDataProvider('Course', array(
            'criteria' => array(
                'order' => 'course_id ASC',
                'condition' => '1=1',
            )
        ));

        $this->render('index', array(
            'dataProviderCursos' => $dataProviderCursos,
        ));
    }

    public function actionView($courseId, $enrollmentId)
    {
        $model = Enrollment::model()->findByPk(array(
            'course_id' => $courseId,
            'enrollment_id' => $enrollmentId
        ));
        $this->render('view', array(
            'model' => $model
        ));
    }
    
    public function actionEnroll($courseId)
    {
        $inscricao = new Enrollment();
        $inscricao->course_id = $courseId;
        $inscricao->enr_public_server = 0;
        $curso = Course::model()->findByPk($courseId);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Enrollment'])) {
            $inscricao->attributes = $_POST['Enrollment'];
            if ($inscricao->save()) {
                $this->redirect(array(
                    'success',
                    'enrollmentId' => $inscricao->enrollment_id,
                    'courseId' => $courseId,
                ));
            }
        }

        $baseUrl = Yii::app()->baseUrl;
        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile($baseUrl . '/js/mascara_celular.js');

        $this->render('enroll', array(
            'model' => $inscricao,
            'course' => $curso,
        ));
    }

    public function actionSuccess($enrollmentId, $courseId)
    {
        $inscricao = Enrollment::model()->findByPk(array(
            'enrollment_id' => $enrollmentId,
            'course_id' => $courseId,
        ));
        $curso = Course::model()->findByPk($courseId);

        $this->render('success', array(
            'enrollment' => $inscricao,
            'course' => $curso,
        ));
    }

    public function actionManage()
    {
        $dataProviderCursos = new CActiveDataProvider('Course', array(
            'criteria' => array(
                'order' => 'course_id ASC',
                'condition' => '1=1',
            )
        ));
        
        $this->render('manage', array(
            'dataProviderCursos' => $dataProviderCursos,
        ));
    }

    public function actionManageCourse($id)
    {
        $model = Course::model()->findByPk($id);
        
        $dataProvider = new CActiveDataProvider('Enrollment', array(
            'criteria' => array(
                'order' => 'enrollment_id ASC',
                'condition' => 'course_id = ' . $id,
            )
        ));
        
        $this->render('manageCourse', array(
            'dataProvider' => $dataProvider,
            'model' => $model,
        ));
    }

    /**
     * Esta ação pode ser chamada por AJAX ou não. Aprova ou recusa uma
     * inscrição em um curso.
     * 
     * @param int $id ID da inscrição sendo avaliada
     * @param int $state 1 se a inscrição deve ser aprovada, 0 caso contário
     */
    public function actionApproveRefuseEnrollment($courseId, $enrollmentId, $state)
    {
        $model = Enrollment::model()->findByPk(array(
            'course_id' => $courseId,
            'enrollment_id' => $enrollmentId
        ));

        $resultado = $state == 1 ? $model->approve() : $model->refuse();

        if ($resultado === false)
        {
            Yii::app()->user->setFlash('error', "Problema na validação da inscrição de '{$model->fullName}'.");
            $this->redirect(array('enrollment/manageCourse', 'id' => $model->course_id));
        }

        if (!Yii::app()->request->isPostRequest)
        {
            $texto = $state == 0 ? 'recusada' : 'aprovada';
            Yii::app()->user->setFlash('success', "Inscrição de {$model->fullName}' {$texto}");
//            Yii::log("Inscrição de '{$model->fullName}' (ID {$model->enrollment_id}) {$texto} pelo usuário '" . Yii::app()->user->name . "'.", 'info', 'system.controllers.EnrollmentController');
            $this->redirect(array('enrollment/manageCourse', 'id' => $model->course_id));
        }
        else
        {
            header('Content-type: application/json');
            echo CJSON::encode(array('resposta' => 'ok'));
            Yii::app()->end();
        }
    }
    
}
