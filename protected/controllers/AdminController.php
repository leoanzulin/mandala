<?php

class AdminController extends Controller
{

    public function actionGerenciarInscricoes()
    {
        $this->processarChecksPagouMatricula();

        $model = new Inscricao('search');
        $model->unsetAttributes();
        if (isset($_GET['Inscricao'])) {
            $model->attributes = $_GET['Inscricao'];
        }

        if (isset($_POST['exportar'])) {
            $model->attributes = $_POST['Inscricao'];
            $dataProvider = $model->search();
            // http://www.yiiframework.com/forum/index.php/topic/24113-cactivedataprovider-only-returns-first-few-records/
            $inscricoesFiltradas = Inscricao::model()->findAll($dataProvider->getCriteria());

            $cabecalho = array('cpf', 'status', 'nome', 'sobrenome', 'email', 'candidato à bolsa', 'recebe bolsa', 'observações');
            $dados = array();
            foreach ($inscricoesFiltradas as $inscricao) {
                $dados[] = array(
                    'cpf' => $inscricao->cpf,
                    'status' => $inscricao->status,
                    'nome' => $inscricao->nome,
                    'sobrenome' => $inscricao->sobrenome,
                    'email' => $inscricao->email,
                    'candidato à bolsa' => $inscricao->candidato_a_bolsa ? 'sim' : 'não',
                    'recebe bolsa' => $inscricao->recebe_bolsa ? 'sim' : 'não',
                    'observações' => $inscricao->observacoes,
                );
            }

            Exportador::exportar($cabecalho, $dados, 'lista de alunos', 'xls');
        }

        $this->render('gerenciarInscricoes', array(
            'model' => $model,
        ));
    }

    private function processarChecksPagouMatricula()
    {
        if (isset($_POST['pagou_matricula'])) {
            $idsMatriculasPagas = $_POST['pagou_matricula'];
            foreach ($idsMatriculasPagas as $id) {
                $inscricao = Inscricao::model()->findByPk($id);
                $inscricao->matricular();
            }
        }
    }

    /**
     * Visualiza uma inscrição de participante do curso.
     */
    public function actionView($id)
    {
        $model = Inscricao::model()->findByPk($id);
        $this->render('visualizarInscricao', array(
            'model' => $model,
        ));
    }

    public function actionVisualizarInscricoesEmOfertas()
    {
        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodos = $recuperadorDeOfertas->recuperar();

        $this->render('visualizarInscricoesEmOfertas', array(
            'ofertasPorPeriodos' => $ofertasPorPeriodos,
        ));
    }

    public function actionRelatorios()
    {
        $this->adicionarArquivosJavascript('/js/modulos/relatorioPersonalizadoApp');

        $this->render('relatorios');
    }

    public function actionRelatoriosNotasProEx()
    {
        $sql = "
SELECT
    o.id, o.ano, o.mes, c.nome
FROM oferta o
    JOIN componente_curricular c ON o.componente_curricular_id = c.id
WHERE
    o.ativo IS TRUE
    AND c.ativo IS TRUE
ORDER BY
    o.ano, o.mes, c.nome
;
        ";
        $ofertas = Yii::app()->db->createCommand($sql)->queryAll();
        $this->render('relatoriosNotasProEx', [
            'ofertas' => $ofertas,
        ]);
    }

    public function actionVisualizarAlunosInscritos()
    {
        $erros = false;

        if (isset($_POST['Salvar'])) {
            $erros = $this->processarCheckRecebeBolsa() || $erros;
            $erros = $this->processarCampoObservacoes() || $erros;
            if (!$erros) {
                Yii::app()->user->setFlash('notificacao', 'Alterações salvas com sucesso!');
                Yii::log("Administrador salvou alterações nas informações de bolsas de alunos inscritos.", 'info', 'system.controllers.AdminController');
            } else {
                Yii::app()->user->setFlash('notificacao-negativa', 'Problemas nas alterações');
                Yii::log("Ocorreram problemas na alteração de informações de bolsas de alunos inscritos.", 'info', 'system.controllers.AdminController');
            }
        }

        $model = new Inscricao('search');
        $model->unsetAttributes();
        $model->status = Inscricao::STATUS_DOCUMENTOS_SENDO_ANALISADOS;
        if (isset($_GET['Inscricao'])) {
            $model->attributes = $_GET['Inscricao'];
        }

        $this->render('visualizarInscritos', array(
            'model' => $model,
        ));
    }

    private function processarCheckRecebeBolsa()
    {
        $erros = false;
        if (isset($_POST['recebe_bolsa'])) {
            foreach ($_POST['recebe_bolsa'] as $cpf) {
                $inscricao = Inscricao::model()->findByCpf($cpf);
                if (!$inscricao->saveAttributes(array('recebe_bolsa' => true))) {
                    $erros = true;
                }
            }
        }
        return $erros;
    }

    private function processarCampoObservacoes()
    {
        $erros = false;
        if (isset($_POST['observacoes'])) {
            foreach ($_POST['observacoes'] as $cpf => $observacao) {
                if (isset($observacao)) {
                    $inscricao = Inscricao::model()->findByCpf($cpf);
                    if (!$inscricao->saveAttributes(array('observacoes' => $observacao))) {
                        $erros = true;
                    }
                }
            }
        }
        return $erros;
    }

    public function actionVisualizarAlunosMatriculados()
    {
        $erros = false;

        if (isset($_POST['status_aluno'])) {
            $erros = $this->processarComboStatusAluno();
            if (!$erros) {
                Yii::app()->user->setFlash('notificacao', 'Alterações salvas com sucesso!');
                Yii::log("Administrador salvou alterações nas informações de status de alunos.", 'info', 'system.controllers.AdminController');
            } else {
                Yii::app()->user->setFlash('notificacao-negativa', 'Problemas nas alterações');
                Yii::log("Ocorreram problemas na alteração de informações de status de alunos.", 'info', 'system.controllers.AdminController');
            }
        }

        $model = new Inscricao('search');
        $model->unsetAttributes();
        $model->status = Inscricao::STATUS_MATRICULADO;
        if (isset($_GET['Inscricao'])) {
            $model->attributes = $_GET['Inscricao'];
        }

        $this->render('visualizarMatriculados', array(
            'model' => $model,
        ));
    }

    private function processarComboStatusAluno()
    {
        $erros = false;
        foreach ($_POST['status_aluno'] as $id => $status) {
            if (isset($status)) {
                $inscricao = Inscricao::model()->findByPk($id);
                if (!$inscricao->alterarStatusAluno($status)) {
                    $erros = true;
                }
            }
        }
        return $erros;
    }

    public function actionMatricular($cpf)
    {
        $inscricao = Inscricao::model()->findByCpf($cpf);
        $inscricao->matricular();
        $this->redirect(array('visualizarAlunosInscritos'));
    }

    public function actionGerenciarNotas()
    {
        if (isset($_POST['sincronizar'])) {
            RecuperadorDeNotas::recuperarTodasAsNotas();
            Yii::app()->user->setFlash('notificacao', 'Notas sincronizadas com o Moodle');
            $this->redirect(array('gerenciarNotas'));
        }

        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodos = $recuperadorDeOfertas->recuperar();

        $this->render('gerenciarNotas', array(
            'ofertasPorPeriodos' => $ofertasPorPeriodos,
        ));
    }

    public function actionGerenciarNotasDaOferta($id)
    {
        $oferta = Oferta::model()->findByPk($id);

        $ofertaProjetoIntegrador = Oferta::model()->recuperarOfertaProjetoIntegrador();
        if ($id != $ofertaProjetoIntegrador->id) {
            RecuperadorDeNotas::recuperarNotasDaOfertaCujoCodigoNoMoodleEh($oferta->codigo_moodle);
        }

        if (isset($_POST['Notas'])) {

            $transaction = Yii::app()->db->beginTransaction();
            try {

                foreach ($_POST['Notas'] as $inscricaoId => $dados) {
                    $inscricaoOferta = InscricaoOferta::model()->findByPk(array(
                        'oferta_id' => $id,
                        'inscricao_id' => $inscricaoId,
                    ));
                    $inscricaoOferta->media = !empty($dados['media']) ? str_replace(',', '.', $dados['media']) : null;
                    $inscricaoOferta->frequencia = !empty($dados['frequencia']) ? str_replace(',', '.', $dados['frequencia']) : null;
                    $inscricaoOferta->status = !empty($dados['status']) ? $dados['status'] : null;
                    // TODO: Verificar erros
                    $inscricaoOferta->saveAttributes(['media', 'frequencia', 'status']);
                }
                $transaction->commit();
                Yii::app()->user->setFlash('notificacao', 'Notas salvas com sucesso!');
                Yii::log("Notas e status de inscrições da oferta {$inscricaoOferta->oferta_id} salvas com sucesso.", 'info', 'system.controllers.AdminController');

            } catch (Exception $e) {
                $transaction->rollback();
                Yii::app()->user->setFlash('notificacao-negativa', "Problema ao salvar notas");
                Yii::log("Problema ao salvar notas e status de inscrições da oferta {$inscricaoOferta->oferta_id}.", 'info', 'system.controllers.AdminController');
            }
        }

        $inscricoesOferta = InscricaoOferta::model()->findAllByAttributes(array(
            'oferta_id' => $id,
        ));
        $inscricoesOfertaApenasAlunosAtivos = $this->filtrarAlunosNaoAtivos($inscricoesOferta);
        InscricaoOferta::ordenarPorNome($inscricoesOfertaApenasAlunosAtivos);

        $this->render('gerenciarNotasDaOferta', array(
            'oferta' => $oferta,
            'inscricoesOferta' => $inscricoesOfertaApenasAlunosAtivos,
        ));
    }

    private function filtrarAlunosNaoAtivos($inscricoesOfertas)
    {
        return array_filter($inscricoesOfertas, function($inscricaoOferta) {
            return $inscricaoOferta->inscricao->status_aluno == 'Ativo';
        });
    }

    public function actionEditarInscricao($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);

        if (isset($_POST['Inscricao'])) {
            $transaction = Yii::app()->db->beginTransaction();
            try {
                if ($inscricao->ehAlunoDeEspecializacao()) {
                    $this->processarHabilitacoes($inscricao);
                }
                $statusAntigo = $inscricao->status;
                $inscricao->attributes = $_POST['Inscricao'];
                $inscricao = $this->processarDocumentos($inscricao);
                $inscricao->data_matricula = $inscricao->data_matricula ?: null;
                $inscricao->data_conclusao = $inscricao->data_conclusao ?: null;
                $inscricao->processo_proex = $inscricao->processo_proex ?: null;

                if ($inscricao->validate() && $inscricao->update()) {
                    $this->salvarFormacoes($inscricao);
                    $this->salvarHabilitacoes($inscricao);
                    $this->gravarDocumentosNoDisco($inscricao);

                    if ($statusAntigo != $inscricao->status && $inscricao->status == Inscricao::STATUS_DOCUMENTOS_VERIFICADOS) {
                        Email::mensagemLinkParaPagamento($inscricao->nomeCompleto, $inscricao->email);
                    }

                    // Salva informaçpões adicionais (data de conclusão e processo ProEx) para cada habilitação inscrita
                    if (isset($_POST['InscricaoHabilitacao'])) {
                        foreach ($_POST['InscricaoHabilitacao'] as $habilitacaoId => $informacoes) {
                            $inscricaoHabilitacao = InscricaoHabilitacao::model()->findByAttributes([
                                'inscricao_id' => $id,
                                'habilitacao_id' => $habilitacaoId,
                            ]);

                            if (!empty($inscricaoHabilitacao)) {
                                $inscricaoHabilitacao->data_conclusao = $informacoes['data_conclusao'] ?: null;
                                $inscricaoHabilitacao->processo_proex = $informacoes['processo_proex'] ?: null;
                                $inscricaoHabilitacao->save();
                            }
                        }
                    }

                    $transaction->commit();
                    Yii::log("Inscrição de {$inscricao->nome} (CPF {$inscricao->cpf}) atualizada com sucesso.", 'info', 'system.controllers.AdminController');
                    Yii::app()->user->setFlash('notificacao', "{$inscricao} atualizada com sucesso!");

                    // Chama afterFind novamente para converter datas do formato yyyy-mm-dd para dd/mm/yyyy
                    $inscricao = Inscricao::model()->findByPk($id);
                }
            } catch (Exception $e) {
                $transaction->rollback();
                Yii::app()->user->setFlash('notificacao-negativa', "Problema ao editar {$inscricao}");
                Yii::log("Problema ao editar a inscrição {$inscricao->id}", 'error', 'system.controllers.AdminController');
            }
        }

        $this->adicionarArquivosJavascript('/js/modulos/inscricaoApp');

        $this->render('editarInscricao', array(
            'model' => $inscricao,
            'modelFormacao' => new Formacao(),
            'habilitacoes' => Habilitacao::findAllValid(),
        ));
    }

    private function processarHabilitacoes(Inscricao $inscricao)
    {
        $habilitacoesSelecionadas = [];
        foreach ($_POST['habilitacoes'] as $letra => $ordem) {
            if (!empty($ordem)) $habilitacoesSelecionadas[$letra] = $ordem;
        }
        asort($habilitacoesSelecionadas);

        if (count($habilitacoesSelecionadas) == 0) {
            $inscricao->addError('habilitacao1', 'Selecione pelo menos uma habilitação');
            return;
        }

        foreach ($habilitacoesSelecionadas as $letra => $odem) {
            $inscricao->habilitacoesEscolhidas[] = Habilitacao::findByLetra($letra)->id;
        }
    }

    private function processarDocumentos($inscricao)
    {
        $documentos = array(
            'documento_cpf',
            'documento_rg',
            'documento_diploma',
            'documento_comprovante_residencia',
            'documento_curriculo',
            'documento_justificativa'
        );
        foreach ($documentos as $documento) {
            $arquivo = CUploadedFile::getInstance($inscricao, $documento);
            if (!empty($arquivo)) {
                $inscricao->$documento = $arquivo;
            }
        }
        return $inscricao;
    }

    private function salvarFormacoes(Inscricao $inscricao)
    {
        Formacao::model()->deleteAll('inscricao_id = ' . $inscricao->id);

        foreach ($inscricao->formacao as $formacao) {
            $f = new Formacao();
            $f->attributes = $formacao;
            $f->inscricao_id = $inscricao->id;
            $f->save();
        }
    }

    private function salvarHabilitacoes(Inscricao $inscricao)
    {
        $habilitacoesInscritas = $inscricao->recuperarHabilitacoes();
        $habilitacoesInscritasIds = array_map(function($h) { return $h->id; }, $habilitacoesInscritas);
        $habilitacoesDeselecionadas = array_diff($habilitacoesInscritasIds, $inscricao->habilitacoesEscolhidas);
        $novasHabilitacoes = array_diff($inscricao->habilitacoesEscolhidas, $habilitacoesInscritasIds);

        $inscricoesEmOfertasQuePerderamHabilitacao = [];
        foreach ($habilitacoesDeselecionadas as $habilitacaoId) {
            $inscricoesEmOfertasQuePerderamHabilitacao = array_merge(
                $inscricoesEmOfertasQuePerderamHabilitacao,
                $this->recuperarInscricoesEmOfertasQuePerderamHabiltiacao($inscricao->id, $habilitacaoId)
            );
            $this->deletarInscricaoHabilitacao($inscricao->id, $habilitacaoId);
        }
        $inscricoesEmOfertasQuePerderamHabilitacao = array_unique($inscricoesEmOfertasQuePerderamHabilitacao);

        foreach ($inscricao->habilitacoesEscolhidas as $i => $habilitacaoId) {
            if (in_array($habilitacaoId, $habilitacoesInscritasIds)) {
                // muda ordem porque é uma habilitação que já estava selecionada antes
                $ih = InscricaoHabilitacao::model()->findByAttributes([
                    'inscricao_id' => $inscricao->id,
                    'habilitacao_id' => $habilitacaoId,
                ]);
                $ih->ordem = $i + 1;
                $ih->save();
            } else {
                // é uma habilitação que não tinha sido selecionada antes, cria novo registro
                $ih = new InscricaoHabilitacao();
                $ih->inscricao_id = $inscricao->id;
                $ih->habilitacao_id = $habilitacaoId;
                $ih->ordem = $i + 1;
                $ih->save();
            }
        }

        // Restaura inscrições em ofertas associados à habilitações que foram removidas,
        // associando-as às novas habilitações escolhidas
        if (!empty($inscricoesEmOfertasQuePerderamHabilitacao)) {
            foreach ($novasHabilitacoes as $habilitacaoId) {
                $values = array_map(function($inscricaoOferta) use($habilitacaoId) {
                    [ $inscricaoId, $ofertaId ] = explode('_', $inscricaoOferta);
                    return "({$habilitacaoId}, {$inscricaoId}, {$ofertaId})";
                }, $inscricoesEmOfertasQuePerderamHabilitacao);

                $valuesString = implode(',', $values);
                $sql = "INSERT INTO habilitacao_inscricao_oferta(habilitacao_id, inscricao_id, oferta_id) VALUES {$valuesString};";
                Yii::app()->db->createCommand($sql)->execute();
                $sql = "INSERT INTO habilitacao_inscricao_oferta_certificados(habilitacao_id, inscricao_id, oferta_id) VALUES {$valuesString};";
                Yii::app()->db->createCommand($sql)->execute();
            }
        }

    }

    private function recuperarInscricoesEmOfertasQuePerderamHabiltiacao($inscricaoId, $habilitacaoId)
    {
        $sql = "SELECT * FROM habilitacao_inscricao_oferta WHERE inscricao_id = {$inscricaoId} AND habilitacao_id = {$habilitacaoId}";
        $inscricaoOfertas = Yii::app()->db->createCommand($sql)->queryAll();
        return array_map(function($inscricaoOferta) {
            return $inscricaoOferta['inscricao_id'] . '_' . $inscricaoOferta['oferta_id'];
        }, $inscricaoOfertas);
    }

    private function deletarInscricaoHabilitacao($inscricaoId, $habilitacaoId)
    {
        $sql = "DELETE FROM habilitacao_inscricao_oferta_certificados WHERE inscricao_id = {$inscricaoId} AND habilitacao_id = {$habilitacaoId}";
        Yii::app()->db->createCommand($sql)->execute();
        $sql = "DELETE FROM habilitacao_inscricao_oferta WHERE inscricao_id = {$inscricaoId} AND habilitacao_id = {$habilitacaoId}";
        Yii::app()->db->createCommand($sql)->execute();
        InscricaoHabilitacao::model()->deleteAllByAttributes([
            'inscricao_id' => $inscricaoId,
            'habilitacao_id' => $habilitacaoId,
        ]);
    }

    private function gravarDocumentosNoDisco($inscricao)
    {
        $documentos = array(
            'cpf',
            'rg',
            'diploma',
            'comprovante_residencia',
            'curriculo',
            'justificativa'
        );

        foreach ($documentos as $documento) {
            $attr = "documento_{$documento}";
            if (!empty($inscricao->{$attr}) && $inscricao->{$attr} instanceof CUploadedFile) {
                $extensao = $this->recuperarExtensao($inscricao->{$attr});
                $inscricao->{$attr}->saveAs(Yii::app()->basePath . "/../uploads/{$inscricao->cpf}_{$documento}.{$extensao}");
            }
        }
    }

    private function recuperarExtensao($nomeArquivo)
    {
        $partes = pathinfo($nomeArquivo);
        return $partes['extension'];
    }

    /**
     * Página que mostra as inscrições de um determinado aluno
     */
    public function actionVisualizarInscricoes($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);

        $this->adicionarArquivosJavascript('/js/modulos/recuperarInscricoesEmOfertas');

        $this->render('visualizarInscricoes', array(
            'inscricao' => $inscricao,
        ));
    }

    public function actionEditarInscricoesInscricao($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);
        $mensagensDeErro = '';

        if (isset($_POST['limpar_historico'])) {
            $this->limparHistoricoInscricao($id);
            Yii::app()->user->setFlash('notificacao', 'Histórico do aluno limpo com sucesso!');
        } else if (isset($_POST['gerar_pdf'])) {
            GeradorDeDeclaracaoDeInscricoes::gerar($inscricao, $_POST['ContagemDeComponentes']);
        } else if (isset($_POST['Salvar'])) {

            $salvadorDeInscricoesEmOfertas = new SalvadorDeInscricoesEmOfertas($inscricao, $_POST['Inscricao']);

            if ($salvadorDeInscricoesEmOfertas->salvar()) {
                Yii::app()->user->setFlash('notificacao', 'Inscrição em ofertas feitas com sucesso!');
                Yii::log("Administrador editou inscrições em ofertas de {$inscricao}.", 'info', 'system.controllers.AdminController');
            } else {
                $mensagensDeErro = $salvadorDeInscricoesEmOfertas->recuperarMensagensDeErro();
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema na inscrição em ofertas');
                Yii::log("Administrador teve problemas ao editar as inscrições em ofertas de {$inscricao}.", 'error', 'system.controllers.AdminController');
            }
        }

        $this->adicionarArquivosJavascript('/js/modulos/recuperarInscricoesEmOfertas');

        $this->render('editarInscricoesInscricao', array(
            'inscricao' => $inscricao,
            'mensagensDeErro' => $mensagensDeErro,
        ));
    }

    public function limparHistoricoInscricao($id)
    {
        $inscricoesOferta = InscricaoOferta::model()->findAllByAttributes([
            'inscricao_id' => $id,
        ]);

        $transaction = Yii::app()->db->beginTransaction();
        try {
            foreach ($inscricoesOferta as $inscricaoOferta) {
                if ($inscricaoOferta->estaNoPassado() && !$inscricaoOferta->ehAprovada()) {
                    $inscricaoOferta->trancar();
                    Yii::log("Inscrição {$inscricaoOferta->inscricao_id} teve sua inscrição na oferta {$inscricaoOferta->oferta_id} trancada pelo administrador", 'info', 'system.controllers.AdminController');
                }
            }
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::log("Problema ao limpar o histórico da inscrição {$inscricaoOferta->inscricao_id}", 'error', 'system.controllers.AdminController');
        }
        $transaction->commit();
    }

    public function actionVisualizarHistorico($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);
        RecuperadorDeNotas::recuperarNotasDoAlunoDeCpf($inscricao->cpf);

        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodo = $recuperadorDeOfertas
                ->daInscricao($id)
                ->semCamposRelacionados()
                ->manterApenasInscritas()
                ->manterApenasOfertasPassadasEPresentes()
                ->comTrancadas()
                ->recuperar();

        if (isset($_POST['gerar_pdf'])) {
            GeradorDeHistorico::gerar($inscricao);
        }
        else if (isset($_POST['gerar_pdf_limpo'])) {
            GeradorDeHistorico::gerar($inscricao, true);
        }

        $this->render('/aluno/historico', array(
            'inscricao' => $inscricao,
            'ofertasPorPeriodo' => $ofertasPorPeriodo,
        ));
    }

    public function actionGerenciarTccs()
    {
        $model = new Tcc('search');
        $model->unsetAttributes();
        if (isset($_GET['Tcc'])) {
            $model->attributes = $_GET['Tcc'];
        }
        $this->render('gerenciarTccs', array(
            'model' => $model,
        ));
    }

    public function actionEmitirCertificados()
    {
        $model = new Inscricao('search');
        $model->unsetAttributes();
        $model->status = Inscricao::STATUS_MATRICULADO;
        // $model->tipo_curso = Inscricao::TIPO_CURSO_ESPECIALIZACAO;
        if (isset($_GET['Inscricao'])) {
            $model->attributes = $_GET['Inscricao'];
        }

        $this->render('emitirCertificados', array(
            'model' => $model,
        ));
    }

    // $id = id da inscrição
    public function actionCadastrarTcc($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);

        $tcc = new Tcc();
        $tcc->inscricao_id = $id;

        if (isset($_POST['Tcc'])) {
            $tcc->attributes = $_POST['Tcc'];
            $tcc = $this->recuperarDocumentosUpados($tcc);
            if ($tcc->validate() && $tcc->save()) {
                $this->salvarDocumentosNoDisco($tcc);
                Yii::app()->user->setFlash('notificacao', 'TCC cadastrado com sucesso!');
                Yii::log("TCC {$tcc} (ID {$tcc->id}) cadastrado.", 'info', 'system.controllers.AdminController');
                $this->redirect(Yii::app()->createUrl('admin/gerenciarTccs'));
            }
        }

        $habilitacoes = $inscricao->recuperarHabilitacoes();
        $listaHabilitacoes = [];
        foreach ($habilitacoes as $habilitacao) {
            $listaHabilitacoes[ $habilitacao->id ] = $habilitacao->nome;
        }
        $todosDocentes = Docente::model()->findAll(array('order' => 'nome, sobrenome'));
        // TODO: Ver forma de já trazer os dados da forma que eu preciso
        $listaDocentes = [];
        foreach ($todosDocentes as $docente) {
            $listaDocentes[ $docente->cpf ] = $docente->nomeCompleto;
        }

        $this->render('cadastrarTcc', array(
            'tcc' => $tcc,
            'listaDocentes' => $listaDocentes,
            'listaHabilitacoes' => $listaHabilitacoes,
        ));
    }

    private function recuperarDocumentosUpados($tcc)
    {
        $arquivoDoc = CUploadedFile::getInstance($tcc, 'arquivo_doc');
        $arquivoPdf = CUploadedFile::getInstance($tcc, 'arquivo_pdf');
        if (!empty($arquivoDoc)) {
            $tcc->arquivo_doc = $arquivoDoc;
        }
        if (!empty($arquivoPdf)) {
            $tcc->arquivo_pdf = $arquivoPdf;
        }
        return $tcc;
    }

    private function salvarDocumentosNoDisco($tcc)
    {
        $base = Yii::app()->basePath . "/../uploads/tcc_{$tcc->id}";

        if (!empty($tcc->arquivo_doc) && $tcc->arquivo_doc instanceof CUploadedFile) {
            $tcc->arquivo_doc->saveAs("{$base}.doc");
        }
        if (!empty($tcc->arquivo_pdf) && $tcc->arquivo_pdf instanceof CUploadedFile) {
            $tcc->arquivo_pdf->saveAs("{$base}.pdf");
        }
    }

    // $id = id do TCC
    public function actionEditarTcc($id)
    {
        $tcc = Tcc::model()->findByPk($id);
        $inscricao = $tcc->inscricao;

        if (isset($_POST['salvar'])) {
            $tcc->atribuirPreOrientador($_POST['Tcc']['validacao_orientador_cpf'] ?? null);
            if (!empty($_POST['Tcc']['validacao_orientador_cpf'])) Utils::tornarOrientador($_POST['Tcc']['validacao_orientador_cpf']);
            $tcc->atribuirBanca(
                $_POST['Tcc']['banca_membro1_cpf'] ?? null,
                $_POST['Tcc']['banca_membro2_cpf'] ?? null,
                $_POST['Tcc']['banca_membro3_cpf'] ?? null
            );
            if (!empty($_POST['Tcc']['banca_membro1_cpf'])) Utils::tornarOrientador($_POST['Tcc']['banca_membro1_cpf']);
            if (!empty($_POST['Tcc']['banca_membro2_cpf'])) Utils::tornarOrientador($_POST['Tcc']['banca_membro2_cpf']);
            if (!empty($_POST['Tcc']['banca_membro3_cpf'])) Utils::tornarOrientador($_POST['Tcc']['banca_membro3_cpf']);
            $tcc->atribuirOrientadorFinal(
                $_POST['Tcc']['final_orientador_cpf'] ?? null,
                $_POST['Tcc']['final_coorientador_cpf'] ?? null
            );
            if (!empty($_POST['Tcc']['final_orientador_cpf'])) Utils::tornarOrientador($_POST['Tcc']['final_orientador_cpf']);
            if (!empty($_POST['Tcc']['final_coorientador_cpf'])) Utils::tornarOrientador($_POST['Tcc']['final_coorientador_cpf']);
            $tcc->attributes = $_POST['Tcc'];
            $tcc = $this->recuperarDocumentosUpados($tcc);

            if ($tcc->validate() && $tcc->save()) {
                if ($tcc->atribuiu_pre_orientador) {
                    Evento::coordenacaoAtribuiuPreOrientador($tcc->id);
                }
                if ($tcc->atribuiu_banca) {
                    Evento::coordenacaoAtribuiuBanca($tcc->id);
                }
                if ($tcc->atribuiu_orientador_final) {
                    Evento::coordenacaoAtribuiuOrientadoresFinais($tcc->id);
                }
                $this->salvarDocumentosNoDisco($tcc);
                Yii::app()->user->setFlash('notificacao', 'TCC atualizado com sucesso!');
                Yii::log("TCC {$tcc} (ID {$tcc->id}) atualizado.", 'info', 'system.controllers.AdminController');
                // Atualiza o formato das datas de yyyy-mm-dd para dd/mm/yyyy
                $tcc->refresh();
            }
        } else if (isset($_POST['exportar'])) {
            $tcc->attributes = $_POST['Tcc'];
            if ($tcc->validate() && $tcc->save()) {
                $this->exportarTccParaDocx($tcc);
            }
        } else if (isset($_POST['excluir'])) {
            $tcc->delete();
            Yii::app()->user->setFlash('notificacao', 'TCC excluído com sucesso!');
            Yii::log("TCC {$tcc} (ID {$tcc->id}) excluído.", 'info', 'system.controllers.AdminController');
            $this->redirect(Yii::app()->createUrl('admin/gerenciarTccs'));
        }

        $habilitacoes = $inscricao->recuperarHabilitacoes();
        $listaHabilitacoes = [];
        foreach ($habilitacoes as $habilitacao) {
            $listaHabilitacoes[ $habilitacao->id ] = $habilitacao->nome;
        }
        $todosDocentes = Docente::model()->findAll(array('order' => 'nome, sobrenome'));
        // TODO: Ver forma de já trazer os dados da forma que eu preciso
        $listaDocentes = [];
        foreach ($todosDocentes as $docente) {
            $listaDocentes[ $docente->cpf ] = $docente->nomeCompleto;
        }
        $todosTutores = Tutor::model()->findAll([ 'order' => 'nome, sobrenome' ]);
        $listaTutores = [];
        foreach ($todosTutores as $tutor) {
            $listaTutores[ $tutor->cpf ] = $tutor->nomeCompleto;
        }

        $this->render('editarTcc', array(
            'tcc' => $tcc,
            'listaDocentes' => $listaDocentes,
            'listaTutores' => $listaTutores,
            'listaHabilitacoes' => $listaHabilitacoes,
        ));
    }

    // https://stackoverflow.com/questions/4914750/how-to-zip-a-whole-folder-using-php
    // https://stackoverflow.com/questions/17708562/zip-all-files-in-directory-and-download-generated-zip
    private function exportarTccParaDocx($tcc)
    {
        // TODO: REFATORAR TUDO ISSO
        $urlModelo = 'protected/components/modelo_tcc';
        $rootPath = realpath("{$urlModelo}/modelo");
        $modeloOriginal = file("{$urlModelo}/document_original.xml");
        $modeloEditado = $this->fazerSubstituicoes($modeloOriginal, $tcc);
        file_put_contents("{$urlModelo}/modelo/word/document.xml", $modeloEditado);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $diretorio = 'tccs';
        $arquivoTcc = "tcc_{$tcc->inscricao->id}_{$tcc->id}.docx";
        if (!file_exists($diretorio)) mkdir($diretorio, 0700);

        $zip = new ZipArchive();
        $zip->open("{$diretorio}/{$arquivoTcc}", ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header("Content-Disposition: attachment; filename={$arquivoTcc}");
        header('Content-Length: ' . filesize("{$diretorio}/{$arquivoTcc}"));
        header("Location: {$diretorio}/{$arquivoTcc}");
    }

    private function fazerSubstituicoes($modelo, $tcc)
    {
        $modeloEditado = '';
        $sinteses = $tcc->sinteses_componentes;
        $propostas = $tcc->propostas_pedagogicas;
        $deveEscreverLinha = true;

        foreach ($modelo as $linha) {
            $linhaSemEspacos = trim($linha);

            if ($linhaSemEspacos == '<w:t>Coloque aqui a sua habilitação</w:t>') {
                $linha = "<w:t>{$tcc->habilitacao->nome}</w:t>";
            } else if ($linhaSemEspacos == '<w:t xml:space="preserve">Título do TCC – Título da Síntese Reflexiva </w:t>') {
                $linha = '<w:t xml:space="preserve">' . $tcc->titulo . '</w:t>';
            } else if ($linhaSemEspacos == '<w:t>Inserir o Título aqui do trabalho</w:t>' ) {
                $linha = "<w:t>{$tcc->titulo}</w:t>";
            } else if ($linhaSemEspacos == '<w:t>Inserir o nome do Estudante aqui</w:t>') {
                $linha = "<w:t>{$tcc->inscricao->nome}</w:t>";
            } else if (strpos($linhaSemEspacos, '<w:t>{CARACTERIZACAO_ESPECIALISTA_') !== FALSE) {
                // Não quero usar exprssões regulares para pegar um número no meio da string
                $numero = (int)$linhaSemEspacos[34];
                if ($numero == 1) {
                    $texto = $tcc->caracterizacao_especialista_perfil;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                } else if ($numero == 2) {
                    $texto = $tcc->caracterizacao_especialista_importancia;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                } else if ($numero == 3) {
                    $texto = $tcc->caracterizacao_especialista_saberes;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                } else if ($numero == 4) {
                    $texto = $tcc->caracterizacao_especialista_atividades;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                } else if ($numero == 5) {
                    $texto = $tcc->caracterizacao_especialista_desafios;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                }
                // http://officeopenxml.com/WPtextSpecialContent.php
                $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                $linha = "<w:t>{$texto}</w:t>";
            } else if (preg_match('/<!-- SINTESE_(\d+)_INICIO -->/', $linhaSemEspacos, $matches)) {
                $numero = (int)$matches[1];
                if ($numero > count($sinteses)) {
                    $deveEscreverLinha = false;
                }
            } else if (preg_match('/<!-- SINTESE_(\d+)_FIM -->/', $linhaSemEspacos, $matches)) {
                $numero = (int)$matches[1];
                $deveEscreverLinha = true;
            } else if (preg_match('/\{SINTESE_(\d+)_NOME\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $sinteses[$numero - 1]->componente_curricular->nome;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{SINTESE_(\d+)_DESCRICAO\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $sinteses[$numero - 1]->descricao;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{SINTESE_(\d+)_REFLEXAO\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $sinteses[$numero - 1]->reflexao;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/<!-- PROPOSTA_(\d+)_INICIO -->/', $linhaSemEspacos, $matches)) {
                $numero = (int)$matches[1];
                if ($numero > count($propostas)) {
                    $deveEscreverLinha = false;
                }
            } else if (preg_match('/<!-- PROPOSTA_(\d+)_FIM -->/', $linhaSemEspacos, $matches)) {
                $numero = (int)$matches[1];
                $deveEscreverLinha = true;
            } else if (preg_match('/\{PROPOSTA_(\d+)_TITULO\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->titulo;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_NIVEL_FORMACAO\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = PropostaPedagogica::NIVEL_FORMACAO($propostas[$numero - 1]->nivel_formacao);
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_AREA_CONHECIMENTO\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = PropostaPedagogica::AREA_CONHECIMENTO($propostas[$numero - 1]->area_conhecimento);
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_MODALIDADE\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->modalidade;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_NOME_FERRAMENTA\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->nome_ferramenta;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_DESCRICAO_DINAMICA\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->descricao_dinamica;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_DESCRICAO_DIFERENCIAIS\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->descricao_diferenciais;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_DESCRICAO_PROCEDIMENTOS\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->descricao_procedimentos;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_DESCRICAO_REFLEXAO\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->descricao_reflexao;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_DESCRICAO_ABORDAGEM\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->descricao_abordagem;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_DESCRICAO_REFERENCIAS\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = $propostas[$numero - 1]->descricao_referencias;
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            } else if (preg_match('/\{PROPOSTA_(\d+)_TIPO_PROPOSTA\}/', $linhaSemEspacos, $matches)) {
                if ($deveEscreverLinha) {
                    $numero = (int)$matches[1];
                    $texto = PropostaPedagogica::TIPO_PROPOSTA($propostas[$numero - 1]->tipo_proposta);
                    $texto = str_replace(['&', '<', '>', '"'], ['&amp;', '&lt;', '&gt;', '&quot;'], $texto);
                    $texto = str_replace(["\r\n", "\n\r", "\r", "\n"], "</w:t><w:br/><w:t>", $texto);
                    $linha = "<w:t>{$texto}</w:t>";
                }
            }

            if ($deveEscreverLinha && !preg_match('/<!--/', $linhaSemEspacos)) {
                $modeloEditado .= $linha;
            }
        }
        return $modeloEditado;
    }

    public function actionEditarCaracterizacaoEspecialista($tccId)
    {
        $tcc = Tcc::model()->findByPk($tccId);

        if (isset($_POST['salvar'])) {
            $tcc->attributes = $_POST['Tcc'];
            if ($tcc->validate() && $tcc->save()) {
                Yii::app()->user->setFlash('notificacao', 'Caracterização do especialista editada com sucesso!');
                Yii::log("Caracterização do especialista do TCC {$tccId} editada pelo admin.", 'info', 'system.controllers.AdminController');
                $this->redirect(Yii::app()->createUrl('admin/editarTcc', ['id' => $tccId]));
            }
        }

        $this->render('/tcc/editarCaracterizacao', [
            'tcc' => $tcc,
        ]);
    }

    public function actionCadastrarSinteseComponente($tccId)
    {
        $tcc = Tcc::model()->findByPk($tccId);
        $sinteseComponente = new SinteseComponente();
        $sinteseComponente->tcc_id = $tccId;

        if (isset($_POST['salvar'])) {
            $sinteseComponente->attributes = $_POST['SinteseComponente'];
            $sinteseComponente->ordem = count($tcc->sinteses_componentes) + 1;
            if ($sinteseComponente->validate() && $sinteseComponente->save()) {
                Yii::app()->user->setFlash('notificacao', 'Síntese de componente cadastrada com sucesso!');
                Yii::log("Síntese de componente {$sinteseComponente->id} cadastrada pelo admin.", 'info', 'system.controllers.AdminController');
                $this->redirect(Yii::app()->createUrl('admin/editarTcc', ['id' => $tccId]));
            }
        }

        $this->render('/tcc/sinteseComponente/cadastrar', [
            'listaComponentes' => $this->recuperarComponentesAtivosEObrigatoriosPara($tcc->habilitacao_id),
            'sinteseComponente' => $sinteseComponente,
            'tcc' => $tcc,
        ]);
    }

    private function recuperarComponentesAtivosEObrigatoriosPara($habilitacaoId)
    {
        $todosComponentes = ComponenteCurricular::model()->findAll(['order' => 'nome']);
        $listaComponentes = [];
        foreach ($todosComponentes as $componente) {
            if (!$componente->ativo) continue;
            if ($componente->prioridadeParaHabilitacaoNumero($habilitacaoId) != Constantes::PRIORIDADE_OPTATIVA) continue;
            $listaComponentes[$componente->id] = $componente->nome;
        }
        return $listaComponentes;
    }

    public function actionEditarSinteseComponente($id)
    {
        $sinteseComponente = SinteseComponente::model()->findByPk($id);

        if (isset($_POST['salvar'])) {
            $sinteseComponente->attributes = $_POST['SinteseComponente'];
            if ($sinteseComponente->validate() && $sinteseComponente->save()) {
                Yii::app()->user->setFlash('notificacao', 'Síntese de componente atualizada com sucesso!');
                Yii::log("Síntese de componente {$sinteseComponente->id} atualizada pelo admin.", 'info', 'system.controllers.AdminController');
                $this->redirect(Yii::app()->createUrl('admin/editarTcc', ['id' => $sinteseComponente->tcc->id]));
            }
        } else if (isset($_POST['excluir'])) {
            $sinteseComponente->delete();
            Yii::app()->user->setFlash('notificacao', 'Síntese de componente excluída com sucesso!');
            Yii::log("Síntese de componente {$sinteseComponente->id} excluída pelo admin.", 'info', 'system.controllers.AdminController');
            $this->redirect(Yii::app()->createUrl('admin/editarTcc', ['id' => $sinteseComponente->tcc->id]));
        }

        $this->render('/tcc/sinteseComponente/editar', [
            'listaComponentes' => $this->recuperarComponentesAtivosEObrigatoriosPara($sinteseComponente->tcc->habilitacao_id),
            'sinteseComponente' => $sinteseComponente,
            'tcc' => $sinteseComponente->tcc,
        ]);
    }

    public function actionMoverSinteseAcima($id)
    {
        $sinteseComponente = SinteseComponente::model()->findByPk($id);
        $sinteseComOrdemAnterior = SinteseComponente::model()->findByAttributes([
            'tcc_id' => $sinteseComponente->tcc_id,
            'ordem' => $sinteseComponente->ordem - 1,
        ]);
        $sinteseComOrdemAnterior->ordem++;
        $sinteseComponente->ordem--;
        $sinteseComOrdemAnterior->save();
        $sinteseComponente->save();
    }

    public function actionMoverSinteseAbaixo($id)
    {
        $sinteseComponente = SinteseComponente::model()->findByPk($id);
        $sinteseComOrdemPosterior = SinteseComponente::model()->findByAttributes([
            'tcc_id' => $sinteseComponente->tcc_id,
            'ordem' => $sinteseComponente->ordem + 1,
        ]);
        $sinteseComOrdemPosterior->ordem--;
        $sinteseComponente->ordem++;
        $sinteseComOrdemPosterior->save();
        $sinteseComponente->save();
    }

    public function actionCadastrarPropostaPedagogica($tccId)
    {
        $tcc = Tcc::model()->findByPk($tccId);
        $propostaPedagogica = new PropostaPedagogica();
        $propostaPedagogica->tcc_id = $tccId;

        if (isset($_POST['salvar'])) {
            $propostaPedagogica->attributes = $_POST['PropostaPedagogica'];
            $propostaPedagogica->ordem = count($tcc->sinteses_componentes) + 1;
            if ($propostaPedagogica->validate() && $propostaPedagogica->save()) {
                Yii::app()->user->setFlash('notificacao', 'Proposta pedagógica cadastrada com sucesso!');
                Yii::log("Proposta pedagógica {$propostaPedagogica->id} cadastrada pelo admin.", 'info', 'system.controllers.AdminController');
                $this->redirect(Yii::app()->createUrl('admin/editarTcc', ['id' => $tccId]));
            }
        }

        $this->render('/tcc/propostaPedagogica/cadastrar', [
            'propostaPedagogica' => $propostaPedagogica,
            'tcc' => $tcc,
        ]);
    }

    public function actionEditarPropostaPedagogica($id)
    {
        $propostaPedagogica = PropostaPedagogica::model()->findByPk($id);

        if (isset($_POST['salvar'])) {
            $propostaPedagogica->attributes = $_POST['PropostaPedagogica'];
            if ($propostaPedagogica->validate() && $propostaPedagogica->save()) {
                Yii::app()->user->setFlash('notificacao', 'Proposta pedagógica atualizada com sucesso!');
                Yii::log("Proposta pedagógica {$propostaPedagogica->id} atualizada pelo admin.", 'info', 'system.controllers.AdminController');
                $this->redirect(Yii::app()->createUrl('admin/editarTcc', ['id' => $propostaPedagogica->tcc->id]));
            }
        } else if (isset($_POST['excluir'])) {
            $propostaPedagogica->delete();
            Yii::app()->user->setFlash('notificacao', 'Proposta pedagógica excluída com sucesso!');
            Yii::log("Proposta pedagógica {$propostaPedagogica->id} excluída pelo admin.", 'info', 'system.controllers.AdminController');
            $this->redirect(Yii::app()->createUrl('admin/editarTcc', ['id' => $propostaPedagogica->tcc->id]));
        }

        $this->render('/tcc/propostaPedagogica/editar', [
            'propostaPedagogica' => $propostaPedagogica,
            'tcc' => $propostaPedagogica->tcc,
        ]);
    }

    public function actionMoverPropostaAcima($id)
    {
        $propostaPedagogica = PropostaPedagogica::model()->findByPk($id);
        $propostaComOrdemAnterior = PropostaPedagogica::model()->findByAttributes([
            'tcc_id' => $propostaPedagogica->tcc_id,
            'ordem' => $propostaPedagogica->ordem - 1,
        ]);
        $propostaComOrdemAnterior->ordem++;
        $propostaPedagogica->ordem--;
        $propostaComOrdemAnterior->save();
        $propostaPedagogica->save();
    }

    public function actionMoverPropostaAbaixo($id)
    {
        $propostaPedagogica = PropostaPedagogica::model()->findByPk($id);
        $propostaComOrdemPosterior = PropostaPedagogica::model()->findByAttributes([
            'tcc_id' => $propostaPedagogica->tcc_id,
            'ordem' => $propostaPedagogica->ordem + 1,
        ]);
        $propostaComOrdemPosterior->ordem--;
        $propostaPedagogica->ordem++;
        $propostaComOrdemPosterior->save();
        $propostaPedagogica->save();
    }

    public function actionGerarCertificado($inscricaoId, $tipo, $habilitacaoId = null, $formatoTexto = false)
    {
        switch ($tipo) {
            case 'extensao':
                GeradorDeCertificado::deExtensao($inscricaoId, $formatoTexto);
                break;
            case 'aperfeicoamento':
                GeradorDeCertificado::deAperfeicoamento($inscricaoId, $formatoTexto);
                break;
            case 'especializacao':
                GeradorDeCertificado::deEspecializacaoParaHabilitacao($inscricaoId, $habilitacaoId, $formatoTexto);
                break;
        }
    }

    public function actionEditarSelecaoDeComponentesParaCertificados($id)
    {
        $inscricao = Inscricao::model()->findByPk($id);
        $mensagensDeErro = '';

        if (isset($_POST['Salvar'])) {

            $salvadorDeInscricoesEmOfertas = new SalvadorDeInscricoesEmOfertas($inscricao, $_POST['Inscricao']);
            $salvadorDeInscricoesEmOfertas->ehSelecaoDeComponentesParaCertificados();

            if ($salvadorDeInscricoesEmOfertas->salvar()) {
                Yii::app()->user->setFlash('notificacao', 'Seleção de ofertas para certificados feita com sucesso!');
                Yii::log("Administrador selecionou ofertas para certificados para {$inscricao}.", 'info', 'system.controllers.AlunoController');
            } else {
                $mensagensDeErro = $salvadorDeInscricoesEmOfertas->recuperarMensagensDeErro();
                $mensagensDeErroString = implode(",", $mensagensDeErro);
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema na seleção de ofertas para certificados');
                if ($mensagensDeErro) {
                    Yii::log("Problemas quando administrador tentou selecionar ofertas para certificados para inscrição {$inscricao}: {$mensagensDeErroString}", 'error', 'system.controllers.AlunoController');
                } else {
                    Yii::log("Administrador teve problemas na seleção de ofertas para certificados para {$inscricao}.", 'error', 'system.controllers.AlunoController');
                }
            }
        }

        $this->adicionarArquivosJavascript('/js/modulos/recuperarInscricoesEmOfertas');

        $this->render('escolhaComponentesCertificados', array(
            'inscricao' => $inscricao,
            'mensagensDeErro' => $mensagensDeErro,
        ));
    }

    public function actionCriarComponenteProjetoIntegrador()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $COMPONENTE = Constantes::COMPONENTE_DESENVOLVIMENTO_DE_PROJETO_INTEGRADOR;
            $componenteJaExiste = ComponenteCurricular::model()->findByAttributes([ 'nome' => $COMPONENTE ]);
            if (!empty($componenteJaExiste)) {
                echo "Componente \"{$COMPONENTE}\" jã foi criado";
                return;
            }

            $sql = "INSERT INTO componente_curricular(nome, carga_horaria) VALUES ('{$COMPONENTE}', 20)";
            Yii::app()->db->createCommand($sql)->execute();
            $componente = ComponenteCurricular::model()->findByAttributes([ 'nome' => $COMPONENTE ]);

            $habilitacoes = Habilitacao::model()->findAllValid();
            $valores = [];
            $sql = "INSERT INTO componente_habilitacao(componente_curricular_id, habilitacao_id, prioridade) VALUES ";
            foreach ($habilitacoes as $habilitacao) {
                $valores[] = "({$componente->id}, {$habilitacao->id}, 0)"; // prioridade 0 = obrigatório
            }
            $sql .= implode(',', $valores);
            Yii::app()->db->createCommand($sql)->execute();

            $sql = "INSERT INTO oferta(componente_curricular_id, ano, mes) VALUES ({$componente->id}, 1, 1);";
            Yii::app()->db->createCommand($sql)->execute();
            $oferta = Oferta::model()->findByAttributes([ 'componente_curricular_id' => $componente->id ]);

            $inscricoesEspecializacao = Inscricao::model()->with('habilitacoes')->findAllByAttributes([
                'ativo' => true,
                'status' => Inscricao::STATUS_MATRICULADO,
                'tipo_curso' => Inscricao::TIPO_CURSO_ESPECIALIZACAO,
            ]);
            $sql = "INSERT INTO inscricao_oferta(inscricao_id, oferta_id) VALUES ";
            $sql2 = "INSERT INTO habilitacao_inscricao_oferta(habilitacao_id, inscricao_id, oferta_id) VALUES ";
            $valores = [];
            $valores2 = [];
            foreach ($inscricoesEspecializacao as $inscricao) {
                $valores[] = "({$inscricao->id}, {$oferta->id})";
                foreach ($inscricao->habilitacoes as $habilitacao) {
                    $valores2[] = "({$habilitacao->id}, {$inscricao->id}, {$oferta->id})";
                }
            }
            $sql .= implode(',', $valores);
            $sql2 .= implode(',', $valores2);
            Yii::app()->db->createCommand($sql)->execute();
            Yii::app()->db->createCommand($sql2)->execute();

            echo "Componente \"{$COMPONENTE}\" criado com sucesso.";

            $transaction->commit();
            Yii::log("Componente \"{$COMPONENTE}\" criado com sucesso.", 'info', 'system.controllers.AdminController');
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::log("Problema ao criar o componente \"{$COMPONENTE}\" ", 'error', 'system.controllers.AdminController');
        }

    }

    public function actionAtualizarPrioridadeDoComponenteProjetoIntegradorParaNFE()
    {
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $COMPONENTE = Constantes::COMPONENTE_DESENVOLVIMENTO_DE_PROJETO_INTEGRADOR;
            $modelComponente = ComponenteCurricular::model()->findByAttributes([ 'nome' => $COMPONENTE ]);
            if (empty($modelComponente)) {
                echo "Componente \"{$COMPONENTE}\" ainda não foi criado";
                return;
            }
            $idComponente = $modelComponente->id;

            $sql = "UPDATE componente_habilitacao SET prioridade = 1 WHERE componente_curricular_id = {$idComponente}";
            Yii::app()->db->createCommand($sql)->execute();

            echo "Prioridade do componente \"{$COMPONENTE}\" atualizada com sucesso para NFE.";
            $transaction->commit();
            Yii::log("Prioridade do componente \"{$COMPONENTE}\" atualizada com sucesso.", 'info', 'system.controllers.AdminController');
        } catch (Exception $e) {
            $transaction->rollback();
            Yii::log("Problema ao atualizar a prioridade do componente \"{$COMPONENTE}\" ", 'error', 'system.controllers.AdminController');
        }
    }

    public function actionGerarAtestadoDocencia($docenteCpf, $ofertaId)
    {
        GeradorDeAtestado::deDocencia($docenteCpf, $ofertaId);
    }

    public function actionGerarAtestadoTutoria($tutorCpf, $ofertaId)
    {
        GeradorDeAtestado::deTutoria($tutorCpf, $ofertaId);
    }

    public function actionGerarAtestadoOrientador($docenteCpf, $tccId)
    {
        GeradorDeAtestado::deorientacao($docenteCpf, $tccId);
    }

    public function actionGerarAtestadoBanca($docenteCpf, $tccId)
    {
        GeradorDeAtestado::deMembroDeBanca($docenteCpf, $tccId);
    }

    public function actionMostrarLogs()
    {
        $sql = 'SELECT * FROM log ORDER BY id DESC LIMIT 300';
        $logs = Yii::app()->db->createCommand($sql)->queryAll();
        var_export($logs);
    }

    public function actionExecutarSql($sql, $token)
    {
        if (hash('sha256', $token) !== '7b31f574aa5e35b84f370021a5d3adc70e21b4f090f00cd4cb0f552c9a7958ce') return;
        $resultado = Yii::app()->db->createCommand($sql)->execute();
        var_export($resultado);
    }

    public function actionConsultarSql($sql, $token)
    {
        if (hash('sha256', $token) !== '7b31f574aa5e35b84f370021a5d3adc70e21b4f090f00cd4cb0f552c9a7958ce') return;
        $resultados = Yii::app()->db->createCommand($sql)->queryAll();
        var_export($resultados);
    }

    public function actionConsultarSqlAva($sql, $token)
    {
        if (hash('sha256', $token) !== '7b31f574aa5e35b84f370021a5d3adc70e21b4f090f00cd4cb0f552c9a7958ce') return;
        $resultados = Yii::app()->dbAva->createCommand($sql)->queryAll();
        var_export($resultados);
    }

    public function actionRodarComando($comando, $token)
    {
        if (hash('sha256', $token) !== '7b31f574aa5e35b84f370021a5d3adc70e21b4f090f00cd4cb0f552c9a7958ce') return;
        exec($comando, $output);
        var_export($output);
    }

    public function actionTesteNotas()
    {
//        $sql = "SELECT DISTINCT * FROM notas_do_moodle WHERE codigo_componente = 'EDUTEC_363'";
//        $notas = Yii::app()->dbAva->createCommand($sql)->queryAll();
	    // var_dump($notas);
	    // 
$inscricao = Inscricao::model()->findByNumeroUfscar('710899');
        var_dump($inscricao);   
	    $inscricaoOfertas = InscricaoOferta::encontrarInscricaoOfertas('710899', 'EDUTEC_363');
        var_dump($inscricaoOfertas);
    }

}
