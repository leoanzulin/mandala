<?php

/**
 * This is the model class for table "habilitacao".
 *
 * The followings are the available columns in table 'habilitacao':
 * @property string $id
 * @property string $nome
 * @property string $letra
 * @property string $curso_id
 *
 * The followings are the available model relations:
 * @property Inscricao[] $inscricoes
 * @property ComponenteCurricular[] $componenteCurriculars
 * @property Curso $curso
 */
class Habilitacao extends CActiveRecord
{

    public function tableName()
    {
        return 'habilitacao';
    }

    public function rules()
    {
        return array(
            array('nome, curso_id, cor, letra', 'required'),
            array('nome, cor', 'length', 'max' => 256),
            array('letra', 'length', 'max' => 1),
            array('nome, letra', 'multipleUnique', 'on' => 'insert'),
            array('id, nome, letra, curso_id, cor', 'safe', 'on' => 'search'),
        );
    }

    public function multipleUnique($attribute, $params)
    {
        $model = self::model()->findByAttributes(array(
            $attribute => $this->$attribute,
        ));
        if (!empty($model)) {
            $this->addError($attribute, "Este(a) {$attribute} já pertence a outra habilitação.");
        }
    }

    public function relations()
    {
        return array(
            'componenteCurriculars' => array(self::MANY_MANY, 'ComponenteCurricular', 'componente_habilitacao(habilitacao_id, componente_curricular_id)'),
            'curso' => array(self::BELONGS_TO, 'Curso', 'curso_id'),
            'inscricoes' => array(self::MANY_MANY, 'Inscricao', 'inscricao_habilitacao(inscricao_id, habilitacao_id)'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'curso_id' => 'Curso',
            'cor' => 'Cor',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function findByLetra($letra)
    {
        return self::model()->findByAttributes(array('letra' => ucfirst($letra)));
    }

    // Remove a habilitação "Nenhuma", que tem ID 0
    public static function findAllValid()
    {
        $habilitacoes = self::model()->findAllByAttributes(array(), 'id > 0');
        usort($habilitacoes, function($a, $b) {
            $order = [ 3, 4, 1, 2, 5 ];
            foreach ($order as $id) {
                if ($a->id === $id) return -1;
                if ($b->id === $id) return 1;
            }
            return $a < $b ? -1 : 1;
        });
        return $habilitacoes;
    }

    public function asArray()
    {
        return array(
            'id' => $this->id,
            'nome' => $this->nome,
            'letra' => $this->letra,
            'cor' => $this->cor,
        );
    }

    public function __toString()
    {
        return "{$this->nome} ({$this->letra})";
    }

}
