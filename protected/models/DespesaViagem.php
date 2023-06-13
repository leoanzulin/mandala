<?php

/**
 * This is the model class for table "despesa_viagem".
 *
 * The followings are the available columns in table 'despesa_viagem':
 * @property int $id
 * @property string $viagem_id
 * @property string $tipo
 * @property string $valor
 *
 * The followings are the available model relations:
 * @property Viagem $viagem
 */
class DespesaViagem extends ActiveRecord
{
    public function tableName()
    {
        return 'despesa_viagem';
    }

    public function rules()
    {
        return array(
            array('viagem_id, tipo, valor', 'required'),
            array('viagem_id, tipo, valor', 'safe'),
            array('id, viagem_id, tipo, valor', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'viagem' => array(self::BELONGS_TO, 'Viagem', 'viagem_id'),
        );
    }

    public function attributeLabels()
    {
        return array(

        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('viagem_id', $this->viagem_id, true);
        $criteria->compare('tipo', $this->tipo, true);
        $criteria->compare('valor', $this->valor, true);

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
        $despesa = [
            'id' => $this->id,
            'vioagem_id' => $this->viagem_id,
            'tipo' => $this->tipo,
            'valor' => $this->valor,
        ];
        return $despesa;
    }

    protected function beforeValidate()
    {
        $this->valor = str_replace(',', '.', $this->valor);
        return parent::beforeValidate();
    }
}
