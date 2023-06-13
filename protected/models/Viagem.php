<?php

/**
 * This is the model class for table "viagem".
 *
 * The followings are the available columns in table 'viagem':
 * @property int $id
 * @property string $docente_cpf
 * @property string $tutor_cpf
 * @property string $colaborador_cpf
 * @property string $local
 * @property string $data_ida
 * @property string $data_volta
 *
 * The followings are the available model relations:
 * @property Docente $docente
 * @property Tutor $tutor
 * @property Colaborador $colaborador
 * @property DespesaViagem[] $despesas
 */
class Viagem extends ActiveRecord
{
    // Guarda o CPF do colaborador, seja ele docente, tutor ou colaborador
    public $colaborador;
    public $tipoColaborador;

    public function tableName()
    {
        return 'viagem';
    }

    public function rules()
    {
        return array(
            array('local, data_ida, data_volta', 'required'),
            array('docente_cpf, tutor_cpf, colaborador_cpf, local, data_ida, data_volta', 'safe'),
            array('id, docente_cpf, tutor_cpf, colaborador_cpf, local, data_ida, data_volta', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'docente' => array(self::BELONGS_TO, 'Docente', 'docente_cpf'),
            'tutor' => array(self::BELONGS_TO, 'Tutor', 'tutor_cpf'),
            'r_colaborador' => array(self::BELONGS_TO, 'Colaborador', 'colaborador_cpf'),
            'despesas' => array(self::HAS_MANY, 'DespesaViagem', 'viagem_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'data_ida' => 'Data de ida',
            'data_volta' => 'Data de volta',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('local', $this->local, true);
        $criteria->compare('data_ida', $this->data_ida, true);
        $criteria->compare('data_volta', $this->data_volta, true);
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
        $viagem = [
            'id' => $this->id,
            'tipo_colaborador' => $this->getTipoColaborador(),
            'colaborador_cpf' => $this->getColaborador()->cpf,
            'data_ida' => $this->data_ida,
            'data_volta' => $this->data_volta,
            'local' => $this->local,
            'despesas' => array_map(function($despesa) { return $despesa->asArray(); }, $this->despesas),
        ];
        return $viagem;
    }

}
