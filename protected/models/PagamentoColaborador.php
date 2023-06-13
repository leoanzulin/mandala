<?php

/**
 * This is the model class for table "pagamento_colaborador".
 *
 * The followings are the available columns in table 'pagamento_colaborador':
 * @property int $id
 * @property string $docente_cpf
 * @property string $tutor_cpf
 * @property string $colaborador_cpf
 * @property string $data
 * @property string $forma_pagamento
 * @property string $valor_total
 * @property string $valor_pago
 * @property string $observacoes
 *
 * The followings are the available model relations:
 * @property Docente $docente
 * @property Tutor $tutor
 * @property Colaborador $colaborador
 * @property Servico[] $servicos
 */
class PagamentoColaborador extends ActiveRecord
{
    // Guarda o CPF do colaborador, seja ele docente, tutor ou colaborador
    public $colaborador;
    public $tipoColaborador;

    public function tableName()
    {
        return 'pagamento_colaborador';
    }

    public function rules()
    {
        return array(
            array('data, forma_pagamento, valor_total, valor_pago', 'required'),
            array('forma_pagamento, observacoes', 'length', 'max' => 256),
            array('docente_cpf, tutor_cpf, colaborador_cpf, data, forma_pagamento, valor_total, valor_pago, observacoes', 'safe'),
            array('id, docente_cpf, tutor_cpf, colaborador_cpf, data, forma_pagamento, valor_total, valor_pago, observacoes', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'docente' => array(self::BELONGS_TO, 'Docente', 'docente_cpf'),
            'tutor' => array(self::BELONGS_TO, 'Tutor', 'tutor_cpf'),
            'r_colaborador' => array(self::BELONGS_TO, 'Colaborador', 'colaborador_cpf'),
            'servicos' => array(self::HAS_MANY, 'Servico', 'pagamento_colaborador_id'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'forma_pagamento' => 'Forma de pagamento',
            'valor_total' => 'Valor total (R$)',
            'valor_pago' => 'Valor pago (R$)',
            'observacoes' => 'Observações',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('data', $this->data, true);
        $criteria->compare('docente_cpf', $this->docente_cpf, true);
        $criteria->compare('tutor_cpf', $this->tutor_cpf, true);
        $criteria->compare('colaborador_cpf', $this->colaborador_cpf, true);
        $criteria->compare('forma_pagamento', $this->forma_pagamento, true);
        $criteria->compare('valor_total', $this->valor_total, true);
        $criteria->compare('valor_pago', $this->valor_pago, true);
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

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeValidate()
    {
        $this->valor_pago = str_replace(',', '.', $this->valor_pago);
        $this->valor_total = str_replace(',', '.', $this->valor_total);
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

    public function searchMesAno($mes, $ano)
    {
        $proximoMes = $mes + 1;
        $proximoAno = $ano;
        if ($proximoMes == 13) {
            $proximoMes = 1;
            $proximoAno = $ano + 1;
        }
        $mes = $mes < 10 ? "0{$mes}" : $mes;
        $dataInicial = "{$ano}-{$mes}-01";
        $dataFinal = "{$proximoAno}-{$proximoMes}-01";

        return new CActiveDataProvider('PagamentoColaborador', [
            'criteria' => [
                'condition' => "data >= '{$dataInicial}' and data < '{$dataFinal}'",
                'order' => 'data ASC',
            ],
        ]);
    }

    public function asArray()
    {
        $pagamentoColaborador = [
            'id' => $this->id,
            'tipo_colaborador' => $this->getTipoColaborador(),
            'colaborador_cpf' => $this->getColaborador()->cpf,
            'data' => $this->data,
            'servicos' => array_map(function($servico) { return $servico->asArray(); }, $this->servicos),
            'forma_pagamento' => $this->forma_pagamento,
            'valor_total' => $this->valor_total,
            'valor_pago' => $this->valor_pago,
            'observacoes' => $this->observacoes,
        ];
        return $pagamentoColaborador;
    }

}
