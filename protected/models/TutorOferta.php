<?php

/**
 * This is the model class for table "tutor_oferta".
 *
 * The followings are the available columns in table 'tutor_oferta':
 * @property string $oferta_id
 * @property string $tutor_cpf
 */
class TutorOferta extends CActiveRecord
{

    public function tableName()
    {
        return 'tutor_oferta';
    }

    public function rules()
    {
        return array(
            array('oferta_id, tutor_cpf', 'required'),
            array('tutor_cpf', 'length', 'max' => 256),
            array('oferta_id, tutor_cpf', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
        );
    }

    public function attributeLabels()
    {
        return array(
            'oferta_id' => 'Oferta',
            'tutor_cpf' => 'Tutor Cpf',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('oferta_id', $this->oferta_id, true);
        $criteria->compare('tutor_cpf', $this->tutor_cpf, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        return array(
            'oferta_id' => $this->oferta_id,
            'tutor_cpf' => $this->tutor_cpf,
        );
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if (!empty($this->tutor_cpf)) {
                while (strlen($this->tutor_cpf) < 11) {
                    $this->tutor_cpf = '0' . $this->tutor_cpf;
                }
            }
            return true;
        }
        return false;
    }

    public function estaNoBanco()
    {
        return TutorOferta::model()->findByAttributes(array(
                    'tutor_cpf' => $this->tutor_cpf,
                    'oferta_id' => $this->oferta_id,
                )) != null;
    }

}
