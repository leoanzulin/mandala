<?php

/**
 * This is the model class for table "enrollment".
 *
 * The followings are the available columns in table 'enrollment':
 * @property integer $enrollment_id
 * @property integer $course_id
 * 
 * Dados pessoais
 * @property string $enr_document
 * @property string $enr_firstname
 * @property string $enr_lastname
 * @property string $enr_email
 * @property string $enr_birthdate
 * @property string $enr_mothername
 * @property string $enr_fathername
 * @property string $enr_civilstatus
 *
 * Endereço e telefone
 * @property string $enr_phone
 * @property string $enr_mobile
 * @property string $enr_alternativephone
 * @property string $enr_zipcode
 * @property string $enr_address
 * @property string $enr_number
 * @property string $enr_complement
 * @property string $enr_city
 * @property string $enr_state
 *
 * Informações de formação é mlehor ficar em uma tabela à parte
 * //@property string $enr_formation
 * //@property string $enr_formation_area
 * 
 * Emprego atual
 * @property string $enr_currentposition
 * @property string $enr_currentcompany
 * @property string $enr_comercialphone
 * 
 * Curso/Habilitações 
 * 
 * 
 * @property boolean $enr_public_server
 * @property string $enr_siape
 * @property boolean $enr_payment
 * @property string $enr_date
 * @property integer $enr_status
 *
 * The followings are the available model relations:
 * @property Course $course
 */
class Enrollment extends ActiveRecord
{

    const STATUS_NORMAL = 0;
    const STATUS_APPROVED = 1;
    const STATUS_REFUSED = 2;
    
    public $email_confirmation;
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'enrollment';
    }

    public function rules()
    {
        return array(
            array('enr_date', 'default', 'setOnEmpty' => true, 'value' => date('Y-m-d H:i:s')),
//            array('enr_complement, enr_siape', 'default', 'setOnEmpty' => true, 'value' => null),

            array('course_id, enr_document, enr_firstname, enr_lastname, enr_email, enr_phone, enr_zipcode, enr_address, enr_city, enr_state, enr_formation, enr_formation_area', 'required'),
            array('email_confirmation', 'required', 'on' => 'insert'),
            array('course_id, enr_siape', 'numerical', 'integerOnly' => true),
            array('enr_email, email_confirmation', 'email'),

            array('enr_firstname, enr_lastname, enr_email, enr_complement, enr_city, enr_state, enr_formation, enr_formation_area', 'length', 'max' => 256),
            array('enr_document', 'length', 'max' => 11),
            array('enr_phone',    'length', 'max' => 13),
            array('enr_mobile',   'length', 'max' => 14),
            array('enr_zipcode',  'length', 'max' => 9),
            array('enr_address',  'length', 'max' => 500),
            array('enr_siape',    'length', 'max' => 10),
            
            array('enr_document', 'validadorCpf'),

            array('email_confirmation', 'compare', 'compareAttribute' => 'enr_email', 'on' => 'insert',
                'message' => 'E-mail não confere'),
            
            array('enr_public_server, enr_payment', 'safe'),
            array('enrollment_id, course_id, enr_document, enr_firstname, enr_lastname, enr_email, enr_phone, enr_mobile, enr_zipcode, enr_address, enr_complement, enr_city, enr_state, enr_formation, enr_formation_area, enr_public_server, enr_siape, enr_payment, enr_date, enr_status', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'course' => array(self::BELONGS_TO, 'Course', 'course_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'enrollment_id' => 'ID',
            'course_id' => 'Curso',
            'enr_document' => 'CPF',
            'enr_firstname' => 'Nome',
            'enr_lastname' => 'Sobrenome',
            'enr_email' => 'E-mail',
            'email_confirmation' => 'Confirme seu e-mail',
            'enr_phone' => 'Telefone',
            'enr_mobile' => 'Celular',
            'enr_zipcode' => 'CEP',
            'enr_address' => 'Endereço',
            'enr_complement' => 'Complemento',
            'enr_city' => 'Cidade',
            'enr_state' => 'Estado',
            'enr_formation' => 'Formação',
            'enr_formation_area' => 'Área de formação',
            'enr_public_server' => 'É servidor público?',
            'enr_siape' => 'SIAPE',
            'enr_payment' => 'Está pago',
            'enr_date' => 'Data de inscrição',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('enrollment_id', $this->enrollment_id);
        $criteria->compare('course_id', $this->course_id);
        $criteria->compare('enr_document', $this->enr_document, true);
        $criteria->compare('enr_firstname', $this->enr_firstname, true);
        $criteria->compare('enr_lastname', $this->enr_lastname, true);
        $criteria->compare('enr_email', $this->enr_email, true);
        $criteria->compare('enr_phone', $this->enr_phone, true);
        $criteria->compare('enr_mobile', $this->enr_mobile, true);
        $criteria->compare('enr_zipcode', $this->enr_zipcode, true);
        $criteria->compare('enr_address', $this->enr_address, true);
        $criteria->compare('enr_complement', $this->enr_complement, true);
        $criteria->compare('enr_city', $this->enr_city, true);
        $criteria->compare('enr_state', $this->enr_state, true);
        $criteria->compare('enr_formation', $this->enr_formation, true);
        $criteria->compare('enr_formation_area', $this->enr_formation_area, true);
        $criteria->compare('enr_public_server', $this->enr_public_server);
        $criteria->compare('enr_siape', $this->enr_siape, true);
        $criteria->compare('enr_payment', $this->enr_payment);
        $criteria->compare('enr_date', $this->enr_date, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public function getFullName()
    {
        return $this->enr_firstname . ' ' . $this->enr_lastname;
    }
    
    public function approve()
    {
        $this->enr_status = Enrollment::STATUS_APPROVED;
        $this->saveAttributes(array('enr_status'));
    }
    
    public function refuse()
    {
        $this->enr_status = Enrollment::STATUS_REFUSED;
        $this->saveAttributes(array('enr_status'));
    }
    
}
