<?php

/**
 * This is the model class for table "sintese_componente".
 *
 * The followings are the available columns in table 'sintese_componente':
 * @property int $id
 * @property int $tcc_id
 * @property int $componente_curricular_id
 * @property int $ordem
 * @property string descricao
 * @property string reflexao
 *
 * The followings are the available model relations:
 * @property Tcc $tcc
 * @property ComponenteCurricular $componente_curricular
 */
class SinteseComponente extends CActiveRecord
{
    public function tableName()
    {
        return 'sintese_componente';
    }

    public function rules()
    {
        return [
            ['tcc_id, componente_curricular_id, ordem', 'required'],
            ['id', 'required', 'on' => 'update'],
            ['ordem', 'numerical', 'integerOnly' => true],
            ['id, tcc_id, componente_curricular_id, ordem, descricao, reflexao', 'safe'],
        ];
    }

    public function relations()
    {
        return [
            'tcc' => [self::BELONGS_TO, 'Tcc', 'tcc_id'],
            'componente_curricular' => [self::BELONGS_TO, 'ComponenteCurricular', 'componente_curricular_id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'componente_curricular_id' => 'Componente Curricular',
            'descricao' => 'Descrição',
            'reflexao' => 'Reflexão',
        ];
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
