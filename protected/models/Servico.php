<?php

/**
 * This is the model class for table "servico".
 *
 * The followings are the available columns in table 'servico':
 * @property int $id
 * @property string $pagamento_colaborador_id
 * @property string $funcao
 * @property string $valor
 * @property string $descricao
 *
 * The followings are the available model relations:
 * @property PagamentoColaborador $pagamento
 */
class Servico extends ActiveRecord
{
    public function tableName()
    {
        return 'servico';
    }

    public function rules()
    {
        return array(
            array('pagamento_colaborador_id, funcao, valor', 'required'),
            array('funcao, descricao', 'length', 'max' => 256),
            array('pagamento_colaborador_id, funcao, descricao, valor', 'safe'),
            array('id, pagamento_colaborador_id, funcao, descricao, valor', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'pagamento' => array(self::BELONGS_TO, 'PagamentoColaborador', 'pagamento_colaborador_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'pagamento_colaborador_id' => 'Pagamento',
            'funcao' => 'Função',
            'descricao' => 'Descrição',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('docente_cpf', $this->docente_cpf, true);
        $criteria->compare('tutor_cpf', $this->tutor_cpf, true);
        $criteria->compare('colaborador_cpf', $this->colaborador_cpf, true);
        $criteria->compare('funcao', $this->funcao, true);
        $criteria->compare('valor', $this->valor, true);
        $criteria->compare('descricao', $this->descricao, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
            'sort' => array(
                'attributes' => array(
                    '*',
                ),
            ),
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        return [
            'id' => $this->id,
            'pagamento_colaborador_id' => $this->pagamento_colaborador_id,
            'funcao' => $this->funcao,
            'valor' => $this->valor,
            'descricao' => $this->descricao,
        ];
    }

}
