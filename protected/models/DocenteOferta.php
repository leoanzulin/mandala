<?php

/**
 * This is the model class for table "docente_oferta".
 *
 * The followings are the available columns in table 'docente_oferta':
 * @property string $oferta_id
 * @property string $docente_cpf
 */
class DocenteOferta extends CActiveRecord
{

    public function tableName()
    {
        return 'docente_oferta';
    }

    public function rules()
    {
        return array(
            array('oferta_id, docente_cpf', 'required'),
            array('oferta_id, docente_cpf', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
//            'inscricao' => array(self::MANY_MANY, 'Inscricao', 'preinscricao_componente(componente_curricular_id, inscricao_id)'),
//            'componente' => array(self::BELONGS_TO, 'ComponenteCurricular', 'componente_curricular_id'),
//            'inscricao' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'oferta_id' => 'Oferta',
            'docente_cpf' => 'Docente',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        return array(
            'oferta_id' => $this->oferta_id,
            'docente_cpf' => $this->docente_cpf,
        );
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if (!empty($this->docente_cpf)) {
                while (strlen($this->docente_cpf) < 11) {
                    $this->docente_cpf = '0' . $this->docente_cpf;
                }
            }
            return true;
        }
        return false;
    }

    public function estaNoBanco()
    {
        return DocenteOferta::model()->findByAttributes(array(
                    'docente_cpf' => $this->docente_cpf,
                    'oferta_id' => $this->oferta_id,
                )) != null;
    }

}
