<?php

/**
 * This is the model class for table "usuario".
 *
 * The followings are the available columns in table 'usuario':
 * @property string $cpf
 * @property string $nome
 * @property string $senha
 * @property string $email
 * @property string $ultimo_acesso
 */
class Usuario extends CActiveRecord
{

    public function tableName()
    {
        return 'usuario';
    }

    public function rules()
    {
        return array(
            array('cpf, senha, nome, email', 'required'),
            array('cpf, senha, nome, email', 'length', 'max' => 256),
            array('cpf, senha, nome, email', 'safe', 'on' => 'search'),
        );
    }

    public function relations()
    {
        return array();
    }

    public function attributeLabels()
    {
        return array(
            'cpf' => 'CPF',
            'email' => 'E-mail',
            'ultimo_acesso' => 'Último acesso',
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function temPapel($papel)
    {
        $registros = Authassignment::model()->findAllByAttributes(array('userid' => $this->cpf));
        $papeis = array_map(function($registro) {
            return $registro->itemname;
        }, $registros);
        return in_array($papel, $papeis);
    }

    public function atualizarUltimoAcesso()
    {
        $this->ultimo_acesso = date('Y-m-d H:i:s', time());
        $this->saveAttributes(array('ultimo_acesso'));
    }

    public function trocarSenha($novaSenha)
    {
        $hasher = new PasswordHash(8, false);
        $this->senha = $hasher->HashPassword($novaSenha);
        $this->saveAttributes(array('senha'));
    }

    public function __toString()
    {
        return "[Usuário(a) {$this->nome} ({$this->cpf})]";
    }

    /**
     * Cria um novo usuário a partir de uma inscrição.
     * 
     * @param Usuario $usuario Usuário criado.
     */
    public static function criarUsuario($inscricao, $senhaAleatoria)
    {
        $usuario = new Usuario();
        $usuario->cpf = $inscricao->cpf;
        $hasher = new PasswordHash(8, false);
        $usuario->senha = $hasher->HashPassword($senhaAleatoria);
        $usuario->nome = $inscricao->nomeCompleto;
        $usuario->email = $inscricao->email;

        if (!$usuario->save()) {
            Yii::log("Problemas na criação do usuário para a {$inscricao}. Erros: " . print_r($usuario->errors, true), 'error', 'system.models.Inscricao');
            return false;
        }

        return $usuario;
    }

}
