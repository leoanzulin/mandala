<?php

class PagamentoController extends Controller
{

    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionBolsas()
    {
        if (isset($_POST['Salvar'])) {
            $this->salvarNovasBolsas();
            $this->deletarBolsasRemovidas();

            Yii::app()->user->setFlash('notificacao', 'Bolsas salvas com sucesso!');
            Yii::log("Informaçẽos de bolsa para docentes e tutores salvas.", 'info', 'system.controllers.PagamentoController');
            // Redirecionar para a própria página resolve o problema de reenviar os
            // dados POST quando recarregar a página
            $this->redirect(array('bolsas'));
        }

        $this->adicionarArquivosJavascript('/js/modulos/bolsasApp');

        $this->render('bolsas', array(
            'docentes' => Docente::model()->findAll(array('order' => 'nome')),
            'tutores' => Tutor::model()->findAll(array('order' => 'nome')),
        ));
    }

    private function salvarNovasBolsas()
    {
        if (isset($_POST['Bolsa'])) {

            $bolsas = array_map(function($bolsaEncodada) {
                $bolsaJson = urldecode($bolsaEncodada);
                return Bolsa::fromJson($bolsaJson);
            }, $_POST['Bolsa']);

            foreach ($bolsas as $bolsa) {
                if (!isset($bolsa->id)) {
                    $bolsa->save();
                }
            }
        }
    }

    private function deletarBolsasRemovidas()
    {
        if (isset($_POST['BolsasADeletar'])) {
            // https://stackoverflow.com/questions/16798161/yii-deleteall-and-in-statement
            $criteria = new CDbCriteria();
            $criteria->addInCondition('id', $_POST['BolsasADeletar']);
            Bolsa::model()->deleteAll($criteria);
        }
    }

    public function actionAlunos()
    {
        if (isset($_POST['Salvar'])) {

            // TODO: Refatorar esta parte e mover para componente
            $this->processarPagamentos();

            // TODO: Verificar se houve algum erro
            Yii::app()->user->setFlash('notificacao', 'Pagamentos salvos com sucesso!');
            Yii::log("Pagamentos feitos por alunos salvos.", 'info', 'system.controllers.PagamentoController');
            $this->redirect(array('alunos'));
        }

        $this->adicionarArquivosJavascript('/js/modulos/pagamentosApp');

        $this->render('alunos');
    }

    private function processarPagamentos()
    {
        // Recupera os itens que já estão no banco
        $sql = 'SELECT id, inscricao_id, tipo, valor, data_pagamento FROM pagamento WHERE valor <> 0 ORDER BY inscricao_id, tipo';
        $pagamentosDoBd = Yii::app()->db->createCommand($sql)->queryAll();

        $pagamentosASeremSalvos = $this->processarPostDePagamentos();

        $funcaoQueverificaSeItensDePagamentoSaoIguais = function($a, $b) {
            if ($a['inscricao_id'] != $b['inscricao_id']) {
                return $b['inscricao_id'] - $a['inscricao_id'];
            }
            if ($a['tipo'] != $b['tipo']) {
                return $b['tipo'] - $a['tipo'];
            }
            return 0;
        };

        // DELETE - Itens que serão deletados
        $pagamentosQueEstaoNoBdMasNaoNosNovos = array_udiff($pagamentosDoBd, $pagamentosASeremSalvos, $funcaoQueverificaSeItensDePagamentoSaoIguais);
        // INSERT - Itens que serão inseridos
        $pagamentosQueEstaoNosNovosMasNaoNoBd = array_udiff($pagamentosASeremSalvos, $pagamentosDoBd, $funcaoQueverificaSeItensDePagamentoSaoIguais);
        // UPDATE - Itens que serão atualizados
        $pagamentosQueEstaoEmAmbos = array_uintersect($pagamentosDoBd, $pagamentosASeremSalvos, $funcaoQueverificaSeItensDePagamentoSaoIguais);
//        var_dump('PAGAMENTOS DO BD', $pagamentosDoBd, 'PAGAMENTOS NOVOS', $pagamentosASeremSalvos, 'PAGAMENTOS A SEREM DELETADOS', $pagamentosQueEstaoNoBdMasNaoNosNovos, 'PAGAMENTOS A SEREM INSERIDOS', $pagamentosQueEstaoNosNovosMasNaoNoBd, 'PAGAMENTOS A SEREM ATUALIZADOS', $pagamentosQueEstaoEmAmbos, '======');

        $sqls = array();
        $sqls = array_merge($sqls, $this->criarQueryDeletarItens($pagamentosQueEstaoNoBdMasNaoNosNovos));
        $sqls = array_merge($sqls, $this->criarQueriesInserirItens($pagamentosQueEstaoNosNovosMasNaoNoBd));
        $sqls = array_merge($sqls, $this->criarQueriesAtualizarItens($pagamentosQueEstaoEmAmbos));

        foreach ($sqls as $sql) {
            Yii::app()->db->createCommand($sql)->execute();
        }
    }

    private function processarPostDePagamentos()
    {
        $pagamentosASeremSalvos = array();

        foreach ($_POST['item'] as $inscricaoId => $tiposDePagamento) {
            foreach ($tiposDePagamento as $tipoDePagamento => $itemPagamento) {
                if ($tipoDePagamento == 'parcelas') {
                    foreach ($itemPagamento as $numero => $parcela) {
                        if (!$this->itemDePagamentoEhValido($parcela)) {
                            continue;
                        }
                        $pagamentosASeremSalvos[] = $this->gerarArrayItemDePagamento($inscricaoId, "parcela{$numero}", $parcela);
                    }
                } else {
                    if (!$this->itemDePagamentoEhValido($itemPagamento)) {
                        continue;
                    }
                    $pagamentosASeremSalvos[] = $this->gerarArrayItemDePagamento($inscricaoId, $tipoDePagamento, $itemPagamento);
                }
            }
        }

        return $pagamentosASeremSalvos;
    }

    private function itemDePagamentoEhValido($itemDePagamento)
    {
        if (empty($itemDePagamento['valor']) ||
                empty($itemDePagamento['data_pagamento']) ||
                $itemDePagamento['valor'] == 0 ||
                !$this->dataEhValida($itemDePagamento['data_pagamento'])) {
            return false;
        }
        return true;
    }

    private function dataEhValida($data)
    {
        $matches = array();
        if (preg_match('/^(\d\d)\/(\d\d)\/(\d\d\d\d)$/', $data, $matches) !== 1) {
            return false;
        }
        return checkdate($matches[2], $matches[1], $matches[3]);
    }

    private function gerarArrayItemDePagamento($inscricaoId, $tipo, $itemDePagamento)
    {
        return array(
            'inscricao_id' => $inscricaoId,
            'tipo' => Pagamento::recuperarTipoDeItemDePagamento($tipo),
            'valor' => str_replace(',', '.', $itemDePagamento['valor']),
            'data_pagamento' => $this->transformarDataDeBarrasParaHifens($itemDePagamento['data_pagamento']),
        );
    }

    private function transformarDataDeBarrasParaHifens($data)
    {
        if (strpos($data, '/') === false) {
            return $data;
        }
        $partes = explode('/', $data);
        return $partes[2] . '-' . $partes[1] . '-' . $partes[0];
    }

    private function criarQueryDeletarItens($itensDePagamento)
    {
        $sqlDelete = 'DELETE FROM pagamento WHERE id IN (';

        $idsParaDeletar = array_map(function($item) {
            Yii::log("Item de pagamento (inscrição de ID {$item['inscricao_id']}, tipo {$item['tipo']}, valor {$item['valor']}, data {$item['data_pagamento']}) sendo deletado.", 'info', 'system.controllers.PagamentoController');
            return $item['id'];
        }, $itensDePagamento);
        $sqlDelete .= implode(', ', $idsParaDeletar);

        $sqlDelete .= ');';

        return count($itensDePagamento) > 0 ? array($sqlDelete) : array();
    }

    private function criarQueriesInserirItens($itensDePagamento)
    {
        $sqls = array();
        foreach ($itensDePagamento as $item) {
            Yii::log("Novo pagamento realizado (inscrição de ID {$item['inscricao_id']}, tipo {$item['tipo']}, valor {$item['valor']}, data {$item['data_pagamento']}).", 'info', 'system.controllers.PagamentoController');
            $sqlInsert = "INSERT INTO pagamento (inscricao_id, tipo, valor, data_pagamento) VALUES (" .
                    "{$item['inscricao_id']}, {$item['tipo']}, {$item['valor']}, '{$item['data_pagamento']}');";
            $sqls[] = $sqlInsert;
        }
        return $sqls;
    }

    private function criarQueriesAtualizarItens($itensDePagamento)
    {
        $sqls = array();
        foreach ($itensDePagamento as $item) {
            Yii::log("Pagamento atualizado (inscrição de ID {$item['inscricao_id']}, tipo {$item['tipo']}, valor {$item['valor']}, data {$item['data_pagamento']}).", 'info', 'system.controllers.PagamentoController');
            $sqlUpdate = "UPDATE pagamento SET valor = {$item['valor']}, data_pagamento = '{$item['data_pagamento']}' WHERE id = {$item['id']}";
            $sqls[] = $sqlUpdate;
        }
        return $sqls;
    }

    public function actionSaldoBolsas()
    {
        $this->adicionarArquivosJavascript('/js/modulos/saldoBolsasApp');

        $this->render('saldoBolsas');
    }

    public function actionAlunosExtensaoAperfeicoamento()
    {
        if (isset($_POST['adicionarExtensao'])) {

            $idInscricao = array_keys($_POST['adicionarExtensao']);
            $idInscricao = $idInscricao[0];
            $valor = $_POST['PagamentoExtensao'][$idInscricao]['valor'];
            $data = $_POST['PagamentoExtensao'][$idInscricao]['data'];

            $pagamento = new Pagamento();
            $pagamento->inscricao_id = $idInscricao;
            $pagamento->tipo = Pagamento::TIPO_PAGAMENTO_CURSO_EXTENSAO;
            $pagamento->valor = $valor;
            $pagamento->data_pagamento = $data;
            
            if ($pagamento->save()) {
                Yii::app()->user->setFlash('notificacao', "{$pagamento} salvo com sucesso!");
                Yii::log("{$pagamento} salvo com sucesso.", 'info', 'system.controllers.PagamentoController');
                $this->redirect(array('alunosExtensaoAperfeicoamento'));
            }
            else {
                Yii::app()->user->setFlash('notificacao', "Problema no pagamento. Erros: {$pagamento->errors}");
            }

        }
        else if (isset($_POST['adicionarAperfeicoamento'])) {

            $idInscricao = array_keys($_POST['adicionarAperfeicoamento']);
            $idInscricao = $idInscricao[0];
            $valor = $_POST['PagamentoAperfeicoamento'][$idInscricao]['valor'];
            $data = $_POST['PagamentoAperfeicoamento'][$idInscricao]['data'];

            $pagamento = new Pagamento();
            $pagamento->inscricao_id = $idInscricao;
            $pagamento->tipo = Pagamento::TIPO_PAGAMENTO_CURSO_APERFEICOAMENTO;
            $pagamento->valor = $valor;
            $pagamento->data_pagamento = $data;

            if ($pagamento->save()) {
                Yii::app()->user->setFlash('notificacao', "{$pagamento} salvo com sucesso!");
                Yii::log("{$pagamento} salvo com sucesso.", 'info', 'system.controllers.PagamentoController');
                $this->redirect(array('alunosExtensaoAperfeicoamento'));
            }
            else {
                Yii::app()->user->setFlash('notificacao', "Problema no pagamento. Erros: {$pagamento->errors}");
            }
            
        }
        else if (isset($_POST['excluir'])) {
            $idPagamento = array_keys($_POST['excluir']);
            $idPagamento = $idPagamento[0];
            
            $pagamento = Pagamento::model()->findByPk($idPagamento);
            $mensagem = "{$pagamento} deletado com sucesso";
            $pagamento->delete();
                    
            Yii::app()->user->setFlash('notificacao', $mensagem);
            Yii::log($mensagem, 'info', 'system.controllers.PagamentoController');
            $this->redirect(array('alunosExtensaoAperfeicoamento'));
        }

        $this->render('alunosExtensaoAperfeicoamento');
    }

}
