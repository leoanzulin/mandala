<?php

/**
 * This is the model class for table "discipline".
 *
 * The followings are the available columns in table 'discipline':
 * @property integer $discipline_id
 * @property integer $course_id
 * @property string $discipline_name
 * @property string $menu
 * @property integer $workload
 * @property string $objective
 *
 * The followings are the available model relations:
 * @property Course $course
 * @property Offer[] $offers
 */
class Discipline extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'discipline';
    }

    public function rules()
    {
        return array(
            array('course_id, discipline_name, menu, workload, objective', 'required'),
            array('course_id, workload', 'numerical', 'integerOnly' => true),
            array('discipline_name', 'length', 'max' => 255),
            array('discipline_id, course_id, discipline_name, menu, workload, objective', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'course' => array(self::BELONGS_TO, 'Course', 'course_id'),
            'offers' => array(self::HAS_MANY, 'Offer', 'offer_discipline_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'discipline_id' => 'ID',
            'course_id' => 'Curso',
            'discipline_name' => 'Nome',
            'menu' => 'Ementa',
            'workload' => 'Carga horária',
            'objective' => 'Objetivos',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('discipline_id', $this->discipline_id);
        $criteria->compare('course_id', $this->course_id);
        $criteria->compare('discipline_name', $this->discipline_name, true);
        $criteria->compare('menu', $this->menu, true);
        $criteria->compare('workload', $this->workload);
        $criteria->compare('objective', $this->objective, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
