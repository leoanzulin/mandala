<?php

/**
 * This is the model class for table "pagamento".
 *
 * The followings are the available columns in table 'pagamento':
 * @property string $id
 * @property string $inscricao_id
 * @property string $tipo
 * @property string $valor
 * @property string $data_pagamento
 */
class Pagamento extends ActiveRecord
{

    const TIPO_PAGAMENTO_INSCRICAO = 1;
    const TIPO_PAGAMENTO_MATRICULA = 2;
    const TIPO_PAGAMENTO_PAGOU_A_VISTA = 3;
    const TIPO_PAGAMENTO_TOTAL_PREVISTO = 4;
    const TIPO_PAGAMENTO_CURSO_EXTENSAO = 32;
    const TIPO_PAGAMENTO_CURSO_APERFEICOAMENTO = 33;

    public static function TIPO_PAGAMENTO_PARCELA($numero = null)
    {
        $parcelas = array();
        for ($i = 1; $i <= 27; $i++) {
            $parcelas[$i] = Pagamento::TIPO_PAGAMENTO_TOTAL_PREVISTO + $i;
        }

        return empty($numero) ? $parcelas : $parcelas[$numero];
    }

    public static function recuperarTipoDeItemDePagamento($tipo)
    {
        if ($tipo == 'inscricao') {
            return self::TIPO_PAGAMENTO_INSCRICAO;
        } else if ($tipo == 'matricula') {
            return self::TIPO_PAGAMENTO_MATRICULA;
        } else if ($tipo == 'pagouAVista') {
            return self::TIPO_PAGAMENTO_PAGOU_A_VISTA;
        } else if ($tipo == 'totalPrevisto') {
            return self::TIPO_PAGAMENTO_TOTAL_PREVISTO;
        } else if (preg_match('/^parcela(\d+)$/', $tipo, $matches)) {
            return self::TIPO_PAGAMENTO_PARCELA($matches[1]);
        }

        throw new Exception("Tipo de item de pagamento inválido: '{$tipo}'");
    }

    public function tableName()
    {
        return 'pagamento';
    }

    public function rules()
    {
        return array(
            array('inscricao_id, tipo, valor', 'required'),
            array('tipo', 'length', 'max' => 256),
            array('id, inscricao_id, tipo, valor, data_pagamento', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'inscricao_id' => 'Inscrição',
            'tipo' => 'Tipo',
            'valor' => 'Valor',
            'data_pagamento' => 'Data Pagamento',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('inscricao_id', $this->inscricao_id, true);
        $criteria->compare('tipo', $this->tipo, true);
        $criteria->compare('valor', $this->valor, true);
        $criteria->compare('data_pagamento', $this->data_pagamento, true);

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
        return array(
            'id' => $this->id,
            'inscricao_id' => $this->inscricao_id,
            'tipo' => $this->tipo,
            'valor' => str_replace('.', ',', $this->valor),
            'data_pagamento' => $this->data_pagamento,
        );
    }

    public static function asArrayVazio($inscricaoId, $tipo)
    {
        return array(
            'id' => 0,
            'inscricao_id' => $inscricaoId,
            'tipo' => $tipo,
            'valor' => '0,00',
            'data_pagamento' => null,
        );
    }

    public function __toString()
    {
        return "[Pagamento da inscrição de ID {$this->inscricao_id}, R\${$this->valor} em {$this->data_pagamento}, tipo {$this->tipo}]";
    }

}
