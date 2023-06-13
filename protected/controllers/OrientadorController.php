<?php

class OrientadorController extends Controller
{

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionPendentes()
    {
        $model = new Tcc('search');
        $model->unsetAttributes();
        if (isset($_GET['Tcc'])) {
            $model->attributes = $_GET['Tcc'];
        }

        $todosTccs = Tcc::model()->findAll(['order' => 'titulo']);

        $pendentesValidacao = array_filter($todosTccs, function($tcc) {
            return $tcc->recuperarStatus() == Tcc::FASE_PRE_ORIENTADOR_ATRIBUIDO
                && $this->souPreOrientadorDeste($tcc);
        });
        $pendentesBanca = array_filter($todosTccs, function($tcc) {
            return $tcc->recuperarStatus() == Tcc::FASE_BANCA_ATRIBUIDA
                && $this->souMembroDaBancaDeste($tcc);
        });
        $pendentesFinal = array_filter($todosTccs, function($tcc) {
            return $tcc->recuperarStatus() == Tcc::FASE_ORIENTADOR_FINAL_ATRIBUIDO
                && $this->souOrientadorOuCoorientadorDeste($tcc);
        });

        $this->render('pendentes', [
            'validacao' => $pendentesValidacao,
            'banca' => $pendentesBanca,
            'final' => $pendentesFinal,
        ]);
    }

    private function souPreOrientadorDeste($tcc)
    {
        return $tcc->validacao_orientador_cpf == Yii::app()->user->id;
    }

    private function souMembroDaBancaDeste($tcc)
    {
        return in_array(Yii::app()->user->id, [
            $tcc->banca_membro1_cpf,
            $tcc->banca_membro2_cpf,
            $tcc->banca_membro3_cpf,
        ]);
    }

    private function souOrientadorOuCoorientadorDeste($tcc)
    {
        return $tcc->final_orientador_cpf == Yii::app()->user->id
            || $tcc->final_coorientador_cpf == Yii::app()->user->id;
    }

    public function actionAvaliar($id)
    {
        $colaborador = $this->recuperarColaborador();
        $tcc = Tcc::model()->findByPk($id);

        if (isset($_POST['Tcc'])) {
            $tcc->attributes = $_POST['Tcc'];

            if (isset($_POST['aprovar-validacao'])) {
                $tcc->validacao_tem_pendencias = false;
            } else if (isset($_POST['aprovar-validacao-pendencias'])) {
                $tcc->validacao_tem_pendencias = true;
            } else if (isset($_POST['aprovar-banca'])) {
                $tcc->banca_tem_pendencias = false;
            } else if (isset($_POST['aprovar-banca-pendencias'])) {
                $tcc->banca_tem_pendencias = true;
            }

            if ($tcc->validate() && $tcc->save()) {

                if (isset($_POST['aprovar-validacao'])) {
                    Evento::orientadorAprovouPreTrabalho($tcc->id);
                } else if (isset($_POST['aprovar-validacao-pendencias'])) {
                    Evento::orientadorAprovouPreTrabalhoComPendencias($tcc->id);
                } else if (isset($_POST['salvar-banca'])) {
                    Evento::orientadorSalvouConsideracoesBanca($tcc->id);
                } else if (isset($_POST['aprovar-banca'])) {
                    Evento::orientadorAprovouTrabalhoBanca($tcc->id);
                } else if (isset($_POST['aprovar-banca-pendencias'])) {
                    Evento::orientadorAprovouTrabalhoBancaComPendencias($tcc->id);
                } else if (isset($_POST['aprovar-final'])) {
                    Evento::orientadorAprovouTrabalhoFinal($tcc->id);
                }

                Yii::app()->user->setFlash('notificacao', 'TCC avaliado com sucesso!');
                Yii::log("TCC {$tcc} (ID {$tcc->id}) avaliado.", 'info', 'system.controllers.OrientadorController');
                $this->redirect(Yii::app()->createUrl('orientador/pendentes'));
            }
        }

        $this->render('avaliar', [
            'tcc' => $tcc,
            'colaborador' => $colaborador,
        ]);
    }

    private function recuperarColaborador()
    {
        $cpf = Yii::app()->user->id;
        $docente = Docente::model()->findByPk($cpf);
        if ($docente) return $docente;
        $tutor = Tutor::model()->findByPk($cpf);
        if ($tutor) return $tutor;
        $colaborador = Colaborador::model()->findByPk($cpf);
        if ($colaborador) return $colaborador;
        throw new Exception('Usuário não encontrado');
    }

    public function actionAvaliados()
    {
        $model = new Tcc('search');
        $model->unsetAttributes();
        if (isset($_GET['Tcc'])) {
            $model->attributes = $_GET['Tcc'];
        }

        $todosTccs = Tcc::model()->recuperarTccsQueOriento(Yii::app()->user->id);

        $validacao = array_filter($todosTccs, function($tcc) {
            return $tcc->recuperarStatus() == Tcc::FASE_APROVADO_PELO_PRE_ORIENTADOR;
        });
        $banca = array_filter($todosTccs, function($tcc) {
            return $tcc->recuperarStatus() == Tcc::FASE_APROVADO_PELA_BANCA;
        });
        $final = array_filter($todosTccs, function($tcc) {
            return $tcc->recuperarStatus() == Tcc::FASE_TCC_APROVADO;
        });

        $this->render('avaliados', [
            'validacao' => $validacao,
            'banca' => $banca,
            'final' => $final,
            'model' => $model,
        ]);
    }

    public function actionGerarAtestadoOrientador($tccId)
    {
        GeradorDeAtestado::deorientacao(Yii::app()->user->id, $tccId);
    }

    public function actionGerarAtestadoBanca($tccId)
    {
        GeradorDeAtestado::deMembroDeBanca(Yii::app()->user->id, $tccId);
    }

}
