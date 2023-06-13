<?php

/**
 * This is the model class for table "encontro_presencial_responsavel".
 *
 * The followings are the available columns in table 'encontro_presencial_responsavel':
 * @property int $encontro_presencial_id
 * @property string $docente_cpf
 * @property string $tutor_cpf
 * @property string $colaborador_cpf
 *
 * The followings are the available model relations:
 * @property EncontroPresencial $encontro
 * @property Docente $docente
 * @property Tutor $tutor
 * @property Colaborador $colaborador
 */
class EncontroPresencialResponsavel extends ActiveRecord
{
    // Guarda o CPF do colaborador, seja ele docente, tutor ou colaborador
    public $colaborador;
    public $tipoColaborador;

    public function tableName()
    {
        return 'encontro_presencial_responsavel';
    }

    public function rules()
    {
        return array(
            array('encontro_presencial_id', 'required'),
            array('docente_cpf, tutor_cpf, colaborador_cpf', 'safe'),
            array('docente_cpf, tutor_cpf, colaborador_cpf', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'encontro' => array(self::BELONGS_TO, 'EncontroPresencial', 'encontro_presencial_id'),
            'docente' => array(self::BELONGS_TO, 'Docente', 'docente_cpf'),
            'tutor' => array(self::BELONGS_TO, 'Tutor', 'tutor_cpf'),
            'r_colaborador' => array(self::BELONGS_TO, 'Colaborador', 'colaborador_cpf'),
        );
    }

    public function attributeLabels()
    {
        return array(
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeValidate()
    {
        if ($this->tipoColaborador == 'docente') {
            $this->docente_cpf = $this->colaborador;
        } else if ($this->tipoColaborador == 'tutor') {
            $this->tutor_cpf = $this->colaborador;
        } else if ($this->tipoColaborador == 'colaborador') {
            $this->colaborador_cpf = $this->colaborador;
        }
        return parent::beforeValidate();
    }

    public function getResponavel()
    {
        if ($this->docente_cpf) return Docente::model()->findByPk($this->docente_cpf);
        if ($this->tutor_cpf) return Tutor::model()->findByPk($this->tutor_cpf);
        if ($this->colaborador_cpf) return Colaborador::model()->findByPk($this->colaborador_cpf);
        return null;
    }

}
