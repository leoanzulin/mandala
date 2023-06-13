<?php

class OfferController extends RController
{

    public $layout = '//layouts/column2';

    public function filters()
    {
        return array('rights');
    }

    public function actionIndex()
    {
        $dataProviderCursos = new CActiveDataProvider('Offer', array(
        ));
        
        $this->render('index', array(
            'dataProvider' => $dataProviderCursos,
        ));
    }

    public function actionCreate($disciplineId) {
        $discipline = Discipline::model()->findByPk($disciplineId);
        $offer = new Offer();
        $offer->offer_discipline_id = $disciplineId;

        if (isset($_POST['Offer'])) {
            $offer->attributes = $_POST['Offer'];
            if ($offer->save()) {
                Yii::app()->user->setFlash('success', "Oferta da disciplina '{$offer->discipline->discipline_name}' criada com sucesso.");
                $this->redirect(array('discipline/view', 'id' => $disciplineId,));
            }
            else {
                Yii::app()->user->setFlash('error', "Problemas na criação da oferta da disciplina '{$offer->discipline->discipline_name}'.");
            }
        }

        $this->render('create', array(
            'discipline' => $discipline,
            'offer' => $offer,
        ));
    }

    public function actionUpdate($disciplineId, $userId, $startDate, $endDate) {
        $offer = Offer::model()->findByPk(array(
            'offer_discipline_id' => $disciplineId,
            'offer_teacher_id' => $userId,
            'offer_start_date' => $this->convertDateToDatabaseFormat($startDate),
            'offer_end_date' => $this->convertDateToDatabaseFormat($endDate)
        ));

        if (isset($_POST['Offer'])) {
            $offer->attributes = $_POST['Offer'];
            if ($offer->save()) {
                Yii::app()->user->setFlash('success', "Oferta da disciplina '{$offer->discipline->discipline_name}' atualizada com sucesso.");
                $this->redirect(array('discipline/view', 'id' => $disciplineId,));
            }
            else {
                Yii::app()->user->setFlash('error', "Problemas na atualização da oferta da disciplina '{$offer->discipline->discipline_name}'.");
            }
        }

        $this->render('update', array(
            'model' => $offer
        ));
    }
    
    private function convertDateToDatabaseFormat($date) {
        $parts = explode('/', $date);
        return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
    }
    
    public function actionDelete($disciplineId, $userId, $startDate, $endDate) {
        $offer = Offer::model()->findByPk(array(
            'offer_discipline_id' => $disciplineId,
            'offer_teacher_id' => $userId,
            'offer_start_date' => $this->convertDateToDatabaseFormat($startDate),
            'offer_end_date' => $this->convertDateToDatabaseFormat($endDate)
        ));

        $offer->offer_start_date = $this->convertDateToDatabaseFormat($startDate);
        $offer->offer_end_date = $this->convertDateToDatabaseFormat($endDate);
        
        $disciplineName = $offer->discipline->discipline_name;

        if ($offer->delete()) {
            Yii::app()->user->setFlash('success', "Oferta da disciplina '{$disciplineName}' removida com sucesso.");
            $this->redirect(array('discipline/view', 'id' => $disciplineId));
        }
        else {
            Yii::app()->user->setFlash('error', "Problemas na remoção da oferta da disciplina '{$disciplineName}'.");
        }
    }

}
