<?php

/**
 * This is the model class for table "course".
 *
 * The followings are the available columns in table 'course':
 * @property integer $course_id
 * @property string $course_name
 *
 * The followings are the available model relations:
 * @property Enrollment[] $enrollments
 * @property Users[] $users
 * @property Discipline[] $disciplines
 */
class Course extends CActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'course';
    }

    public function rules()
    {
        return array(
            array('course_name', 'required'),
            array('course_name', 'length', 'max' => 500),
            array('course_id, course_name', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'enrollments' => array(self::HAS_MANY, 'Enrollment', 'course_id'),
            'users' => array(self::MANY_MANY, 'Users', 'course_student(ce_course_id, ce_user_id)'),
            'disciplines' => array(self::HAS_MANY, 'Discipline', 'course_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'course_id' => 'ID',
            'course_name' => 'Nome',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('course_id', $this->course_id);
        $criteria->compare('course_name', $this->course_name, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
    
    public function getNumberOfEnrollments()
    {
        $enrollments = Enrollment::model()->findAllByAttributes(array(
            'course_id' => $this->course_id
        ));
        return count($enrollments);
    }

    public function getNumberOfAcceptedEnrollments()
    {
        $acceptedEnrollments = Enrollment::model()->findAllByAttributes(array(
            'course_id' => $this->course_id,
            'enr_status' => Enrollment::STATUS_APPROVED
        ));
        return count($acceptedEnrollments);
    }
    
}
