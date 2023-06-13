<?php

class EncontroController extends Controller
{

    public function actionGerenciar()
    {
        $model = new EncontroPresencial('search');
        $model->unsetAttributes();
        if (isset($_GET['EncontroPresencial'])) {
            $model->attributes = $_GET['EncontroPresencial'];
        }

        $this->render('gerenciar', array(
            'model' => $model,
        ));
    }

    public function actionCadastrar()
    {
        $model = new EncontroPresencial();

        if (isset($_POST['EncontroPresencial'])) {
            $model->attributes = $_POST['EncontroPresencial'];

            if ($model->validate() && $model->save()) {

                if (!empty($_POST['EncontroPresencial']['responsaveis'])) {
                    foreach ($_POST['EncontroPresencial']['responsaveis'] as $responsavel) {
                        $encontroResponsavel = new EncontroPresencialResponsavel;
                        $encontroResponsavel->encontro_presencial_id = $model->id;
                        [$encontroResponsavel->tipoColaborador, $encontroResponsavel->colaborador] = explode('_', $responsavel);
                        $encontroResponsavel->save();
                    }
                }
                if (!empty($_POST['EncontroPresencial']['alunos'])) {
                    foreach ($_POST['EncontroPresencial']['alunos'] as $inscricaoId) {
                        $encontroAluno = new EncontroPresencialInscricao;
                        $encontroAluno->encontro_presencial_id = $model->id;
                        $encontroAluno->inscricao_id = $inscricaoId;
                        $encontroAluno->save();
                    }
                }

                Yii::app()->user->setFlash('notificacao', "Encontro presencial cadastrado com sucesso!");
                Yii::log("{$model} foi cadastrado.", 'info', 'system.controllers.EncontroController');
                $this->redirect(['encontro/editar', 'id' => $model->id]);
            } else {
                $erros = $model->errors;
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema ao salvar encontro presencial');
                Yii::log("Ocorreram problemas ao salvar o encontro presencial. Erros: " . print_r($erros, true), 'error', 'system.controllers.EncontroController');
            }
        }

        $this->adicionarArquivosJavascript('/js/pikaday1.8.2.min');

        $inscricoes = Inscricao::model()->findAllByAttributes([
            'status' => 3,
        ], [
            'order' => 'nome, sobrenome',
        ]);

        $this->render('cadastrar', [
            'model' => $model,
            'colaboradores' => $this->recuperarColaboradores(),
            'inscricoes' => $inscricoes,
        ]);
    }

    private function recuperarColaboradores()
    {
        $docentes = Docente::model()->findAll(['order' => 'nome, sobrenome']);
        $tutores = Tutor::model()->findAll(['order' => 'nome, sobrenome']);
        $colaboradores = Colaborador::model()->findAll(['order' => 'nome, sobrenome']);
        return [
            'Docentes' => $this->formatarEmLista('docente', $docentes),
            'Tutores' => $this->formatarEmLista('tutor', $tutores),
            'Colaboradores' => $this->formatarEmLista('colaborador', $colaboradores),
        ];
    }

    private function formatarEmLista($tipo, $array)
    {
        $lista = [];
        foreach ($array as $item) {
            $chave = $tipo . '_' . $item['cpf'];
            $lista[$chave] = $item->nomeCompleto;
        }
        return $lista;
    }

    public function actionEditar($id)
    {
        $model = EncontroPresencial::model()->findByPk($id);

        if (isset($_POST['EncontroPresencial'])) {
            $model->attributes = $_POST['EncontroPresencial'];

            $transaction = Yii::app()->db->beginTransaction();
            if ($model->validate() && $model->save()) {

                EncontroPresencialResponsavel::model()->deleteAll('encontro_presencial_id = ' . $model->id);
                if (!empty($_POST['EncontroPresencial']['responsaveis'])) {
                    foreach ($_POST['EncontroPresencial']['responsaveis'] as $responsavel) {
                        $encontroResponsavel = new EncontroPresencialResponsavel;
                        $encontroResponsavel->encontro_presencial_id = $model->id;
                        [$encontroResponsavel->tipoColaborador, $encontroResponsavel->colaborador] = explode('_', $responsavel);
                        $encontroResponsavel->save();
                    }
                }

                $alunosNaLista = EncontroPresencialInscricao::model()->findAllByAttributes(['encontro_presencial_id' => $model->id]);
                $idsAlunosNaLista = array_map(function($aluno) { return $aluno->inscricao_id; }, $alunosNaLista);
                $idsARemover = array_diff($idsAlunosNaLista, $_POST['EncontroPresencial']['alunos']);
                $idsARemoverString = implode(',', $idsARemover);
                $idsNovos = array_diff($_POST['EncontroPresencial']['alunos'], $idsAlunosNaLista);
                if (!empty($idsARemoverString)) {
                    $sql = "DELETE FROM encontro_presencial_inscricao WHERE encontro_presencial_id = {$model->id} AND inscricao_id IN ({$idsARemoverString})";
                    Yii::app()->db->createCommand($sql)->execute();
                }

                if (!empty($_POST['EncontroPresencial']['alunos'])) {
                    foreach ($_POST['EncontroPresencial']['alunos'] as $inscricaoId) {
                        if (!in_array($inscricaoId, $idsNovos)) continue;
                        $encontroAluno = new EncontroPresencialInscricao;
                        $encontroAluno->encontro_presencial_id = $model->id;
                        $encontroAluno->inscricao_id = $inscricaoId;
                        $encontroAluno->save();
                    }
                }

                $transaction->commit();

                Yii::app()->user->setFlash('notificacao', "Encontro presencial atualizado com sucesso!");
                Yii::log("{$model} foi atualizado.", 'info', 'system.controllers.EncontroController');
                $this->redirect(['encontro/editar', 'id' => $model->id]);
            } else {
                $transaction->rollback();

                $erros = $model->errors;
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema ao atualizar encontro presencial');
                Yii::log("Ocorreram problemas ao atualizar o encontro presencial. Erros: " . print_r($erros, true), 'error', 'system.controllers.EncontroController');
            }
        }

        $this->adicionarArquivosJavascript('/js/pikaday1.8.2.min');
        $inscricoes = Inscricao::model()->findAllByAttributes([
            'status' => 3,
        ], [
            'order' => 'nome, sobrenome',
        ]);

        $this->render('editar', [
            'model' => $model,
            'colaboradores' => $this->recuperarColaboradores(),
            'inscricoes' => $inscricoes,
        ]);
    }

    public function actionMarcarPresenca($id)
    {
        $model = EncontroPresencial::model()->findByPk($id);

        if (isset($_POST['Presenca'])) {

            $transaction = Yii::app()->db->beginTransaction();
            try {

                $sql = "UPDATE encontro_presencial_inscricao SET presente = FALSE WHERE encontro_presencial_id = {$model->id}";
                Yii::app()->db->createCommand($sql)->execute();

                $inscricaoIds = implode(',', array_keys($_POST['Presenca']));
                if ($inscricaoIds) {
                    $sql = "UPDATE encontro_presencial_inscricao SET presente = TRUE WHERE encontro_presencial_id = {$model->id} AND inscricao_id IN ({$inscricaoIds})";
                    Yii::app()->db->createCommand($sql)->execute();
                }
                $transaction->commit();

                Yii::app()->user->setFlash('notificacao', "Presenças marcadas com sucesso!");
                Yii::log("Presença dos alunos para o encontro presencial {$model} foi atualizada.", 'info', 'system.controllers.EncontroController');
                $this->redirect(['encontro/editar', 'id' => $model->id]);
            } catch (Exception $e) {
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema ao marcar presenças');
                Yii::log("Ocorreram problemas ao marcar presenças para o encontro presencial {$model}. Erros: " . $e->getMessage() . ' ' . $e->getTraceAsString(), 'error', 'system.controllers.EncontroController');
                $transaction->rollback();
            }
        }

        $idsAlunosPresentes = array_map(
            function($aluno) { return $aluno->inscricao_id; },
            EncontroPresencialInscricao::model()->findAllByAttributes([
                'encontro_presencial_id' => $model->id,
                'presente' => true,
            ])
        );

        $this->render('marcarPresenca', [
            'model' => $model,
            'idsAlunosPresentes' => $idsAlunosPresentes,
        ]);
    }

    public function actionDeletar($id)
    {
        $encontro = EncontroPresencial::model()->findByPk($id);
        if ($encontro === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $encontro->desativar();
    }

}
