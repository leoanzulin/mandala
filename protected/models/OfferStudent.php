<?php

/**
 * This is the model class for table "offer_student".
 *
 * The followings are the available columns in table 'offer_student':
 * @property integer $os_offer_discipline_id
 * @property string $os_offer_teacher_id
 * @property string $os_offer_start_date
 * @property string $os_offer_end_date
 * @property string $os_student_id
 * @property double $os_grade
 * @property double $os_frequency
 *
 * The followings are the available model relations:
 * @property Users $osStudent
 * @property Offer $osOfferStartDate
 * @property Offer $osOfferTeacher
 * @property Offer $osOfferDiscipline
 * @property Offer $osOfferEndDate
 */
class OfferStudent extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'offer_student';
    }

    public function rules()
    {
        return array(
            array('os_offer_discipline_id, os_offer_teacher_id, os_offer_start_date, os_offer_end_date, os_student_id, os_grade, os_frequency', 'required'),
            array('os_offer_discipline_id', 'numerical', 'integerOnly' => true),
            array('os_grade, os_frequency', 'numerical'),
            array('os_offer_teacher_id, os_student_id', 'length', 'max' => 11),
            array('os_offer_discipline_id, os_offer_teacher_id, os_offer_start_date, os_offer_end_date, os_student_id, os_grade, os_frequency', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'osStudent' => array(self::BELONGS_TO, 'Users', 'os_student_id'),
            'osOfferStartDate' => array(self::BELONGS_TO, 'Offer', 'os_offer_start_date'),
            'osOfferTeacher' => array(self::BELONGS_TO, 'Offer', 'os_offer_teacher_id'),
            'osOfferDiscipline' => array(self::BELONGS_TO, 'Offer', 'os_offer_discipline_id'),
            'osOfferEndDate' => array(self::BELONGS_TO, 'Offer', 'os_offer_end_date'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'os_offer_discipline_id' => 'Os Offer Discipline',
            'os_offer_teacher_id' => 'Os Offer Teacher',
            'os_offer_start_date' => 'Os Offer Start Date',
            'os_offer_end_date' => 'Os Offer End Date',
            'os_student_id' => 'Os Student',
            'os_grade' => 'Os Grade',
            'os_frequency' => 'Os Frequency',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('os_offer_discipline_id', $this->os_offer_discipline_id);
        $criteria->compare('os_offer_teacher_id', $this->os_offer_teacher_id, true);
        $criteria->compare('os_offer_start_date', $this->os_offer_start_date, true);
        $criteria->compare('os_offer_end_date', $this->os_offer_end_date, true);
        $criteria->compare('os_student_id', $this->os_student_id, true);
        $criteria->compare('os_grade', $this->os_grade);
        $criteria->compare('os_frequency', $this->os_frequency);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

}
