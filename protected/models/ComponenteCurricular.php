<?php

/**
 * This is the model class for table "componente_curricular".
 *
 * The followings are the available columns in table 'componente_curricular':
 * @property string $id
 * @property string $nome
 * @property integer $carga_horaria
 * @property string $ementa
 * @property string $bibliografia
 *
 * The followings are the available model relations:
 * @property Habilitacao[] $habilitacaos
 */
class ComponenteCurricular extends CActiveRecord
{

    public function tableName()
    {
        return 'componente_curricular';
    }

    public function rules()
    {
        return array(
            array('nome, carga_horaria', 'required'),
            array('carga_horaria', 'numerical', 'integerOnly' => true, 'max' => 10000, 'min' => 1),
            array('nome', 'length', 'max' => 256),
            array('nome', 'multipleUnique', 'on' => 'insert'),
            array('ementa, bibliografia', 'length', 'max' => 1048576),
            array('id, nome, carga_horaria, ementa, bibliografia', 'safe', 'on' => 'search'),
        );
    }

    public function multipleUnique($attribute, $params)
    {
        $model = self::model()->findByAttributes(array(
            $attribute => $this->$attribute,
        ));
        if (!empty($model)) {
            $this->addError($attribute, "Este {$attribute} já pertence a outro componente curricular.");
        }
    }

    public function relations()
    {
        return array(
            'habilitacaos' => array(self::MANY_MANY, 'Habilitacao', 'componente_habilitacao(componente_curricular_id, habilitacao_id)'),
            'preinscricoes' => array(self::MANY_MANY, 'Inscricao', 'preinscricao_componente(componente_curricular_id, inscricao_id)'),
            'ofertas' => [self::HAS_MANY, 'Oferta', 'componente_curricular_id', 'order' => 'ano ASC, mes ASC'],
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'nome' => 'Nome',
            'carga_horaria' => 'Carga Horaria',
            'ementa' => 'Ementa',
            'bibliografia' => 'Bibliografia',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        $componente = array(
            'id' => $this->id,
            'nome' => $this->nome,
            'ementa' => $this->ementa,
            'cargaHoraria' => $this->carga_horaria,
            'prioridades' => $this->processarPrioridades(),
        );
        $componente['ehNecessaria'] = $this->ehNecessario($componente['prioridades']);
        return $componente;
    }

    private function processarPrioridades()
    {
        $componenteHabilitacoes = ComponenteHabilitacao::findByComponente($this->id);

        $prioridades = array();
        foreach ($componenteHabilitacoes as $componenteHabilitacao) {
            $prioridades[] = array(
                'habilitacao_id' => $componenteHabilitacao['habilitacao_id'],
                'prioridade' => $componenteHabilitacao['prioridade'],
                'letra' => $componenteHabilitacao['letra'],
                'cor' => $componenteHabilitacao['cor'],
            );
        }

        return $prioridades;
    }

    private function ehNecessario($prioridades)
    {
        foreach ($prioridades as $prioridade)
            if ($prioridade['prioridade'] <= Constantes::PRIORIDADE_NECESSARIA) return true;
        return false;
    }

    public function toJSON()
    {
        return json_encode($this->asArray());
    }

    /**
     * Retorna a prioridade (em forma de letra) deste componente curricular em
     * relação à habilitação passada como parâmetro.
     * 
     * @param Habilitacao $habilitacao
     * @return Prioridade
     */
    public function prioridadeParaHabilitacao($habilitacao)
    {
        if (is_numeric($habilitacao)) {
            $habilitacao = Habilitacao::model()->findByPk($habilitacao);
        }
        if (empty($habilitacao) || $habilitacao->id == 0) {
            return '-';
        }

        $componenteHabilitacao = ComponenteHabilitacao::model()->findByAttributes(array(
            'componente_curricular_id' => $this->id,
            'habilitacao_id' => $habilitacao->id,
        ));

        return Constantes::PRIORIDADE_PARA_LETRA($componenteHabilitacao->prioridade);
    }

    /**
     * Retorna a prioridade deste componente para uma determinada habilitacao
     * em formato numérico.
     * @param type $habilitacao
     */
    public function prioridadeParaHabilitacaoNumero($habilitacao)
    {
        if (is_numeric($habilitacao)) {
            $habilitacao = Habilitacao::model()->findByPk($habilitacao);
        }
        if (empty($habilitacao) || $habilitacao->id == 0) {
            return -1;
        }

        $componenteHabilitacao = ComponenteHabilitacao::model()->findByAttributes(array(
            'componente_curricular_id' => $this->id,
            'habilitacao_id' => $habilitacao->id,
        ));

        return $componenteHabilitacao->prioridade;
    }

    public function prioridades()
    {
        $componenteHabilitacoes = ComponenteHabilitacao::findByComponente($this->id);

        $prioridades = array();
        foreach ($componenteHabilitacoes as $componenteHabilitacao) {
            $prioridades[] = array(
                'habilitacao_id' => $componenteHabilitacao['habilitacao_id'],
                'habilitacao_nome' => $componenteHabilitacao['habilitacao_nome'],
                'prioridade' => $componenteHabilitacao['prioridade'],
                'letra' => $componenteHabilitacao['letra'],
                'cor' => $componenteHabilitacao['cor'],
            );
        }
        return $prioridades;
    }

    public function __toString()
    {
        return "[{$this->nome}]";
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->atualizado_em = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }

    public function desativar()
    {
        $this->deletado_em = date('Y-m-d H:i:s');
        $this->ativo = false;
        $this->save();
    }

    public function ehProjetoIntegrador()
    {
        return $this->nome === Constantes::COMPONENTE_DESENVOLVIMENTO_DE_PROJETO_INTEGRADOR;
    }

    public function recuperarComponenteProjetoIntegrador()
    {
        return self::model()->findByAttributes([ 'nome' => Constantes::COMPONENTE_DESENVOLVIMENTO_DE_PROJETO_INTEGRADOR ]);
    }
}
