<?php

/**
 * This is the model class for table "curso".
 *
 * The followings are the available columns in table 'curso':
 * @property string $id
 * @property string $nome
 *
 * The followings are the available model relations:
 * @property Inscricao[] $inscricaos
 * @property Habilitacao[] $habilitacaos
 */
class Curso extends CActiveRecord
{

    public function tableName()
    {
        return 'curso';
    }

    public function rules()
    {
        return array(
            array('nome', 'required'),
            array('nome', 'length', 'max' => 256),
        );
    }

    public function relations()
    {
        return array(
            'inscricaos' => array(self::HAS_MANY, 'Inscricao', 'curso_id'),
            'habilitacaos' => array(self::HAS_MANY, 'Habilitacao', 'curso_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'nome' => 'Nome',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
