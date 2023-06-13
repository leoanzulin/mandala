<?php

/**
 * This is the model class for table "matricula".
 *
 * The followings are the available columns in table 'matricula':
 * @property string $id
 * @property string $cpf
 * @property string $inscricao_id
 * 
 * X@property string $modalidade Distância, presencial ou misto
 * X@property string $habilitacao1
 * X@property string $habilitacao2
 * X@property boolean $candidato_a_bolsa
 * @property string $comentarios
 *
 * The followings are the available model relations:
 * @property Inscricao $inscricao
 */
class Matricula extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'matricula';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cpf, inscricao_id', 'required'),
			array('cpf', 'length', 'max'=>30),
//            array('modalidade', 'length', 'max'=>256),
//            array('candidato_a_bolsa', 'boolean'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, cpf, inscricao_id', 'safe', 'on'=>'search'),
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
			'inscricao' => array(self::BELONGS_TO, 'Inscricao', 'inscricao_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'cpf' => 'Cpf',
			'inscricao_id' => 'Inscricao',
            'modalidade' => 'Modalidade do curso',
            'candidato_a_bolsa' => 'Candidato à bolsa?',
            'comentarios' => 'Comentários',
            'habilitacao1' => 'Habilitação prioritária',
            'habilitacao2' => 'Habilitação secundária',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('cpf',$this->cpf,true);
		$criteria->compare('inscricao_id',$this->inscricao_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Matricula the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
