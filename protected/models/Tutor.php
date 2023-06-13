<?php

/**
 * This is the model class for table "tutor".
 *
 * The followings are the available columns in table 'tutor':
 * @property string $cpf
 * @property string $nome
 * @property string $sobrenome
 * @property string $email
 * @property string $teleofne
 * @property string $endereco
 * @property string $numero
 * @property string $bairro
 * @property string $complemento
 * @property string $cep
 * @property boolean $mestrando_ou_doutorando_ufscar
 * @property boolean $ativo
 * @property boolean $criado_em
 * @property boolean $atualizado_em
 * @property boolean $deletado_em
 *
 * The followings are the available model relations:
 * @property Oferta[] $ofertas
 * @property Servico[] $servicos
 * @property PagamentoColaborador[] $pagamentos
 * @property Viagem[] $viagens
 * @property Compra[] $compras
 */
class Tutor extends ActiveRecord
{

    public $mestrando_ou_doutorando_ufscar_search;

    public function tableName()
    {
        return 'tutor';
    }

    public function rules()
    {
        return array(
            array('cpf, nome, sobrenome, email', 'required'),
            array('cpf, nome, sobrenome, email, telefone, endereco, numero, bairro, complemento, cep', 'length', 'max' => 256),
            array('email', 'email'),
            array('mestrando_ou_doutorando_ufscar', 'safe'),
            array('cpf, nome, sobrenome, email, telefone, endereco, numero, bairro, complemento, cep, mestrando_ou_doutorando_ufscar, mestrando_ou_doutorando_ufscar_search', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'ofertas' => array(self::MANY_MANY, 'Oferta', 'tutor_oferta(tutor_cpf, oferta_id)', 'order' => 'ano, mes'),
            'bolsas' => array(self::HAS_MANY, 'Bolsa', 'tutor_cpf'),
            'servicos' => array(self::HAS_MANY, 'Servico', 'tutor_cpf'),
            'pagamentos' => array(self::HAS_MANY, 'PagamentoColaborador', 'tutor_cpf'),
            'viagens' => array(self::HAS_MANY, 'Viagem', 'tutor_cpf'),
            'compras' => array(self::HAS_MANY, 'Compra', 'tutor_cpf'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'cpf' => 'CPF',
            'nome' => 'Nome',
            'sobrenome' => 'Sobrenome',
            'email' => 'E-mail',
            'endereco' => 'Endereço',
            'numero' => 'Número',
            'cep' => 'CEP',
            'mestrando_ou_doutorando_ufscar' => 'É mestrando ou doutorando da UFSCar?',
            'mestrando_ou_doutorando_ufscar_search' => 'Pós-graduando da UFSCar?',
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria;

        $criteria->compare('cpf', $this->cpf, true);
        $criteria->addSearchCondition('nome', $this->nome, true, 'AND', 'ILIKE');
        $criteria->addSearchCondition('sobrenome', $this->sobrenome, true, 'AND', 'ILIKE');
        $criteria->compare('email', $this->email, true);
        $criteria->compare('telefone', $this->telefone, true);
        $criteria->compare('endereco', $this->endereco, true);
        $criteria->compare('numero', $this->numero, true);
        $criteria->compare('bairro', $this->bairro, true);
        $criteria->compare('complemento', $this->complemento, true);
        $criteria->compare('cep', $this->cep, true);
        $criteria->compare('mestrando_ou_doutorando_ufscar', $this->booleanSearch($this->mestrando_ou_doutorando_ufscar_search));
        $criteria->compare('ativo', true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
            'sort' => array(
                'attributes' => array(
                    'mestrando_ou_doutorando_ufscar_search' => array(
                        'asc' => 'mestrando_ou_doutorando_ufscar',
                        'desc' => 'mestrando_ou_doutorando_ufscar DESC',
                    ),
                    '*',
                ),
                'defaultOrder' => array('nome' => false),
            ),
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function asArray()
    {
        // $ofertasEmQueAtuou = array_map(function($oferta) {
        //     return $oferta->asArraySemDadosRelacionados();
        // }, $this->ofertas);

        // $bolsas = array_map(function($bolsa) {
        //     return $bolsa->asArray();
        // }, $this->bolsas);

        return array(
            'cpf' => $this->cpf,
            'nome' => $this->nome,
            'sobrenome' => $this->sobrenome,
            'nomeCompleto' => $this->nomeCompleto,
            'email' => $this->email,
//            'ofertas' => $ofertasEmQueAtuou,
            // 'bolsas' => $bolsas,
        );
    }

    public function asArraySemOfertasEBolsas()
    {
        return array(
            'cpf' => $this->cpf,
            'nome' => $this->nome,
            'sobrenome' => $this->sobrenome,
            'nomeCompleto' => $this->nomeCompleto,
            'email' => $this->email,
        );
    }

    public static function fromJsonObject($objetoJson)
    {
        $tutor = new Tutor();
        foreach ($tutor->getAttributes() as $atributo => $valor) {
            if (isset($objetoJson->$atributo)) {
                $tutor->$atributo = $objetoJson->$atributo;
            }
        }

        return $tutor;
    }

    public function __toString()
    {
        return "[Tutor {$this->nome} {$this->sobrenome} ({$this->cpf})]";
    }

    public function beforeValidate()
    {
        $this->nome = trim($this->nome);
        $this->sobrenome = trim($this->sobrenome);
        $this->cep = str_replace('-', '', $this->cep);
        return parent::beforeValidate();
    }

    protected function afterSave()
    {
        $usuario = Usuario::model()->findByPk($this->cpf);
        if (empty($usuario)) {
            Usuario::criarUsuario($this, "edutec{$this->cpf}");
            Utils::tornarColaborador($this->cpf);
            Utils::tornarTutor($this->cpf);
        }
        parent::afterSave();
    }

    public function getNomeCompleto()
    {
        return "{$this->nome} {$this->sobrenome}";
    }

    public function desativar()
    {
        $this->ativo = false;
        $this->deletado_em = date('Y-m-d H:i:s');
        return $this->save();
    }
}
