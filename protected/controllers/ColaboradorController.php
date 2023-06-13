<?php

class ColaboradorController extends Controller
{

    public function actionGerenciar()
    {
        $colaborador = new Colaborador('search');
        $colaborador->unsetAttributes();
        if (isset($_GET['Colaborador'])) {
            $colaborador->attributes = $_GET['Colaborador'];
        }

        $this->render('gerenciar', array(
            'model' => $colaborador,
        ));
    }

    public function actionCadastrar()
    {
        $colaborador = new Colaborador();

        if (isset($_POST['Colaborador'])) {
            $colaborador->attributes = $_POST['Colaborador'];
            if ($colaborador->save()) {
                Yii::app()->user->setFlash('notificacao', "Colaborador {$colaborador->nomeCompleto} cadastrado com sucesso!");
                Yii::log("{$colaborador} foi cadastrado no sistema.", 'info', 'system.controllers.ColaboradorController');
                $colaborador = new Colaborador();
            }
        }

        $this->render('cadastrar', array(
            'colaborador' => $colaborador,
        ));
    }

    public function actionEditar($cpf)
    {
        $colaborador = Colaborador::model()->findByPk($cpf);

        if (isset($_POST['Colaborador'])) {
            $colaborador->attributes = $_POST['Colaborador'];
            if ($colaborador->save()) {
                Yii::app()->user->setFlash('notificacao', "Colaborador {$colaborador->nomeCompleto} atualizado com sucesso!");
                Yii::log("{$colaborador} foi atualizado.", 'info', 'system.controllers.ColaboradorController');
                $this->redirect(array('gerenciar'));
            }
        }

        $this->render('editar', array(
            'colaborador' => $colaborador,
        ));
    }

    // Página inicial dos colaboradores
    public function actionIndex()
    {
        $colaborador = $this->recuperarColaborador();
        $this->render('index', [
            'nome' => $colaborador->nomeCompleto,
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

    public function actionPerfil()
    {
        $colaborador = $this->recuperarColaborador();
        $this->render('perfil', array(
            'colaborador' => $colaborador,
        ));
    }

    public function actionEditarPerfil()
    {
        $colaborador = $this->recuperarColaborador();

        if (isset($_POST['Docente']) || isset($_POST['Tutor']) || isset($_POST['Colaborador'])) {
            $colaborador->attributes = $_POST['Docente'] ?? $_POST['Tutor'] ?? $_POST['Colaborador'];

            if ($colaborador->validate() && $colaborador->update()) {
                Yii::app()->user->setFlash('notificacao', 'Alterações salvas com sucesso!');
                Yii::log("{$colaborador} alterou suas informações de perfil.", 'info', 'system.controllers.ColaboradorController');
                $this->redirect(Yii::app()->createUrl('colaborador/perfil'));
            }
        }

        $this->render('editarPerfil', array(
            'model' => $colaborador,
        ));
    }

    public function actionTrocarSenha()
    {
        if (isset($_POST['trocar'])) {
            ResetarSenha::model()->fazerSolicitacao(Yii::app()->user->id);
            $this->redirect(Yii::app()->createUrl('colaborador/perfil'));
        }
        $this->render('trocarSenha');
    }

}
