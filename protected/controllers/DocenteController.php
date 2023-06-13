<?php

/**
 * Controlador que implementa uma interface REST para o modelo Docente.
 * 
 * Métodos:
 * 
 * docente/todos
 * - Retorna um vetor contendo todos os docentes do sistema
 * 
 */
class DocenteController extends Controller
{

    public function actionTodos()
    {
        $docentes = Docente::model()->findAllByAttributes([ 'ativo' => true ]);
        $docentesArray = array_map(function($docente) {
            return $docente->asArray();
        }, $docentes);
        $this->respostaJSON(json_encode($docentesArray));
    }

    /**
     * Actions não-REST
     */
    public function actionGerenciar()
    {
        $model = new Docente('search');
        $model->unsetAttributes();
        if (isset($_GET['Docente'])) {
            $model->attributes = $_GET['Docente'];
        }

        $this->render('gerenciar', array(
            'model' => $model,
        ));
    }

    public function actionCadastrar()
    {
        $model = new Docente();

        if (isset($_POST['Docente'])) {
            $model->attributes = $_POST['Docente'];
            if ($model->save()) {
                Yii::app()->user->setFlash('notificacao', "Docente {$model->nome} {$model->sobrenome} cadastrado com sucesso!");
                Yii::log("{$model} foi cadastrado no sistema.", 'info', 'system.controllers.AdminController');
                $model = new Docente();
            }
        }

        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mascara_celular.js');

        $this->render('cadastrar', array(
            'model' => $model,
        ));
    }

    public function actionEditar($cpf)
    {
        $model = Docente::model()->findByPk($cpf);

        if (isset($_POST['desativar'])) {
            $model->desativar();
            Yii::app()->user->setFlash('notificacao', "Docente {$model->nomeCompleto} desativado com sucesso!");
            Yii::log("{$model} foi desativado.", 'info', 'system.controllers.AdminController');
            $this->redirect(array('gerenciar'));
        } else if (isset($_POST['Docente'])) {
            $model->attributes = $_POST['Docente'];
            if ($model->validate()) {
                if ($cpf != $model->cpf) {
                    $this->atualizarOfertasEmQueODocenteEstaAssociado($cpf, $model);
                    Yii::app()->user->setFlash('notificacao', "Docente {$model->nome} {$model->sobrenome} atualizado com sucesso!");
                    Yii::log("{$model} foi atualizado.", 'info', 'system.controllers.AdminController');
                    $this->redirect(array('gerenciar'));
                } else if ($model->save()) {
                    Yii::app()->user->setFlash('notificacao', "Docente {$model->nome} {$model->sobrenome} atualizado com sucesso!");
                    Yii::log("{$model} foi atualizado.", 'info', 'system.controllers.AdminController');
                    $this->redirect(array('gerenciar'));
                }
            }
        }

        $cs = Yii::app()->getClientScript();
        $cs->registerScriptFile(Yii::app()->baseUrl . '/js/mascara_celular.js');

        $this->render('editar', array(
            'model' => $model,
        ));
    }

    private function atualizarOfertasEmQueODocenteEstaAssociado($cpfAntigo, $docente)
    {
        $sql = "SELECT oferta_id FROM docente_oferta WHERE docente_cpf = '{$cpfAntigo}'";
        $idOfertas = array_map(function($idOferta) {
            return $idOferta['oferta_id'];
        }, Yii::app()->db->createCommand($sql)->queryAll());

        DocenteOferta::model()->deleteAllByAttributes(array(
            'docente_cpf' => $cpfAntigo
        ));
        $docente->save();

        foreach ($idOfertas as $idOferta) {
            $docenteOferta = new DocenteOferta();
            $docenteOferta->docente_cpf = $docente->cpf;
            $docenteOferta->oferta_id = $idOferta;
            $docenteOferta->save();
        }
    }	

    public function actionVisualizar($cpf)
    {
        $model = Docente::model()->findByPk($cpf);
        $tccsOrientados = Tcc::model()->recuperarTccsQueOriento($cpf);

        $this->render('visualizar', array(
            'model' => $model,
            'tccsOrientados' => $tccsOrientados,
        ));
    }

    // Mostra ofertas em que o docente ou tutor está alocado
    public function actionVerOfertas()
    {
        $model = $this->recuperarColaborador();
        $this->render('verOfertas', [
            'model' => $model,
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

    public function actionVerOferta($id)
    {
        $model = $this->recuperarColaborador();
        $oferta = Oferta::model()->findByPk($id);
        $this->render('verOferta', [
            'model' => $model,
            'oferta' => $oferta,
        ]);
    }

    public function actionGerarAtestadoDocencia($ofertaId)
    {
        GeradorDeAtestado::deDocencia(Yii::app()->user->id, $ofertaId);
    }

    public function actionGerarAtestadoTutoria($ofertaId)
    {
        GeradorDeAtestado::deTutoria(Yii::app()->user->id, $ofertaId);
    }

}
