<?php

/**
 * This is the model class for table "formacao".
 *
 * The followings are the available columns in table 'formacao':
 * @property string $id
 * @property string $nivel
 * @property string $curso
 * @property integer $ano_conclusao
 * @property string $instituicao
 * @property string $inscricao_id
 *
 * The followings are the available model relations:
 * @property Inscricao $inscricao
 */
class Formacao extends CActiveRecord
{

    public function tableName()
    {
        return 'formacao';
    }

    public function rules()
    {
        return array(
            array('nivel, curso, ano_conclusao, instituicao, inscricao_id', 'required'),
            array('ano_conclusao', 'numerical', 'integerOnly' => true),
            array('nivel, curso, instituicao', 'length', 'max' => 256),
            array('id, nivel, curso, ano_conclusao, instituicao, inscricao_id', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'inscricao' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'nivel' => 'Nivel',
            'curso' => 'Curso',
            'ano_conclusao' => 'Ano de conclusão',
            'instituicao' => 'Instituição',
            'inscricao_id' => 'Inscrição',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('nivel', $this->nivel, true);
        $criteria->compare('curso', $this->curso, true);
        $criteria->compare('ano_conclusao', $this->ano_conclusao);
        $criteria->compare('instituicao', $this->instituicao, true);
        $criteria->compare('inscricao_id', $this->inscricao_id, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getNivelPorExtenso()
    {
        $niveisPorExtenso = array(
            'graduacao' => 'Graduação',
            'especializacao' => 'Especialização',
            'mestrado' => 'Mestrado',
            'doutorado' => 'Doutorado',
        );
        return $niveisPorExtenso[$this->nivel];
    }

    public function asArray()
    {
        return array(
            'id' => $this->id,
            'nivel' => $this->nivel,
            'curso' => $this->curso,
            'conclusao' => $this->ano_conclusao,
            'instituicao' => $this->instituicao,
            'inscricao_id' => $this->inscricao_id,
        );
    }

}
