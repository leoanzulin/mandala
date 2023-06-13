<?php

/**
 * This is the model class for table "inscricao_habilitacao".
 *
 * The followings are the available columns in table 'inscricao_habilitacao':
 * @property string $inscricao_id
 * @property string $habilitacao_id
 * @property int $ordem Ordem em que o estudante escolheu esta habilitação
 * @property string $data_conclusao
 * @property string $processo_proex
 */
class InscricaoHabilitacao extends ActiveRecord
{
    public function tableName()
    {
        return 'inscricao_habilitacao';
    }

    public function rules()
    {
        return array(
            array('habilitacao_id, inscricao_id', 'required'),
            array('habilitacao_id, inscricao_id, ordem', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'habilitacao' => array(self::BELONGS_TO, 'Habilitacao', 'habilitacao_id'),
            'inscricao' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'habilitacao_id' => 'Habilitação',
            'inscricao_id' => 'Inscrição',
            'data_conclusao' => 'Data de conclusão',
            'processo_proex' => 'Processo ProEx',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        return array(
            'habilitacao_id' => $this->habilitacao_id,
            'inscricao_id' => $this->inscricao_id,
            'ordem' => $this->ordem,
            'processo_proex' => $this->processo_proex,
        );
    }

    public function estaNoBanco()
    {
        return self::model()->findByPk(array(
            'habilitacao_id' => $this->habilitacao_id,
            'oferta_id' => $this->oferta_id,
        )) != null;
    }

}
