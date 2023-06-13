<?php

class FinanceiroController extends Controller
{
    const CLASS_NAME = 'system.controllers.FinanceiroController';

    public function actionPagamentosDeAlunos()
    {
        $inscricao = new Inscricao('search');
        $inscricao->unsetAttributes();
        $inscricao->status = Inscricao::STATUS_MATRICULADO;
        if (isset($_GET['Inscricao'])) {
            $inscricao->attributes = $_GET['Inscricao'];
        }

        $this->render('pagamentosDeAlunos', [
            'model' => $inscricao,
        ]);
    }

    public function actionPagamentosDeAluno($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);
        $novoPagamento = new PagamentoAluno();
        $novoPagamento->inscricao_id = $id;

        if (isset($_POST['Inscricao']) && !empty($_POST['Inscricao']['tipo_bolsa'])) {

            $inscricao->tipo_bolsa = $_POST['Inscricao']['tipo_bolsa'];
            if ($inscricao->validate()) {
                // https://stackoverflow.com/a/15087758/5913350
                Inscricao::model()->updateByPk($id, [
                    'tipo_bolsa' => $_POST['Inscricao']['tipo_bolsa'],
                ]);
                Yii::log("Tipo de bolsa do aluno {$inscricao->nomeCompleto} salvo com sucesso.", 'info', self::CLASS_NAME);
                Yii::app()->user->setFlash('notificacao', "Tipo de bolsa do aluno {$inscricao->nomeCompleto} salvo com sucesso!");
                $this->redirect(['pagamentosDeAluno', 'id' => $id]);
            }
        } else if (isset($_POST['salvar'])) {
            $novoPagamento->attributes = $_POST['PagamentoAluno'];

            if ($novoPagamento->validate() && $novoPagamento->save()) {
                Yii::log("Novo pagamento do aluno {$inscricao->nomeCompleto} salvo com sucesso.", 'info', self::CLASS_NAME);
                Yii::app()->user->setFlash('notificacao', "Novo pagamento do aluno {$inscricao->nomeCompleto} salvo com sucesso!");
                $this->redirect(['pagamentosDeAluno', 'id' => $id]);
            }
        }

        $pagamentos = PagamentoAluno::model()->searchAluno($inscricao->id);

        $listaTiposPagamentos = [
            'matricula' => 'Matrícula',
            'curso_inteiro' => 'Curso inteiro',
        ];
        if ($inscricao->ehAlunoDeEspecializacao()) {
            foreach ($inscricao->habilitacoes as $i => $habilitacao) {
                for ($j = 1; $j <= 18; $j++) {
                    $indiceHabilitacao = $i + 1;
                    $listaTiposPagamentos["habilitacao{$indiceHabilitacao}_parcela{$j}"] = "Hab. {$indiceHabilitacao} - parcela {$j}";
                }
            }
        }
        $listaTiposPagamentos['parcela_adicional'] = 'Parcela adicional';

        $this->adicionarArquivosJavascript('/js/pikaday1.8.2.min');

        $this->render('pagamentosDeAluno', [
            'inscricao' => $inscricao,
            'pagamentos' => $pagamentos,
            'pagamento' => $novoPagamento,
            'tiposBolsa' => [
                'sem_bolsa' => 'Sem bolsa',
                'bolsa_integral' => 'Bolsa integral: desconto de 100%',
                'bolsa_parcial_a' => 'Bolsa parcial A: desconto de 40% nas parcelas',
                'bolsa_parcial_b' => 'Bolsa parcial B: eliminação das X parcelas ao final',
                'desconto_pagamento_antecipado' => 'Desconto por pagamento antecipado (a vista): 30% desconto',
            ],
            'listaTiposPagamentos' => $listaTiposPagamentos,
        ]);
    }

    public function actionDeletarPagamentoAluno($id)
    {
        $pagamentoAluno = PagamentoAluno::model()->findByPk($id);
        if ($pagamentoAluno === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $pagamentoAluno->delete();
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionPagamentosDeColaboradores($mes, $ano)
    {
        $novoPagamento = new PagamentoColaborador();

        if (isset($_POST['PagamentoColaborador'])) {

            $estaAtualizando = false;
            if (!empty($_POST['PagamentoColaborador']['id'])) {
                $novoPagamento = PagamentoColaborador::model()->findByPk($_POST['PagamentoColaborador']['id']);
                $estaAtualizando = true;
            }

            $novoPagamento->attributes = $_POST['PagamentoColaborador'];
            [$novoPagamento->tipoColaborador, $novoPagamento->colaborador] = explode('_', $_POST['PagamentoColaborador']['colaborador']);

            $transaction = Yii::app()->db->beginTransaction();
            if ($novoPagamento->validate() && $novoPagamento->save()) {
                Servico::model()->deleteAll('pagamento_colaborador_id = ' . $novoPagamento->id);
                foreach ($_POST['PagamentoColaborador']['servicos'] as $servico) {
                    $novoServico = new Servico();
                    $novoServico->attributes = $servico;
                    $novoServico->pagamento_colaborador_id = $novoPagamento->id;
                    $novoServico->save();
                }
                $transaction->commit();

                $colaborador = $novoPagamento->getColaborador()->nomeCompleto;
                if ($estaAtualizando) {
                    Yii::log("Pagamento para o colaborador {$colaborador} atualizado com sucesso.", 'info', self::CLASS_NAME);
                    Yii::app()->user->setFlash('notificacao', "Pagamento para o colaborador {$colaborador} atualizado com sucesso!");
                } else {
                    Yii::log("Pagamento para o colaborador {$colaborador} salvo com sucesso.", 'info', self::CLASS_NAME);
                    Yii::app()->user->setFlash('notificacao', "Novo pagamento para o colaborador {$colaborador} salvo com sucesso!");
                }
                $this->redirect(['pagamentosDeColaboradores', 'mes' => $mes, 'ano' => $ano]);
            }
            $transaction->rollback();
        }

        $pagamentos = PagamentoColaborador::model()->searchMesAno($mes, $ano);

        $this->adicionarArquivosJavascript('/js/pikaday1.8.2.min');

        $this->render('pagamentosDeColaboradores', [
            'pagamentos' => $pagamentos,
            'pagamento' => $novoPagamento,
            'colaboradores' => $this->recuperarColaboradores(),
            'mes' => $mes,
            'ano' => $ano,
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

    public function actionDeletarPagamentoColaborador($id)
    {
        $pagamentoColaborador = PagamentoColaborador::model()->findByPk($id);
        if ($pagamentoColaborador === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        foreach ($pagamentoColaborador->servicos as $servico) {
            $servico->delete();
        }
        $pagamentoColaborador->delete();
    }

    public function actionViagens()
    {
        $viagem = new Viagem();

        if (isset($_POST['Viagem'])) {

            $estaAtualizando = false;
            if (!empty($_POST['Viagem']['id'])) {
                $viagem = Viagem::model()->findByPk($_POST['Viagem']['id']);
                $estaAtualizando = true;
            }

            $viagem->attributes = $_POST['Viagem'];
            [$viagem->tipoColaborador, $viagem->colaborador] = explode('_', $_POST['Viagem']['colaborador']);

            $transaction = Yii::app()->db->beginTransaction();
            if ($viagem->validate() && $viagem->save()) {
                DespesaViagem::model()->deleteAll('viagem_id = ' . $viagem->id);
                foreach ($_POST['Viagem']['despesas'] as $despesa) {
                    $despesaViagem = new DespesaViagem();
                    $despesaViagem->attributes = $despesa;
                    $despesaViagem->viagem_id = $viagem->id;
                    $despesaViagem->save();
                }
                $transaction->commit();

                $colaborador = $viagem->getColaborador()->nomeCompleto;
                if ($estaAtualizando) {
                    Yii::log("Viagem do colaborador {$colaborador} atualizada com sucesso.", 'info', self::CLASS_NAME);
                    Yii::app()->user->setFlash('notificacao', "Viagem do colaborador {$colaborador} atualizada com sucesso!");
                } else {
                    Yii::log("Viagem do colaborador {$colaborador} salva com sucesso.", 'info', self::CLASS_NAME);
                    Yii::app()->user->setFlash('notificacao', "Nova Viagem do colaborador {$colaborador} salva com sucesso!");
                }
                $this->redirect(['viagens']);
            }
            $transaction->rollback();
        }

        $viagens = Viagem::model()->search();

        $this->adicionarArquivosJavascript('/js/pikaday1.8.2.min');

        $this->render('viagens', [
            'viagens' => $viagens,
            'viagem' => $viagem,
            'colaboradores' => $this->recuperarColaboradores(),
        ]);
    }

    public function actionDeletarViagem($id)
    {
        $viagem = Viagem::model()->findByPk($id);
        if ($viagem === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $viagem->delete();
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

    public function actionCompras()
    {
        $compra = new Compra();

        if (isset($_POST['Compra'])) {

            $estaAtualizando = false;
            if (!empty($_POST['Compra']['id'])) {
                $compra = Compra::model()->findByPk($_POST['Compra']['id']);
                $estaAtualizando = true;
            }

            $compra->attributes = $_POST['Compra'];
            [$compra->tipoColaborador, $compra->colaborador] = explode('_', $_POST['Compra']['colaborador']);

            if ($compra->validate() && $compra->save()) {
                $colaborador = $compra->getColaborador()->nomeCompleto;
                if ($estaAtualizando) {
                    Yii::log("Compra do colaborador {$colaborador} atualizada com sucesso.", 'info', self::CLASS_NAME);
                    Yii::app()->user->setFlash('notificacao', "Compra do colaborador {$colaborador} atualizada com sucesso!");
                } else {
                    Yii::log("Compra do colaborador {$colaborador} salva com sucesso.", 'info', self::CLASS_NAME);
                    Yii::app()->user->setFlash('notificacao', "Nova compra do colaborador {$colaborador} salva com sucesso!");
                }
                $this->redirect(['compras']);
            }
        }

        $compras = Compra::model()->search();

        $this->adicionarArquivosJavascript('/js/pikaday1.8.2.min');

        $this->render('compras', [
            'compras' => $compras,
            'compra' => $compra,
            'colaboradores' => $this->recuperarColaboradores(),
        ]);
    }

    public function actionDeletarCompra($id)
    {
        $compra = Compra::model()->findByPk($id);
        if ($compra === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $compra->delete();
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (!isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
    }

}
