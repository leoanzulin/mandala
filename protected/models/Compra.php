<?php

/**
 * This is the model class for table "compra".
 *
 * The followings are the available columns in table 'compra':
 * @property int $id
 * @property string $docente_cpf
 * @property string $tutor_cpf
 * @property string $colaborador_cpf
 * @property string $data
 * @property string $descricao
 * @property string $local
 * @property string $valor
 *
 * The followings are the available model relations:
 * @property Docente $docente
 * @property Tutor $tutor
 * @property Colaborador $colaborador
 */
class Compra extends ActiveRecord
{
    // Guarda o CPF do colaborador, seja ele docente, tutor ou colaborador
    public $colaborador;
    public $tipoColaborador;

    public function tableName()
    {
        return 'compra';
    }

    public function rules()
    {
        return array(
            array('data, descricao, local, valor', 'required'),
            array('docente_cpf, tutor_cpf, colaborador_cpf, data, descricao, local, valor', 'safe'),
            array('id, docente_cpf, tutor_cpf, colaborador_cpf, data, descricao, local, valor', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'docente' => array(self::BELONGS_TO, 'Docente', 'docente_cpf'),
            'tutor' => array(self::BELONGS_TO, 'Tutor', 'tutor_cpf'),
            'r_colaborador' => array(self::BELONGS_TO, 'Colaborador', 'colaborador_cpf'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'descricao' => 'Descrição',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('data', $this->data, true);
        $criteria->compare('descricao', $this->descricao, true);
        $criteria->compare('local', $this->local, true);
        $criteria->compare('valor', $this->valor, true);
        $criteria->compare('docente_cpf', $this->docente_cpf, true);
        $criteria->compare('tutor_cpf', $this->tutor_cpf, true);
        $criteria->compare('colaborador_cpf', $this->colaborador_cpf, true);

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

    protected function beforeValidate()
    {
        $this->valor = str_replace(',', '.', $this->valor);
        if ($this->tipoColaborador == 'docente') {
            $this->docente_cpf = $this->colaborador;
        } else if ($this->tipoColaborador == 'tutor') {
            $this->tutor_cpf = $this->colaborador;
        } else if ($this->tipoColaborador == 'colaborador') {
            $this->colaborador_cpf = $this->colaborador;
        }
        return parent::beforeValidate();
    }

    public function getColaborador()
    {
        if (!empty($this->docente_cpf)) return $this->docente;
        if (!empty($this->tutor_cpf)) return $this->tutor;
        if (!empty($this->colaborador_cpf)) return $this->r_colaborador;
        return null;
    }

    public function getTipoColaborador()
    {
        if (!empty($this->docente_cpf)) return 'docente';
        if (!empty($this->tutor_cpf)) return 'tutor';
        if (!empty($this->colaborador_cpf)) return 'colaborador';
        return null;
    }

    public function asArray()
    {
        $compra = [
            'id' => $this->id,
            'tipo_colaborador' => $this->getTipoColaborador(),
            'colaborador_cpf' => $this->getColaborador()->cpf,
            'data' => $this->data,
            'descricao' => $this->descricao,
            'local' => $this->local,
            'valor' => $this->valor,
        ];
        return $compra;
    }

}
