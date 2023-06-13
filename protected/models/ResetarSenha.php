<?php

class ResetarSenha extends CActiveRecord
{

    public function tableName()
    {
        return 'resetar_senha';
    }

    public function rules()
    {
        return array(
            array('id, datahora, usuario_cpf', 'required'),
        );
    }

    public function relations()
    {
        return array(
            'usuario' => array(self::HAS_MANY, 'Usuario', 'usuario_id'),
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Verifica se passou mais de um dia desde a requisição de mudança de senha.
     * @return boolean
     */
    public function passouMaisDeUmDia()
    {
        $agora = time();
        $segundosEmUmDia = 86400;
        return $agora - strtotime($this->datahora) > $segundosEmUmDia;
    }

    /**
     * 1. Gerar identificador para trocar senha e adicionar na tabela resetar_senha
     * 2. Enviar e-mail para o usuário com link para trocar a senha
     */
    public static function fazerSolicitacao($cpf)
    {
        $token = self::gerarNovaStringPseudoAleatoria();
        self::registrarSolicitacao($token, $cpf);

        $usuario = Usuario::model()->findByPk($cpf);
        $link = Yii::app()->createAbsoluteUrl('/trocarSenha&id=' . $token);

        // Parace que há muitos usuários sem e-mail no sistema, por algum motivo.
        $inscricoes = Inscricao::model()->findAllByCpf($cpf);
        $usuarioTemEmail = false;

        foreach ($inscricoes as $inscricao) {
            if (!empty($inscricao->email)) {
                $usuario->email = $inscricao->email;
                $usuario->save();
                Email::mensagemResetarEmail($inscricao->email, $link);
                $usuarioTemEmail = true;
            }
        }

        if ($usuarioTemEmail == false) {
            throw new Exception('Seu usuário está sem e-mail cadastrado no sistema, entre em contato com a secretaria para regularizar seu cadastro.');
        }

        Yii::app()->user->setFlash('notificacao', 'Um e-mail foi enviado para você com instruções para troca de senha');
        Yii::log("{$usuario} solicitou troca de senha.", 'info', 'system.models.ResetarSenha');
    }

    /**
     * Gera uma nova string pseudo aleatória para servir de hash para troca de
     * senha.
     */
    private static function gerarNovaStringPseudoAleatoria()
    {
        $stringPseudoAleatoria = '';
        // Evita conflito de chaves
        do {
            $stringPseudoAleatoria = GeradorSenha::pseudoAleatoria(64, 0);
            $hash = hash('sha256', $stringPseudoAleatoria);
        } while (ResetarSenha::model()->findByPk($hash) != null);
        return $stringPseudoAleatoria;
    }

    private static function registrarSolicitacao($stringPseudoAleatoria, $cpf)
    {
        $resetarSenha = new ResetarSenha();
        $resetarSenha->id = hash('sha256', $stringPseudoAleatoria);
        $resetarSenha->datahora = date('Y-m-d H:i:s', time());
        $resetarSenha->usuario_cpf = $cpf;
        $resetarSenha->save();
    }

}
