<?php

/**
 * Controlador que implementa uma interface REST para o modelo
 * ComponenteCurricular.
 * 
 * Métodos:
 * 
 * componenteCurricular/get&id=X
 * - Retorna um objeto JSON representando o componente curricular de ID X.
 * 
 * componenteCurricular/todos
 * - Retorna um vetor contendo todas os componentes curriculares do sistema e
 *   suas prioridades para cada habilitacação.
 * 
 */
class ComponenteCurricularController extends Controller
{

    public function actionGet($id)
    {
        $componente = ComponenteCurricular::model()->findByPk($id);
        $this->respostaJSON($componente->toJSON());
    }

    public function actionTodos()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition("ativo IS TRUE AND nome != :projetointegrador");
        $criteria->params = [ ':projetointegrador' => Constantes::COMPONENTE_DESENVOLVIMENTO_DE_PROJETO_INTEGRADOR ];
        $criteria->order = 'nome';

        $componentes = ComponenteCurricular::model()->findAll($criteria);
        $componentesArray = array_map(function($componente) {
            return $componente->asArray();
        }, $componentes);
        $this->respostaJSON(json_encode($componentesArray));
    }

    /**
     * Actions não-REST
     */
    public function actionGerenciar()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition("ativo IS TRUE AND nome != :projetointegrador");
        $criteria->params = [ ':projetointegrador' => Constantes::COMPONENTE_DESENVOLVIMENTO_DE_PROJETO_INTEGRADOR ];
        $criteria->order = 'nome';

        $todasComponentes = ComponenteCurricular::model()->findAll($criteria);
        $componentesDesativados = ComponenteCurricular::model()->findAllByAttributes([
            'ativo' => false
        ], [
            'order' => 'nome',
        ]);
        $this->render('gerenciar', array(
            'componentes' => $todasComponentes,
            'componentesDesativados' => $componentesDesativados,
            'habilitacoes' => Habilitacao::findAllValid(),
        ));
    }

    public function actionCadastrar()
    {
        $componente = new ComponenteCurricular();

        if (isset($_POST['ComponenteCurricular'])) {
            $componente->attributes = $_POST['ComponenteCurricular'];
            if ($componente->save()) {
                $this->salvarPrioridades($componente->id);
                Yii::app()->user->setFlash('notificacao', 'Componente salva com sucesso!');
                $this->redirect(array('gerenciar'));
            } else {
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema ao salvar componente');
            }
        }

        $this->render('cadastrar', array(
            'model' => $componente,
            'habilitacoes' => Habilitacao::findAllValid(),
        ));
    }

    public function actionEditar($id)
    {
        $componente = ComponenteCurricular::model()->findByPk($id);

        if (isset($_POST['excluir'])) {
            $componente->desativar();
            Yii::app()->user->setFlash('notificacao', "Componente {$componente->nome} desativado");
            $this->redirect(['gerenciar']);
        } else if (isset($_POST['ComponenteCurricular'])) {
            $componente->attributes = $_POST['ComponenteCurricular'];
            if ($componente->save()) {
                $this->salvarPrioridades($componente->id);
                Yii::app()->user->setFlash('notificacao', 'Alterações salvas com sucesso!');
                $this->redirect(['gerenciar']);
            } else {
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema na atualização do componente');
            }
        }

        $this->render('editar', [
            'model' => $componente,
        ]);
    }

    private function salvarPrioridades($componenteId)
    {
        foreach ($_POST['ComponenteCurricular']['prioridades'] as $id => $prioridade) {
            $componenteHabilitacao = ComponenteHabilitacao::model()->findByPk(array(
                'componente_curricular_id' => $componenteId,
                'habilitacao_id' => $id,
            ));
            if (empty($componenteHabilitacao)) {
                $componenteHabilitacao = new ComponenteHabilitacao();
                $componenteHabilitacao->componente_curricular_id = $componenteId;
                $componenteHabilitacao->habilitacao_id = $id;
            }
            $componenteHabilitacao->prioridade = Constantes::LETRA_PARA_PRIORIDADE($prioridade);
            $componenteHabilitacao->save();
        }
    }

}
