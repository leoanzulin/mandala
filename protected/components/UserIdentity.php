<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{

    private $_id;

    public function __construct($username, $password, $ldap = null)
    {
        parent::__construct($username, $password);
    }

    /**
     * Autentica um usuário no sistema, baseado nas credenciais fornecidas nas
     * variáveis $this->username e $this->password.
     *
     * Autenticação
     * http://www.yiiframework.com/doc/guide/1.1/en/topics.auth
     *
     * Códigos de erro:
     * http://www.yiiframework.com/doc/api/1.1/CBaseUserIdentity#c2217
     *
     * @return boolean Verdadeiro se o usuário se autenticou com sucesso, falso
     *                 caso contrário
     */
    public function authenticate()
    {
        if ($this->usuarioEstaNoBancoESenhaEstaCorreta($this->username, $this->password) === true) {
            Yii::log("Usuário '{$this->username}' se logou no sistema.", 'info', 'system.components.UserIdentity');
            // Atribuir a propriedade _id é importante para o restante do sistema
            $this->_id = $this->username;
            $this->errorCode = self::ERROR_NONE;
            return true;
        }

        Yii::log("Erro no login do usuário '{$this->username}' - usuário não autenticado.", 'info', 'system.components.UserIdentity');
        $this->errorCode = self::ERROR_USERNAME_INVALID;
        return false;
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * Verifica se o usuário está presente no banco e verifica se suas
     * credenciais são válidas.
     *
     * @param $username Login fornecido pelo usuário
     * @param $password Senha fornecida pelo usuário
     * @return Verdadeiro se o usuário está no banco e suas credenciais estão
     *         corretas, falso caso contrário
     */
    private function usuarioEstaNoBancoESenhaEstaCorreta($username, $password)
    {
        $usuario = Usuario::model()->findByPk($username);
        if (empty($usuario)) {
            return false;
        }
        $hasher = new PasswordHash(8, false);
        return $hasher->CheckPassword($password, $usuario->senha);
    }

}
