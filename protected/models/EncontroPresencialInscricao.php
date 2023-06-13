<?php

/**
 * This is the model class for table "encontro_presencial_inscricao".
 *
 * The followings are the available columns in table 'encontro_presencial_inscricao':
 * @property int $encontro_presencial_id
 * @property int $inscricao_id
 * @property boolean $presente
 *
 * The followings are the available model relations:
 * @property EncontroPresencial $encontro
 * @property Inscricao $aluno
 */
class EncontroPresencialInscricao extends ActiveRecord
{
    public function tableName()
    {
        return 'encontro_presencial_inscricao';
    }

    public function rules()
    {
        return array(
            array('encontro_presencial_id, inscricao_id', 'required'),
            array('presente', 'safe'),
            array('encontro_presencial_id, inscricao_id, presente', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'encontro' => array(self::BELONGS_TO, 'EncontroPresencial', 'encontro_presencial_id'),
            'aluno' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}
