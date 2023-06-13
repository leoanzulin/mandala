<?php

/**
 * This is the model class for table "pagamento_aluno".
 *
 * The followings are the available columns in table 'pagamento_aluno':
 * @property int $id
 * @property int $inscricao_id
 * @property string $tipo
 * @property string $data
 * @property string $valor
 * @property string $observacoes
 *
 * The followings are the available model relations:
 * @property Inscricao $aluno
 */
class PagamentoAluno extends ActiveRecord
{
    public function tableName()
    {
        return 'pagamento_aluno';
    }

    public function rules()
    {
        return array(
            array('inscricao_id, tipo, data, valor', 'required'),
            array('tipo, observacoes', 'length', 'max' => 256),
            array('valor', 'numerical', 'max' => 1000000, 'min' => 0),
            array('inscricao_id, tipo, data, valor, observacoes', 'safe'),
            array('id, inscricao_id, tipo, data, valor, observacoes', 'safe', 'on' => 'search'),
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
            'inscricao_id' => 'Aluno',
            'observacoes' => 'Observações',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('data', $this->data, true);
        $criteria->compare('inscricao_id', $this->inscricao_id, true);
        $criteria->compare('tipo', $this->tipo, true);
        $criteria->compare('data', $this->data, true);
        $criteria->compare('observacoes', $this->observacoes, true);

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

    public function searchAluno($id)
    {
        return new CActiveDataProvider('PagamentoAluno', [
            'criteria' => [
                'condition' => 'inscricao_id = ' . $id,
                'order' => 'data ASC',
            ],
        ]);
    }

    public function somarTotal($ids)
    {
        $pagamentos = self::model()->findAllByPk($ids);
        $total = 0;
        foreach ($pagamentos as $pagamento) {
            $total += $pagamento->valor;
        }
        return $total;
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeValidate()
    {
        $this->valor = str_replace(',', '.', $this->valor);
        return parent::beforeValidate();
    }

}
