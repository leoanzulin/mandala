<?php
/**
 * This is the model class for table "colaborador".
 *
 * The followings are the available columns in table 'colaborador':
 * @property string $cpf
 * @property string $nome
 * @property string $sobrenome
 * @property string $email
 *
 * The followings are the available model relations:
 * @property Servico[] $servicos
 * @property PagamentoColaborador[] $pagamentos
 * @property Viagem[] $viagens
 * @property Compra[] $compras
 */
class Colaborador extends ActiveRecord
{
    public function tableName()
    {
        return 'colaborador';
    }

    public function rules()
    {
        return array(
            array('cpf, nome, sobrenome, email', 'required'),
            array('cpf, nome, sobrenome, email', 'length', 'max' => 256),
            array('email', 'email'),
            array('cpf, nome, sobrenome, email', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array(
            'servicos' => array(self::HAS_MANY, 'Servico', 'colaborador_cpf'),
            'pagamentos' => array(self::HAS_MANY, 'PagamentoColaborador', 'colaborador_cpf'),
            'viagens' => array(self::HAS_MANY, 'Viagem', 'colaborador_cpf'),
            'compras' => array(self::HAS_MANY, 'Compra', 'colaborador_cpf'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'cpf' => 'CPF',
            'nome' => 'Nome',
            'sobrenome' => 'Sobrenome',
            'email' => 'E-mail',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('cpf', $this->cpf, true);
        $criteria->addSearchCondition('nome', $this->nome, true, 'AND', 'ILIKE');
        $criteria->addSearchCondition('sobrenome', $this->sobrenome, true, 'AND', 'ILIKE');
        $criteria->compare('email', $this->email, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50
            ),
            'sort' => array(
                'attributes' => array(
                    '*',
                ),
                'defaultOrder' => array('nome' => false),
            ),
        ));
    }

    public function __toString()
    {
        return "[Colaborador {$this->nome} {$this->sobrenome} ({$this->cpf})]";
    }

    protected function beforeValidate()
    {
        $this->nome = trim($this->nome);
        $this->sobrenome = trim($this->sobrenome);
        return parent::beforeValidate();
    }

    protected function afterSave()
    {
        $usuario = Usuario::model()->findByPk($this->cpf);
        if (empty($usuario)) {
            Usuario::criarUsuario($this, "edutec{$this->cpf}");
            Utils::tornarColaborador($this->cpf);
        }
        parent::afterSave();
    }

    public function getNomeCompleto()
    {
        return "{$this->nome} {$this->sobrenome}";
    }

}
