<?php

/**
 * Controlador responsável pela funcionalidade de troca de senha de um usuário.
 */
class TrocarSenhaController extends Controller
{

    /**
     * Ação responsável por verificar um token de requisição de troca de senha e
     * realizar a troca de senha de fato.
     * 
     * Algoritmo:
     * 1. Hashear $id e verificar se há uma requisição correspondente na tabela
     *    resetar_senha
     * 2. Verificar se o tempo que passou é menor que 24 horas
     * 3. Se estiver OK, pedir nova senha do usuário
     * 4. Atualizar senha
     */
    public function actionIndex($id)
    {
        // Verifica se o ID corresponde a alguma requisição de troca de senha
        $hashDoToken = hash('sha256', $id);
        $resetarSenha = ResetarSenha::model()->findByPk($hashDoToken);

        $this->redirecionarParaPaginaDeFalhaSeHaProblemaComToken($resetarSenha);

        $usuario = Usuario::model()->findByPk($resetarSenha->usuario_cpf);
        $ocorreuErro = false;

        if (isset($_POST['salvar'])) {
            $ocorreuErro = $this->processarTrocaDeSenhaSeEstiverOkSenaoRetornaVerdadeiro($usuario, $resetarSenha);
        }

        $this->render('index', array(
            'nome' => $usuario->nome,
            'ocorreuErro' => $ocorreuErro,
        ));
    }

    private function redirecionarParaPaginaDeFalhaSeHaProblemaComToken($resetarSenha)
    {
        // Se houve algum problema com o token mostra a página de falha
        if (empty($resetarSenha) || $resetarSenha->passouMaisDeUmDia()) {
            $link = Yii::app()->createUrl('aluno/trocarSenha');
            $this->render('falha', array('link' => $link));
        }
    }

    private function processarTrocaDeSenhaSeEstiverOkSenaoRetornaVerdadeiro($usuario, $resetarSenha)
    {
        if ($this->senhasEstaoOk($_POST['senha'], $_POST['senha_confirmar'])) {
            $usuario->trocarSenha($_POST['senha']);
            ResetarSenha::model()->deleteAllByAttributes(array('usuario_cpf' => $resetarSenha->usuario_cpf));

            Yii::app()->user->setFlash('notificacao', 'Senha trocada com sucesso!');
            Yii::log("{$usuario} trocou de senha.", 'info', 'system.controllers.TrocarSenhaController');
            $this->redirecionarParaOLocalAdequado($usuario);
        }
        return true;
    }

    private function senhasEstaoOk($senha, $confirmarSenha)
    {
        // Senha vazia
        if (empty($senha) || empty($confirmarSenha))
            return false;
        // Senha contém espaços
        if ($senha != trim($senha) || $confirmarSenha != trim($confirmarSenha))
            return false;
        if ($senha !== $confirmarSenha)
            return false;

        return true;
    }

    private function redirecionarParaOLocalAdequado($usuario)
    {
        // Usuário autenticado
        if (!Yii::app()->user->isGuest) {
            if ($usuario->temPapel('Admin')) {
                $this->redirect(Yii::app()->createUrl('admin/gerenciarInscricoes'));
            }
            $this->redirect(Yii::app()->createUrl('aluno'));
        }

        // Usuário não está autenticado
        $this->redirect(Yii::app()->createUrl('site/login'));
    }

}
