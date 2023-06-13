<?php

/**
 * Controlador que implementa uma interface REST para o modelo
 * Configuracao.
 * 
 * Métodos:
 * 
 * configuracao/get&atributo=X
 * - Retorna um objeto JSON representando o atributo de configuração X.
 * 
 * configuracao/todos
 * - Retorna um vetor contendo todas as configurações do sistema.
 * 
 */
class ConfiguracaoController extends Controller
{

    public function actionGet($atributo)
    {
        $configuracao = Configuracao::mdoel()->findByPk($atributo);
        $this->respostaJSON($configuracao->toJSON());
    }

    public function actionTodos()
    {
        $configuracoes = Configuracao::model()->findAll();
        $configuracoesArray = array_map(function($configuracao) {
            return $configuracao->asArray();
        }, $configuracoes);
        $this->respostaJSON(json_encode($configuracoesArray));
    }

    /**
     * Actions não-REST
     */
    public function actionIndex()
    {
        $configuracoesForm = new ConfiguracoesForm();

        if (isset($_POST['ConfiguracoesForm'])) {
            $configuracoesForm->attributes = $_POST['ConfiguracoesForm'];
            if ($configuracoesForm->validate()) {
                $configuracoesForm->salvar();
                Yii::app()->user->setFlash('notificacao', 'Configurações atualizadas com sucesso!');
            }
        }
        
        $configuracoesForm->novaSenha = '';
        $configuracoesForm->novaSenhaConfirmacao = '';

        $this->render('index', array(
            'model' => $configuracoesForm,
        ));
    }
    
}
