<?php

/**
 * This is the model class for table "bolsa".
 *
 * The followings are the available columns in table 'bolsa':
 * @property string $id
 * @property string $descricao
 * @property string $valor
 * @property string $data_pagamento
 * @property string $docente_cpf
 * @property string $tutor_cpf
 *
 * The followings are the available model relations:
 * @property Docente $docenteCpf
 * @property Tutor $tutorCpf
 */
class Bolsa extends ActiveRecord
{

    public function tableName()
    {
        return 'bolsa';
    }

    public function rules()
    {
        return array(
            array('descricao, valor, data_pagamento', 'required'),
            array('docente_cpf, tutor_cpf', 'length', 'max' => 256),
            array('id, descricao, valor, data_pagamento, docente_cpf, tutor_cpf', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'docente' => array(self::BELONGS_TO, 'Docente', 'docente_cpf'),
            'tutor' => array(self::BELONGS_TO, 'Tutor', 'tutor_cpf'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'descricao' => 'Descricao',
            'valor' => 'Valor',
            'data_pagamento' => 'Data Pagamento',
            'docente_cpf' => 'Docente Cpf',
            'tutor_cpf' => 'Tutor Cpf',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('descricao', $this->descricao, true);
        $criteria->compare('valor', $this->valor, true);
        $criteria->compare('data_pagamento', $this->data_pagamento, true);
        $criteria->compare('docente_cpf', $this->docente_cpf, true);
        $criteria->compare('tutor_cpf', $this->tutor_cpf, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        $nomeDocente = '';
        $nomeTutor = '';
        if ($this->docente_cpf) {
            $nomeDocente = $this->docente->nomeCompleto;
        }
        if ($this->tutor_cpf) {
            $nomeTutor = $this->tutor->nomeCompleto;
        }

        return array(
            'id' => $this->id,
            'descricao' => $this->descricao,
            'valor' => $this->valor,
            'data_pagamento' => $this->data_pagamento,
            'data_pagamento_ano_na_frente' => $this->data_pagamento,
            'docente_cpf' => $this->docente_cpf,
            'tutor_cpf' => $this->tutor_cpf,
            'docente' => $nomeDocente,
            'tutor' => $nomeTutor,
        );
    }

    public function toJSON()
    {
        // http://stackoverflow.com/questions/16764177/angular-orderby-number-sorting-as-text-in-ng-repeat
        // JSON_NUMERIC_CHECK só existe a partir do PHP 5.3.3
//        return json_encode($this->asArray(), JSON_NUMERIC_CHECK);
        return json_encode($this->asArray());
    }

    public static function fromJson($stringJson)
    {
        $objetoJson = json_decode($stringJson);

        $bolsa = new Bolsa();
        foreach ($bolsa->getAttributes() as $atributo => $valor) {
            if (isset($objetoJson->$atributo)) {
                $bolsa->$atributo = $objetoJson->$atributo;
            }
        }

        return $bolsa;
    }

    public function __toString()
    {
        $valor = number_format((float) $this->valor, 2, '.', '');
        return "{$this->descricao} - R\${$valor}";
    }

    protected function beforeSave()
    {
        if (!empty($this->docente_cpf)) {
            while (strlen($this->docente_cpf) < 11) {
                $this->docente_cpf = '0' . $this->docente_cpf;
            }
        }
        if (!empty($this->tutor_cpf)) {
            while (strlen($this->tutor_cpf) < 11) {
                $this->tutor_cpf = '0' . $this->tutor_cpf;
            }
        }
        return parent::beforeSave();
    }

}
