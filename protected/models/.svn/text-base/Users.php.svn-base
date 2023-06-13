<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property string $user_id
 * @property string $user_password
 * @property string $user_firstname
 * @property string $user_lastname
 * @property string $user_email
 *
 * The followings are the available model relations:
 * @property Offer[] $offers
 * @property Course[] $courses
 * @property OfferStudent[] $offerStudents
 */
class Users extends CActiveRecord
{

    // Valores default
    public $user_password = '';
    public $user_password_confirm = '';
    public $user_roles = array();

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'users';
    }

    public function rules()
    {
        return array(
            array('user_id, user_password, user_password_confirm, user_firstname, user_lastname, user_email, user_roles', 'required'),
            array('user_id', 'length', 'max' => 11),
            array('user_password, user_firstname, user_lastname, user_email', 'length', 'max' => 255),
            array('user_password_confirm', 'required', 'on' => 'insert'),
            array('user_password_confirm', 'compare', 'compareAttribute' => 'user_password', 'on' => 'insert', 'on' => 'update',
                'message' => Yii::t('ed_tec', 'Passwords do not match')),
            array('user_id, user_email', 'unique'),
            array('user_id, user_password, user_firstname, user_lastname, user_email', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'offers' => array(self::HAS_MANY, 'Offer', 'offer_teacher_id'),
            'courses' => array(self::MANY_MANY, 'Course', 'course_student(ce_user_id, ce_course_id)'),
            'offerStudents' => array(self::HAS_MANY, 'OfferStudent', 'os_student_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'user_id' => 'Login',
            'user_password' => 'Senha',
            'user_password_confirm' => 'Confirme a senha',
            'user_firstname' => 'Primeiro nome',
            'user_lastname' => 'Sobrenome',
            'user_email' => 'E-mail',
            'user_roles' => 'Papéis',
            'roles' => 'Papéis',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('user_id', $this->user_id, true);
        $criteria->compare('user_password', $this->user_password, true);
        $criteria->compare('user_firstname', $this->user_firstname, true);
        $criteria->compare('user_lastname', $this->user_lastname, true);
        $criteria->compare('user_email', $this->user_email, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Hasheia a senha do usuário antes da validação dos atributos.
     * 
     * @return boolean
     */
    protected function beforeValidate()
    {
        if (parent::beforeValidate()) {
            // Só hasheia a senha se o usuário está sendo adicionado pela interface web normal
            if ($this->scenario === 'insert' || $this->scenario === 'update' && !empty($this->user_password)) {
                $this->user_password = $this->hashPassword($this->user_password);
                $this->user_password_confirm = $this->hashPassword($this->user_password_confirm);
            }
            return true;
        }
        return false;
    }
    
    /**
     * Recupera o(s) papel(éis) do usuário 
     */
    protected function afterFind()
    {
        $items = Authassignment::model()->findAllByAttributes(array(
            'userid' => $this->user_id
        ));
        $this->user_roles = array_map(function($item) {
            return $item->itemname;
        }, $items);
    }

    protected function hashPassword($user_password)
    {
        return PasswordGenerator::sha1($user_password);
    }

    /**
     * Retorna todos os usuários que têm papel 'Docente'.
     */
    public function getTeachers()
    {
        $teachersIds = Authassignment::model()->findAllByAttributes(array(
            'itemname' => 'Docente'
        ));
        return array_map(function($teacherId) {
            return Users::model()->findByPk($teacherId->userid);
        }, $teachersIds);
    }

    public function getRoles()
    {
        return implode(', ', $this->user_roles);
    }

    /**
     * Remove todos os papéis do usuário antes de excluí-lo
     */
    protected function beforeDelete()
    {
        $roles = array_keys(Rights::getAssignedRoles($this->user_id));
        foreach($roles as $role) {
            Yii::app()->authManager->revoke($role, $this->user_id);
        }
        return parent::beforeDelete();
    }

}
