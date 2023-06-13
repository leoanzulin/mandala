<?php

/**
 * Controlador que implementa uma interface REST para o modelo Habilitacao.
 * 
 * Métodos:
 * 
 * habilitacao/get
 * - Retorna um vetor com a(s) habilitação(ões) do aluno logado no momento.
 * 
 * habilitacao/daInscricao&id=X
 * - Retorna um vetor com a(s) habilitação(ões) da inscrição cujo ID é X.
 * 
 */
class HabilitacaoController extends Controller
{

    public function actionGet()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $this->habilitacoesDaInscricaoEmFormatoJSON($inscricao);
    }

    public function actionTodos()
    {
        $habilitacoes = Habilitacao::findAllValid();

        $habilitacoesArray = array_map(function($habilitacao) {
            return $habilitacao->asArray();
        }, $habilitacoes);

        $this->respostaJSON(json_encode($habilitacoesArray));
    }

    public function actionDaInscricao($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);
        $this->habilitacoesDaInscricaoEmFormatoJSON($inscricao);
    }

    private function habilitacoesDaInscricaoEmFormatoJSON($inscricao)
    {
        $habilitacoes = $inscricao->recuperarHabilitacoes();

        $habilitacoesArray = array();
        foreach ($habilitacoes as $habilitacao) {
            $habilitacoesArray[] = $habilitacao->asArray();
        }

        $this->respostaJSON(json_encode($habilitacoesArray));
    }

    // Actions não-REST

    public function actionGerenciar()
    {
        $this->render('gerenciar', array(
            'habilitacoes' => Habilitacao::findAllValid(),
        ));
    }

    public function actionCadastrar()
    {
        $habilitacao = new Habilitacao();

        if (isset($_POST['Habilitacao'])) {
            $habilitacao->attributes = $_POST['Habilitacao'];
            $habilitacao->curso_id = 1;
            if ($habilitacao->save()) {
                $this->associarHabilitacaoATodosOsComponentes($habilitacao);
                Yii::app()->user->setFlash('notificacao', 'Habilitação salva com sucesso!');
                $this->redirect(array('gerenciar'));
            } else {
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema ao salvar habilitação');
            }
        }

        $this->render('cadastrar', array(
            'model' => $habilitacao,
        ));
    }

    private function associarHabilitacaoATodosOsComponentes($habilitacao)
    {
        $componentes = ComponenteCurricular::model()->findAll();
        foreach ($componentes as $componente) {
            $prioridade = new ComponenteHabilitacao();
            $prioridade->componente_curricular_id = $componente->id;
            $prioridade->habilitacao_id = $habilitacao->id;
            $prioridade->prioridade = Constantes::PRIORIDADE_LIVRE;
            $prioridade->save();
        }
    }

    public function actionEditar($id)
    {
        $habilitacao = Habilitacao::model()->findByPk($id);

        if (isset($_POST['Habilitacao'])) {
            $habilitacao->attributes = $_POST['Habilitacao'];
            $habilitacao->curso_id = 1;
            if ($habilitacao->save()) {
                Yii::app()->user->setFlash('notificacao', 'Habilitação salva com sucesso!');
                $this->redirect(array('gerenciar'));
            } else {
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema ao salvar habilitação');
            }
        }

        $this->render('editar', array(
            'model' => $habilitacao,
        ));
    }

}
