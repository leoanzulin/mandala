<?php

/**
 * This is the model class for table "preinscricao_componente".
 *
 * The followings are the available columns in table 'preinscricao_componente':
 * @property string $componente_curricular_id
 * @property string $inscricao_id
 * @property integer $periodo
 */
class PreinscricaoComponente extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'preinscricao_componente';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('componente_curricular_id, inscricao_id, periodo', 'required'),
			array('periodo', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('componente_curricular_id, inscricao_id, periodo', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
//            'inscricao' => array(self::MANY_MANY, 'Inscricao', 'preinscricao_componente(componente_curricular_id, inscricao_id)'),
            'componente' => array(self::BELONGS_TO, 'ComponenteCurricular', 'componente_curricular_id'),
            'inscricao' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'componente_curricular_id' => 'Componente Curricular',
			'inscricao_id' => 'Inscricao',
			'periodo' => 'Periodo',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('componente_curricular_id',$this->componente_curricular_id,true);
		$criteria->compare('inscricao_id',$this->inscricao_id,true);
		$criteria->compare('periodo',$this->periodo);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PreinscricaoComponente the static model class
	 */
	public static function model($className=__CLASS__)
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
