<?php

/**
 * Classe CActiveRecord customizada com os métodos de transformação de
 * datas de e para o formato do banco de dados. As classes de modelo do
 * sistema devem estender esta classe.
 */
class ActiveRecord extends CActiveRecord
{

    public function init()
    {
        // Coloca o asterisco dos campos obrigatórios antes das labels
        CHtml::$afterRequiredLabel = '';
        CHtml::$beforeRequiredLabel = '* ';
        CHtml::$errorContainerTag = 'span';
        return parent::init();
    }

    /**
     * Converte as datas em formato padrão (dd/MM/yyyy hh:mm:ss) para o formato
     * do banco (yyyy-MM-dd hh:mm:ss) antes de salvar.
     *
     * @return boolean
     */
    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            foreach ($this->metadata->tableSchema->columns as $columnName => $column) {
                if (empty($this->$columnName))
                    continue;
                if ($column->dbType == 'date')
                    $this->$columnName = $this->transformarData($this->$columnName, 'dd/MM/yyyy', 'yyyy-MM-dd');
                else if ($column->dbType == 'datetime' || strpos($column->dbType, 'timestamp') === 0)
                    $this->$columnName = $this->transformarData($this->$columnName, 'dd/MM/yyyy HH:mm:ss', 'yyyy-MM-dd HH:mm:ss');
            }
            return true;
        }
        return false;
    }

    /**
     * Converte as datas do formato do banco (yyyy-MM-dd hh:mm:ss) para o
     * formato da interface (dd/MM/yyyy hh:mm:ss).
     *
     * @return boolean
     */
    protected function afterFind()
    {
        foreach ($this->metadata->tableSchema->columns as $columnName => $column) {
            if (empty($this->$columnName))
                continue;
            if ($column->dbType == 'date')
                $this->$columnName = $this->transformarData($this->$columnName, 'yyyy-MM-dd', 'dd/MM/yyyy');
            else if ($column->dbType == 'datetime' || strpos($column->dbType, 'timestamp') === 0)
                $this->$columnName = $this->transformarData($this->$columnName, 'yyyy-MM-dd HH:mm:ss', 'dd/MM/yyyy HH:mm:ss');
        }
        return parent::afterFind();
    }

    /**
     * Método auxiliar para transformar datas de um formato para outro.
     *
     * @param string $data A data que se deseja converter
     * @param string $formatoOriginal O formato em que a data está
     * @param string $novoFormato O formato para o qual a data será convertida
     * @return string Data no novo formato desejado
     */
    protected function transformarData($data, $formatoOriginal, $novoFormato)
    {
        $timestamp = CDateTimeParser::parse($data, $formatoOriginal);
        return Yii::app()->dateFormatter->format($novoFormato, $timestamp);
    }

    /**
     * Validador para comparar datas no formato dd/mm/yyyy.
     *
     * @param string $attribute O nome do atributo data da classe modelo
     *                          a ser comparado
     * @param string $params O nome do outro atributo data a ser comparado e uma
     *                       mensagem de erro associada
     */
    public function comparaDatas($attribute, $params)
    {
        $data1 = $this->$attribute;
        $data2 = $this->$params['atributoComparacao'];
        $data1 = $this->transformarData($data1, 'dd/MM/yyyy', 'yyyy-MM-dd');
        $data2 = $this->transformarData($data2, 'dd/MM/yyyy', 'yyyy-MM-dd');

        switch ($params['operador']) {
            case '<':
                if ($data1 >= $data2) {
                    $this->addError($attribute, $params['mensagem']);
                }
                break;
            case '<=':
                if ($data1 > $data2) {
                    $this->addError($attribute, $params['mensagem']);
                }
                break;
            case '>':
                if ($data1 <= $data2) {
                    $this->addError($attribute, $params['mensagem']);
                }
                break;
            case '>=':
                if ($data1 < $data2) {
                    $this->addError($attribute, $params['mensagem']);
                }
                break;
        }
    }

    /**
     * Validador para verificar se o CPF é válido.
     * 
     * @param string $attribute Atributo correspondente ao CPF
     * @param string $params Não utilizado
     */
    public function validadorCpf($attribute, $params)
    {
        $cpf = $this->$attribute;
        $cpf = preg_replace('/[^\d]/', '', $cpf);

        if (strlen($cpf) != 11) {
            $this->addError($attribute, 'CPF inválido.');
            return;
        }

        // Elimina CPFs inválidos conhecidos
        if ($cpf == '00000000000' ||
                $cpf == '11111111111' ||
                $cpf == '22222222222' ||
                $cpf == '33333333333' ||
                $cpf == '44444444444' ||
                $cpf == '55555555555' ||
                $cpf == '66666666666' ||
                $cpf == '77777777777' ||
                $cpf == '88888888888' ||
                $cpf == '99999999999') {
            $this->addError($attribute, 'CPF inválido.');
            return;
        }

        // Valida 1o digito
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += (int) substr($cpf, $i, 1) * (10 - $i);
        }
        $digito1 = 11 - ($soma % 11);
        if ($digito1 == 10 || $digito1 == 11) {
            $digito1 = 0;
        }
        if ($digito1 != (int) substr($cpf, 9, 1)) {
            $this->addError($attribute, 'CPF inválido.');
            return;
        }

        // Valida 2o digito
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += (int) substr($cpf, $i, 1) * (11 - $i);
        }
        $digito2 = 11 - ($soma % 11);
        if ($digito2 == 10 || $digito2 == 11) {
            $digito2 = 0;
        }
        if ($digito2 != (int) substr($cpf, 10, 1)) {
            $this->addError($attribute, 'CPF inválido.');
            return;
        }
    }

    protected function booleanSearch($campo)
    {
        if (empty($campo)) {
            return null;
        }
        if (is_numeric($campo)) {
            return $campo != 0 ? 1 : 0;
        }

        $campo = strtolower($campo);
        if ($campo == 'sim' || $campo == 's' || $campo == 'v') {
            return 1;
        } else if ($campo == 'não' || $campo == 'n' || $campo == 'x') {
            return 0;
        }

        return null;
    }

    protected function transformarPontoEmVirgula($numero)
    {
        return empty($numero) ? null : str_replace('.', ',', $numero);
    }

    protected function transformarVirgulaEmPonto($numero)
    {
        return empty($numero) ? null : str_replace(',', '.', $numero);
    }

}
