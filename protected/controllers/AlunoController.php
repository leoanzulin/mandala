<?php

class AlunoController extends Controller
{

    public function actionIndex()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $this->render('index', array(
            'nome' => $inscricao->nome,
            'haInscricoesAConfirmar' => $this->haInscricoesAConfirmar($inscricao),
        ));
    }

    private function haInscricoesAConfirmar($inscricao)
    {
        [ $proximoMes, $ano ] = CalendarioHelper::proximoMesEAno();

        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $inscricoesNaoConfirmadasEmOfertas = $recuperadorDeOfertas->daInscricao($inscricao->id)
            ->semCamposRelacionados()
            ->manterApenasInscricoesNaoConfirmadasEmOfertas()
            ->doMesEAno($proximoMes, $ano)
            ->recuperar();
        if (empty($inscricoesNaoConfirmadasEmOfertas)) return false;
        $inscricoesNaoConfirmadasEmOfertas = $inscricoesNaoConfirmadasEmOfertas[0]['ofertas'];

        foreach ($inscricoesNaoConfirmadasEmOfertas as $inscricaoNaoConfirmada) {
            if (CalendarioHelper::estaDentroDoPeriodoDeConfirmacao($inscricaoNaoConfirmada)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Tela de inscrição dos alunos em ofertas.
     */
    public function actionInscricao()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $mensagensDeErro = '';

        if (isset($_POST['gerar_pdf'])) {
            GeradorDeDeclaracaoDeInscricoes::gerar($inscricao, $_POST['ContagemDeComponentes']);
        } else if (isset($_POST['Salvar'])) {

            $salvadorDeInscricoesEmOfertas = new SalvadorDeInscricoesEmOfertas($inscricao, $_POST['Inscricao']);

            if ($salvadorDeInscricoesEmOfertas->salvar()) {
                Yii::app()->user->setFlash('notificacao', 'Inscrição em ofertas feitas com sucesso!');
                Yii::log("{$inscricao} se inscreveu em ofertas.", 'info', 'system.controllers.AlunoController');
            } else {
                $mensagensDeErro = $salvadorDeInscricoesEmOfertas->recuperarMensagensDeErro();
                $mensagensDeErroString = implode(",", $mensagensDeErro);
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema na inscrição em ofertas');
                if ($mensagensDeErro) {
                    Yii::log("Problemas quando inscrição {$inscricao} tentou se inscrever em ofertas: {$mensagensDeErroString}", 'error', 'system.controllers.AlunoController');
                } else {
                    Yii::log("{$inscricao} teve problemas na inscrição em ofertas.", 'error', 'system.controllers.AlunoController');
                }
            }
        }

        $this->adicionarArquivosJavascript('/js/modulos/recuperarInscricoesEmOfertas');

        $this->render('inscricao', array(
            'inscricao' => $inscricao,
            'mensagensDeErro' => $mensagensDeErro,
        ));
    }

    public function actionPerfil()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);

        $this->render('perfil', array(
            'model' => $inscricao,
        ));
    }

    public function actionEditarPerfil()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);

        if (isset($_POST['Inscricao'])) {
            $inscricao->attributes = $_POST['Inscricao'];

            if ($inscricao->validate() && $inscricao->update()) {
                $this->salvarFormacoes($inscricao);
                Yii::app()->user->setFlash('notificacao', 'Alterações salvas com sucesso!');
                Yii::log("{$inscricao} alterou suas informações de perfil.", 'info', 'system.controllers.AlunoController');
                $this->redirect(Yii::app()->createUrl('aluno/perfil'));
            }
        }

        $this->adicionarArquivosJavascript('/js/modulos/inscricaoApp');

        $this->render('editarPerfil', array(
            'model' => $inscricao,
            'modelFormacao' => new Formacao(),
        ));
    }

    private function salvarFormacoes(Inscricao $inscricao)
    {
        foreach ($inscricao->formacao as $formacao) {
            $f = new Formacao();
            $f->attributes = $formacao;
            $f->inscricao_id = $inscricao->id;
            $f->save();
        }
    }

    public function actionTrocarSenha()
    {
        if (isset($_POST['trocar'])) {
            ResetarSenha::model()->fazerSolicitacao(Yii::app()->user->id);
            $this->redirect(Yii::app()->createUrl('aluno/perfil'));
        }
        $this->render('trocarSenha');
    }

    /**
     * Interface REST para recuperar as inscrições que têm status de alunos
     * matriculados.
     */
    public function actionRecuperarAlunos()
    {
        $alunos = Inscricao::model()->findAllByAttributes(array(
            'status' => Inscricao::STATUS_MATRICULADO
        ));
        $alunosArray = array_map(function($aluno) {
            return $aluno->asArray();
        }, $alunos);
        $this->respostaJSON(json_encode($alunosArray));
    }

    /**
     * Interface REST para recuperar as informações de pagamento de inscrições
     * que têm status 'Matriculado'.
     */
    public function actionRecuperarAlunosPagamento()
    {
        $alunos = Inscricao::model()->findAllByAttributes(array(
            'status' => Inscricao::STATUS_MATRICULADO
        ));
        $alunosArray = array_map(function($aluno) {
            return $aluno->asArrayPagamento();
        }, $alunos);
        $this->respostaJSON(json_encode($alunosArray));
    }

    /**
     * REST
     */
    public function actionRecuperarLimiteDeOfertasQuePodemSerInscritas()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $limite = array('limite' => $inscricao->numeroDeOfertasQuePodeSeInscrever());
        $this->respostaJSON(json_encode($limite));
    }

    public function actionHistorico()
    {
        RecuperadorDeNotas::recuperarNotasDoAlunoDeCpf(Yii::app()->user->id);
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);

        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $ofertasPorPeriodo = $recuperadorDeOfertas
                ->daInscricao($inscricao->id)
                ->semCamposRelacionados()
                ->manterApenasInscritas()
                ->manterApenasOfertasPassadasEPresentes()
                ->recuperar();

        if (isset($_POST['gerar_pdf'])) {
            GeradorDeHistorico::gerar($inscricao);
        }

        if (isset($_POST['gerar_pdf_limpo'])) {
            GeradorDeHistorico::gerar($inscricao, true);
        }

        $this->render('historico', array(
            'inscricao' => $inscricao,
            'ofertasPorPeriodo' => $ofertasPorPeriodo,
        ));
    }

    public function actionEscolhaComponentesCertificados()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $mensagensDeErro = '';

        if (isset($_POST['Salvar'])) {

            $salvadorDeInscricoesEmOfertas = new SalvadorDeInscricoesEmOfertas($inscricao, $_POST['Inscricao']);
            $salvadorDeInscricoesEmOfertas->ehSelecaoDeComponentesParaCertificados();

            if ($salvadorDeInscricoesEmOfertas->salvar()) {
                Yii::app()->user->setFlash('notificacao', 'Seleção de ofertas para certificados feita com sucesso!');
                Yii::log("{$inscricao} selecionou ofertas para certificados.", 'info', 'system.controllers.AlunoController');
            } else {
                $mensagensDeErro = $salvadorDeInscricoesEmOfertas->recuperarMensagensDeErro();
                $mensagensDeErroString = implode(",", $mensagensDeErro);
                Yii::app()->user->setFlash('notificacao-negativa', 'Problema na seleção de ofertas para certificados');
                if ($mensagensDeErro) {
                    Yii::log("Problemas quando inscrição {$inscricao} tentou selecionar ofertas para certificados: {$mensagensDeErroString}", 'error', 'system.controllers.AlunoController');
                } else {
                    Yii::log("{$inscricao} teve problemas na seleção de ofertas para certificados.", 'error', 'system.controllers.AlunoController');
                }
            }
        }

        $this->adicionarArquivosJavascript('/js/modulos/recuperarInscricoesEmOfertas');

        $this->render('escolhaComponentesCertificados', array(
            'inscricao' => $inscricao,
            'mensagensDeErro' => $mensagensDeErro,
        ));
    }

    public function actionConfirmarInscricoesEmOfertas()
    {
        $aluno = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        [ $mes, $ano ] = CalendarioHelper::proximoMesEAno();

        $recuperadorDeOfertas = new RecuperadorDeOfertas();
        $ofertasInscritasDesteMes = $recuperadorDeOfertas
                ->daInscricao($aluno->id)
                ->semCamposRelacionados()
                ->manterApenasInscritas()
                ->doMesEAno($mes, $ano)
                ->recuperar();
        $ofertasInscritasDesteMes = $ofertasInscritasDesteMes[0]['ofertas'] ?? [];

        $estaBloqueado = true;
        foreach ($ofertasInscritasDesteMes as $oferta) {
            if (CalendarioHelper::estaDentroDoPeriodoDeConfirmacao($oferta)) {
                $estaBloqueado = false;
            }
        }

        if (isset($_POST['Salvar'])) {
            $ofertasConfirmadas = isset($_POST['ofertas']) ? $_POST['ofertas'] : [];

            foreach ($ofertasConfirmadas as &$ofertaId) $ofertaId = (int) $ofertaId;

            foreach ($ofertasInscritasDesteMes as $oferta) {
                $inscricaoOferta = InscricaoOferta::model()->findByPk(array(
                    'oferta_id' => $oferta['id'],
                    'inscricao_id' => $aluno->id,
                ));
                $inscricaoOferta->confirmada = in_array($oferta['id'], $ofertasConfirmadas);
                $inscricaoOferta->saveAttributes(array('confirmada'));
            }

            Yii::app()->user->setFlash('notificacao', 'Inscricções confirmadas com sucesso!');
            Yii::log("{$aluno} confirmou suas inscrições.", 'info', 'system.controllers.AlunoController');
            $this->redirect(Yii::app()->createUrl('aluno'));
        }

        $this->adicionarArquivosJavascript('/js/confirmarInscricoes');

        $this->render('confirmarInscricoesEmOfertas', array(
            'ofertasInscritasDesteMes' => $ofertasInscritasDesteMes,
            'ano' => $ano,
            'mes' => $mes,
            'bloqueado' => $estaBloqueado,
        ));
    }

    public function actionTcc()
    {
        $this->render('tcc');
    }

    public function actionCriarTcc()
    {
        $aluno = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $this->render('criarTcc', [
            'aluno' => $aluno,
            'habilitacoes' => $aluno->recuperarHabilitacoes(),
        ]);
    }

    public function actionEntregarTccs()
    {
        $aluno = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $this->render('entregarTccs', [
            'aluno' => $aluno,
        ]);
    }

    public function actionEntregarTcc($id)
    {
        $tcc = Tcc::model()->findByPk($id);

        if (isset($_POST['salvar'])) {
            $tcc->attributes = $_POST['Tcc'];
            $tcc = $this->recuperarDocumentosUpados($tcc);
            if ($tcc->entregou_validacao) {
                $tcc->validacao_data_entrega = date('Y-m-d');
            }
            if ($tcc->entregou_banca) {
                $tcc->banca_data_entrega = date('Y-m-d');
            }
            if ($tcc->entregou_final) {
                $tcc->final_data_entrega = date('Y-m-d');
            }
            if ($tcc->validate() && $tcc->save()) {
                if ($tcc->entregou_validacao) {
                    Evento::alunoEntregouTccValidacao($tcc->id);
                }
                if ($tcc->entregou_banca) {
                    Evento::alunoEntregouTccBanca($tcc->id);
                }
                if ($tcc->entregou_final) {
                    Evento::alunoEntregouTccFinal($tcc->id);
                }
                $this->salvarDocumentosNoDisco($tcc);
                Yii::app()->user->setFlash('notificacao', 'TCC entregue com sucesso!');
                Yii::log("TCC {$tcc} (ID {$tcc->id}) entregue.", 'info', 'system.controllers.AlunoController');
                $this->redirect(Yii::app()->createUrl('aluno/entregarTcc', ['id' => $tcc->id]));
            } else {
                if ($tcc->entregou_validacao) {
                    $tcc->validacao_arquivo = null;
                    $tcc->validacao_data_entrega = null;
                }
                if ($tcc->entregou_banca) {
                    $tcc->banca_arquivo = null;
                    $tcc->banca_data_entrega = null;
                }
                if ($tcc->entregou_final) {
                    $tcc->final_arquivo_doc = null;
                    $tcc->final_arquivo_pdf = null;
                    $tcc->final_data_entrega = null;
                }
            }
        }

        $this->render('entregarTcc', [
            'tcc' => $tcc,
        ]);
    }

    private function recuperarDocumentosUpados($tcc)
    {
        $validacaoArquivo = CUploadedFile::getInstance($tcc, 'validacao_arquivo');
        $bancaArquivo = CUploadedFile::getInstance($tcc, 'banca_arquivo');
        $finalArquivoDoc = CUploadedFile::getInstance($tcc, 'final_arquivo_doc');
        $finalArquivoPdf = CUploadedFile::getInstance($tcc, 'final_arquivo_pdf');
        if (empty($tcc->validacao_arquivo) && !empty($validacaoArquivo)) {
            $tcc->entregou_validacao = true;
        }
        if (empty($tcc->banca_arquivo) && !empty($bancaArquivo)) {
            $tcc->entregou_banca = true;
        }
        if (empty($tcc->final_arquivo_doc) && !empty($finalArquivoDoc)) {
            $tcc->entregou_final = true;
        }
        if (!empty($validacaoArquivo)) $tcc->validacao_arquivo = $validacaoArquivo;
        if (!empty($bancaArquivo)) $tcc->banca_arquivo = $bancaArquivo;
        if (!empty($finalArquivoDoc)) $tcc->final_arquivo_doc = $finalArquivoDoc;
        if (!empty($finalArquivoPdf)) $tcc->final_arquivo_pdf = $finalArquivoPdf;
        return $tcc;
    }

    private function salvarDocumentosNoDisco($tcc)
    {
        $base = Yii::app()->basePath . "/../tccs/tcc_{$tcc->id}";

        if (!empty($tcc->validacao_arquivo) && $tcc->validacao_arquivo instanceof CUploadedFile) {
            $extensao = $this->recuperarExtensao($tcc->validacao_arquivo);
            $tcc->validacao_arquivo->saveAs("{$base}_validacao.{$extensao}");
        }
        if (!empty($tcc->banca_arquivo) && $tcc->banca_arquivo instanceof CUploadedFile) {
            $extensao = $this->recuperarExtensao($tcc->banca_arquivo);
            $tcc->banca_arquivo->saveAs("{$base}_banca.{$extensao}");
        }
        if (!empty($tcc->final_arquivo_doc) && $tcc->final_arquivo_doc instanceof CUploadedFile) {
            $tcc->final_arquivo_doc->saveAs("{$base}_final.doc");
        }
        if (!empty($tcc->final_arquivo_pdf) && $tcc->final_arquivo_pdf instanceof CUploadedFile) {
            $tcc->final_arquivo_pdf->saveAs("{$base}_final.pdf");
        }
    }

    private function recuperarExtensao($nomeArquivo)
    {
        $partes = pathinfo($nomeArquivo);
        return $partes['extension'];
    }

    public function actionCadastrarTcc($habilitacaoId)
    {
        $aluno = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        $habilitacao = Habilitacao::model()->findByPk($habilitacaoId);

        $tcc = new Tcc();
        $tcc->inscricao_id = $aluno->id;
        $tcc->habilitacao_id = $habilitacaoId;

        if (isset($_POST['Tcc'])) {
            $tcc->attributes = $_POST['Tcc'];
            if ($tcc->validate() && $tcc->save()) {
                Yii::app()->user->setFlash('notificacao', 'TCC cadastrado com sucesso!');
                Yii::log("TCC {$tcc} (ID {$tcc->id}) cadastrado.", 'info', 'system.controllers.AlunoController');
                $this->redirect(Yii::app()->createUrl('aluno/editarTcc', ['id' => $tcc->id]));
            }
        }

        $this->render('/tcc/cadastrar', [
            'tcc' => $tcc,
            'habilitacao' => $habilitacao,
            'listaDocentes' => $this->recuperarDocentes(),
        ]);
    }

    private function recuperarDocentes()
    {
        $todosDocentes = Docente::model()->findAll(array('order' => 'nome, sobrenome'));
        $listaDocentes = [];
        foreach ($todosDocentes as $docente) {
            $listaDocentes[$docente->cpf] = $docente->nomeCompleto;
        }
        return $listaDocentes;
    }

    public function actionEditarTcc($id)
    {
        $tcc = Tcc::model()->findByPk($id);
        $this->checarSeEhMeuTcc($tcc);
        $habilitacao = $tcc->habilitacao;

        if (isset($_POST['salvar'])) {
            $tcc->attributes = $_POST['Tcc'];
            if ($tcc->validate() && $tcc->save()) {
                Yii::app()->user->setFlash('notificacao', 'TCC atualizado com sucesso!');
                Yii::log("TCC {$tcc} (ID {$tcc->id}) atualizado.", 'info', 'system.controllers.AlunoController');
            }
        } else if (isset($_POST['exportar'])) {
            $tcc->attributes = $_POST['Tcc'];
            if ($tcc->validate() && $tcc->save()) {
                $this->exportarTccParaDocx($tcc);
            }
        } else if (isset($_POST['excluir'])) {
            SinteseComponente::model()->deleteAllByAttributes(['tcc_id' => $tcc->id]);
            PropostaPedagogica::model()->deleteAllByAttributes(['tcc_id' => $tcc->id]);
            $tcc->delete();
            Yii::app()->user->setFlash('notificacao', 'TCC excluído com sucesso!');
            Yii::log("TCC {$tcc} (ID {$tcc->id}) excluído.", 'info', 'system.controllers.AlunoController');
            $this->redirect(Yii::app()->createUrl('aluno/tcc'));
        }

        $this->render('/tcc/editar', [
            'tcc' => $tcc,
            'habilitacao' => $habilitacao,
            'listaDocentes' => $this->recuperarDocentes(),
            'ehCoordenacao' => false,
        ]);
    }

    private function checarSeEhMeuTcc($tcc)
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
        if ($tcc->inscricao_id !== $inscricao->id) {
            throw new CHttpException(403, 'Este TCC não pertence à sua inscrição.');
        }
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
        $this->checarSeEhMeuTcc($tcc);

        if (isset($_POST['salvar'])) {
            $tcc->attributes = $_POST['Tcc'];
            if ($tcc->validate() && $tcc->save()) {
                Yii::app()->user->setFlash('notificacao', 'Caracterização do especialista editada com sucesso!');
                Yii::log("Caracterização do especialista do TCC {$tccId} editada.", 'info', 'system.controllers.AlunoController');
                $this->redirect(Yii::app()->createUrl('aluno/editarTcc', ['id' => $tccId]));
            }
        }

        $this->render('/tcc/editarCaracterizacao', [
            'tcc' => $tcc,
        ]);
    }

    public function actionCadastrarSinteseComponente($tccId)
    {
        $tcc = Tcc::model()->findByPk($tccId);
        $this->checarSeEhMeuTcc($tcc);
        $sinteseComponente = new SinteseComponente();
        $sinteseComponente->tcc_id = $tccId;

        if (isset($_POST['salvar'])) {
            $sinteseComponente->attributes = $_POST['SinteseComponente'];
            $sinteseComponente->ordem = count($tcc->sinteses_componentes) + 1;
            if ($sinteseComponente->validate() && $sinteseComponente->save()) {
                Yii::app()->user->setFlash('notificacao', 'Síntese de componente cadastrada com sucesso!');
                Yii::log("Síntese de componente {$sinteseComponente->id} cadastrada.", 'info', 'system.controllers.AlunoController');
                $this->redirect(Yii::app()->createUrl('aluno/editarTcc', ['id' => $tccId]));
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
            if ($componente->prioridadeParaHabilitacaoNumero($habilitacaoId) == Constantes::PRIORIDADE_NECESSARIA) continue;
            $listaComponentes[$componente->id] = $componente->nome;
        }
        return $listaComponentes;
    }

    public function actionEditarSinteseComponente($id)
    {
        $sinteseComponente = SinteseComponente::model()->findByPk($id);
        $this->checarSeEhMeuTcc($sinteseComponente->tcc);

        if (isset($_POST['salvar'])) {
            $sinteseComponente->attributes = $_POST['SinteseComponente'];
            if ($sinteseComponente->validate() && $sinteseComponente->save()) {
                Yii::app()->user->setFlash('notificacao', 'Síntese de componente atualizada com sucesso!');
                Yii::log("Síntese de componente {$sinteseComponente->id} atualizada.", 'info', 'system.controllers.AlunoController');
                $this->redirect(Yii::app()->createUrl('aluno/editarTcc', ['id' => $sinteseComponente->tcc->id]));
            }
        } else if (isset($_POST['excluir'])) {
            $sinteseComponente->delete();
            Yii::app()->user->setFlash('notificacao', 'Síntese de componente excluída com sucesso!');
            Yii::log("Síntese de componente {$sinteseComponente->id} excluída.", 'info', 'system.controllers.AlunoController');
            $this->redirect(Yii::app()->createUrl('aluno/editarTcc', ['id' => $sinteseComponente->tcc->id]));
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
        $this->checarSeEhMeuTcc($tcc);
        $propostaPedagogica = new PropostaPedagogica();
        $propostaPedagogica->tcc_id = $tccId;

        if (isset($_POST['salvar'])) {
            $propostaPedagogica->attributes = $_POST['PropostaPedagogica'];
            $propostaPedagogica->ordem = count($tcc->sinteses_componentes) + 1;
            if ($propostaPedagogica->validate() && $propostaPedagogica->save()) {
                Yii::app()->user->setFlash('notificacao', 'Proposta pedagógica cadastrada com sucesso!');
                Yii::log("Proposta pedagógica {$propostaPedagogica->id} cadastrada.", 'info', 'system.controllers.AlunoController');
                $this->redirect(Yii::app()->createUrl('aluno/editarTcc', ['id' => $tccId]));
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
        $this->checarSeEhMeuTcc($propostaPedagogica->tcc);

        if (isset($_POST['salvar'])) {
            $propostaPedagogica->attributes = $_POST['PropostaPedagogica'];
            if ($propostaPedagogica->validate() && $propostaPedagogica->save()) {
                Yii::app()->user->setFlash('notificacao', 'Proposta pedagógica atualizada com sucesso!');
                Yii::log("Proposta pedagógica {$propostaPedagogica->id} atualizada.", 'info', 'system.controllers.AlunoController');
                $this->redirect(Yii::app()->createUrl('aluno/editarTcc', ['id' => $propostaPedagogica->tcc->id]));
            }
        } else if (isset($_POST['excluir'])) {
            $propostaPedagogica->delete();
            Yii::app()->user->setFlash('notificacao', 'Proposta pedagógica excluída com sucesso!');
            Yii::log("Proposta pedagógica {$propostaPedagogica->id} excluída.", 'info', 'system.controllers.AlunoController');
            $this->redirect(Yii::app()->createUrl('aluno/editarTcc', ['id' => $propostaPedagogica->tcc->id]));
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

    public function actionGerarAtestadoEspecializacao($habilitacaoId)
    {
        GeradorDeAtestado::deEspecializacao(Yii::app()->session['inscricao_id'], $habilitacaoId);
    }

    public function actionGerarAtestadoAperfeicoamento()
    {
        GeradorDeAtestado::deAperfeicoamento(Yii::app()->session['inscricao_id']);
    }

    public function actionGerarAtestadoExtensao()
    {
        GeradorDeAtestado::deExtensao(Yii::app()->session['inscricao_id']);
    }

    public function actionGerarAtestadoMatricula()
    {
        GeradorDeAtestado::deMatricula(Yii::app()->session['inscricao_id']);
    }

}
