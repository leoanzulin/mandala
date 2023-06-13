<?php

/**
 * This is the model class for table "inscricao_componente".
 *
 * The followings are the available columns in table 'inscricao_componente':
 * @property string $componente_curricular_id
 * @property string $inscricao_id
 * @property integer $periodo
 */
class InscricaoComponente extends CActiveRecord
{

    public function tableName()
    {
        return 'inscricao_componente';
    }

    public function rules()
    {
        return array(
            array('componente_curricular_id, inscricao_id, periodo', 'required'),
            array('periodo', 'numerical', 'integerOnly' => true),
        );
    }

    public function relations()
    {
        return array(
            'componente' => array(self::BELONGS_TO, 'ComponenteCurricular', 'componente_curricular_id'),
            'inscricao' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'componente_curricular_id' => 'Componente Curricular',
            'inscricao_id' => 'Inscricao',
            'periodo' => 'Periodo',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        return array(
            'componente_id' => $this->componente_curricular_id,
            'inscricao_id' => $this->inscricao_id,
            'periodo' => $this->periodo,
        );
    }

    public function toJSON()
    {
        return json_encode($this->asArray());
    }

}
