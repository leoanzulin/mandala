<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends RController
{

    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column2';

    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu = array();

    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs = array();

    public function filters()
    {
        return array('rights');
    }

    protected function resposta($statusCode = 200, $statusMessage = 'OK', $contentType = 'text/html', $corpo = '')
    {
        header('HTTP 1/1 ' . $statusCode . ' ' . $statusMessage);
        header('Content-Type: ' . $contentType);
        echo $corpo;
        Yii::app()->end();
    }

    protected function respostaJSON($corpo)
    {
        $this->resposta(200, 'OK', 'application/json', $corpo);
    }

    /**
     * Controla o redirecionamento do usuário quando ele tem que preencher
     * infromações faltantes. Este controle poderá ser removido eventualmente,
     * quando todos os usuários do sistema tiverem todas as infromações
     * preenchidas.
     */
    protected function beforeAction($action)
    {
        // O usuário sempre pode deslogar
        if ($action->controller->id == 'site' && $action->id == 'logout') {
            return true;
        }

        // Verifica se é um usuário que ainda precisa fornecer as infromações complementares
        if ($this->usuarioTemQuePreencherInformacoes($action)) {
            // Se for, o redireciona para a página até que ele preencha as informações
            $this->redirect(Yii::app()->createUrl('inscricao/informacoesComplementares'));
            return false; // Ação atual não é executada
        }

        // Verifica se é um usuário de multi-login que ainda não selecionou qual inscrição irá usar
        if ($this->usuarioTemQueEscolherMultiLogin($action)) {
            $this->redirect(Yii::app()->createUrl('multiLogin'));
            return false; // Ação atual não é executada
        }

        // Se já fez multi-login e tenta acessar página do multi-login novamente, redireciona para página inicial
        if ($action->controller->id == 'multiLogin' && !empty(Yii::app()->session['inscricao_id'])) {
            $this->redirect(Yii::app()->createUrl('aluno'));
            return false;
        }

        return true; // Ação atual é executada normalmente
    }

    private function usuarioTemQuePreencherInformacoes($action)
    {
        $usuario = Usuario::model()->findByPk(Yii::app()->user->id);

        if (empty($usuario)) // Não está logado
            return false;
        if (!$usuario->temPapel('InscritoComInformacoesFaltantes'))
            return false;
        // Verifica se é a ação de adicionar informações complementares para
        // evitar redirecionamento infinito
        if ($action->controller->id == 'inscricao' && $action->id == 'informacoesComplementares')
            return false;

        return true;
    }

    private function usuarioTemQueEscolherMultiLogin($action)
    {
        if ($action->controller->id == 'multiLogin') return false;
        $inscricoes = Inscricao::model()->findAllByAttributes([ 'cpf' => Yii::app()->user->id ]);
        return count($inscricoes) > 1 && empty(Yii::app()->session['inscricao_id']);
    }

    /**
     * Adiciona arquivos javascript à view atual e todas as suas dependências
     * de forma recursiva e sem duplicação. Os arquivos javascript devem conter
     * em suas primeiras linhas comentários //INCLUDE <arquivo> indicando suas
     * dependências.
     * 
     * @param string $arquivoInicial Caminho do script inicial
     */
    protected function adicionarArquivosJavascript($arquivoInicial)
    {
        $arquivosAAdicionar = array();
        $arquivosAAdicionarDepois = array();
        $this->adicionarArquivosJavascriptRecursivo($arquivoInicial, $arquivosAAdicionar, $arquivosAAdicionarDepois);

        $base = Yii::app()->baseUrl;
        $cs = Yii::app()->getClientScript();
        $todosArquivos = array_merge($arquivosAAdicionar, array($arquivoInicial), $arquivosAAdicionarDepois);

        foreach ($todosArquivos as $arquivo) {
            $cs->registerScriptFile("{$base}{$arquivo}.js");
        }
    }

    private function adicionarArquivosJavascriptRecursivo($arquivo, &$arquivosAAdicionar, &$arquivosAAdicionarDepois)
    {
        //$base = '/var/www/edutec/sca';   // basePath é a pasta /protected
        $base = Yii::app()->basePath . '/..';

        $handle = fopen("{$base}{$arquivo}.js", 'r');

        if (!$handle) {
            die('Problema adicionando arquivos javascript à action ' . Yii::app()->controller->id . ' ' . Yii::app()->controller->action->id);
        }

        while (($line = fgets($handle)) != false) {

            $matches = array();
            if (preg_match('/^\/\/INCLUDE (.*)$/', $line, $matches)) {
                $arquivo = $matches[1];
                // Evita adicionar arquivos repetidos e dependências cíclicas
                if (!in_array($arquivo, $arquivosAAdicionar) && !in_array($arquivo, $arquivosAAdicionarDepois)) {
                    // Busca em profundidade
                    $this->adicionarArquivosJavascriptRecursivo($arquivo, $arquivosAAdicionar, $arquivosAAdicionarDepois);
                    array_push($arquivosAAdicionar, $arquivo);
                }
            } else if (preg_match('/^\/\/INCLUDE_AFTER (.*)$/', $line, $matches)) {
                $arquivo = $matches[1];
                if (!in_array($arquivo, $arquivosAAdicionar) && !in_array($arquivo, $arquivosAAdicionarDepois)) {
                    $this->adicionarArquivosJavascriptRecursivo($arquivo, $arquivosAAdicionar, $arquivosAAdicionarDepois);
                    array_push($arquivosAAdicionarDepois, $arquivo);
                }
            } else {
                break;
            }
        }

        fclose($handle);
    }

}
