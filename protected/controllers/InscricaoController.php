<?php

class InscricaoController extends Controller
{

    /**
     * Formulário de inscrição.
     */
    public function actionIndex()
    {
        $inscricao = new Inscricao();

        if (isset($_POST['Inscricao'])) {
            $inscricao->attributes = $_POST['Inscricao'];
            if ($inscricao->ehAlunoDeEspecializacao()) {
                $this->processarHabilitacoes($inscricao);
            }
            $inscricao = $this->recuperarDocumentosUpados($inscricao);
            $inscricao->status = Inscricao::STATUS_DOCUMENTOS_SENDO_ANALISADOS;
            $inscricao->turma = Constantes::TURMA_ABERTA;
            $inscricao->recebe_bolsa = false;

            if ($inscricao->save()) {
                $this->salvarFormacoes($inscricao);
                $this->salvarHabilitacoes($inscricao);
                $this->salvarDocumentosNoDisco($inscricao);

                Yii::log("{$inscricao->nome} (CPF {$inscricao->cpf}) pré-inscrito com sucesso.", 'info', 'system.controllers.InscricaoController');
                $senha = $inscricao->transformarEmUsuario();
                if (!empty($senha)) {
                    Email::mensagemPreInscricao($inscricao->nomeCompleto, $inscricao->email, $inscricao->cpf, $senha);
                } else {
                    Email::mensagemPreInscricaoSemSenha($inscricao->nomeCompleto, $inscricao->email);
                }
                Email::mensagemInternaUsuarioSeInscreveu($inscricao->nomeCompleto, $inscricao->email, $inscricao->cpf);
                $this->redirect(array('sucesso', 'id' => $inscricao->id));
            }
        }

        $this->adicionarArquivosJavascript('/js/modulos/inscricaoApp');

        $this->render('index', array(
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

    private function salvarFormacoes(Inscricao $inscricao)
    {
        foreach ($inscricao->formacao as $formacao) {
            $f = new Formacao();
            $f->attributes = $formacao;
            $f->inscricao_id = $inscricao->id;
            // TODO: Colocar validação aqui
            $f->save();
        }
    }

    private function salvarHabilitacoes(Inscricao $inscricao)
    {
        $ordem = 1;
        foreach ($inscricao->habilitacoesEscolhidas as $habilitacao) {
            $ih = new InscricaoHabilitacao();
            $ih->inscricao_id = $inscricao->id;
            $ih->habilitacao_id = $habilitacao;
            $ih->ordem = $ordem;
            $ih->save();
            $ordem++;
        }
    }

    public function loadModel($id)
    {
        $model = Inscricao::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Tela de inscrição feita com sucesso.
     */
    public function actionSucesso($id)
    {
        $this->render('sucesso', array(
            'inscricao' => Inscricao::model()->findByPk($id),
        ));
    }

    /**
     * DEPRECATED
     * Formulário de envio de documentos.
     * 
     * @param string $cpf
     * @throws CHttpException
     */
    public function actionDocumentos($cpf)
    {
        $model = Inscricao::model()->findByCpf($cpf);

        $this->validarInscricaoNula($model);
        $this->validarJaEnviouDocumentos($model);

        if (isset($_POST['Inscricao'])) {
            $model = $this->recuperarDocumentosUpados($model);
            if ($model->validate() && $model->update()) {
                $this->salvarDocumentosNoDisco($model);
                // TODO: Descomentar isto no servidor
//                Email::mensagemInternaUsuarioEnviouDocumentos($model->nome, $model->email, $model->cpf);
                Yii::log("{$model} enviou seus documentos ao sistema.", 'info', 'system.controllers.InscricaoController');
                $this->redirect(array('documentosSucesso', 'cpf' => $model->cpf));
            } else {
                Yii::log("Ocorreu um problema no envio dos documentos de {$model}: " . print_r($model->errors, true), 'error', 'system.controllers.InscricaoController');
            }
        }

        $this->render('documentos', array('model' => $model));
    }

    private function validarInscricaoNula($model)
    {
        if ($model === null)
            throw new CHttpException(404, 'O CPF informado não está inscrito no EDUTEC.');
    }

    private function validarJaEnviouDocumentos($model)
    {
        if (!empty($model->documento_cpf) &&
                !empty($model->documento_rg) &&
                !empty($model->documento_curriculo) &&
                !empty($model->documento_comprovante_residencia) &&
                !empty($model->documento_diploma))
            throw new CHttpException(500, 'Você já enviou os documentos necessários para inscrição.');
    }

    private function recuperarDocumentosUpados($model)
    {
//        $documentos = array('cpf', 'rg', 'diploma', 'comprovante_residencia', 'curriculo', 'justificativa');
//        foreach ($this->documentos as $documento) {
//            $atributo = "documento_{$documento}";
//            $model->$atributo = CUploadedFile::getInstance($model, $atributo);
//        }
        $model->documento_cpf = CUploadedFile::getInstance($model, 'documento_cpf');
        $model->documento_rg = CUploadedFile::getInstance($model, 'documento_rg');
        $model->documento_diploma = CUploadedFile::getInstance($model, 'documento_diploma');
        $model->documento_comprovante_residencia = CUploadedFile::getInstance($model, 'documento_comprovante_residencia');
        $model->documento_curriculo = CUploadedFile::getInstance($model, 'documento_curriculo');
        $model->documento_justificativa = CUploadedFile::getInstance($model, 'documento_justificativa');
        return $model;
    }

    private function salvarDocumentosNoDisco($inscricao)
    {
        $base = Yii::app()->basePath . "/../uploads/{$inscricao->cpf}_";

        $extensao = $this->recuperarExtensao($inscricao->documento_cpf);
        $inscricao->documento_cpf->saveAs("{$base}cpf.{$extensao}");
        $extensao = $this->recuperarExtensao($inscricao->documento_rg);
        $inscricao->documento_rg->saveAs("{$base}rg.{$extensao}");
        $extensao = $this->recuperarExtensao($inscricao->documento_diploma);
        $inscricao->documento_diploma->saveAs("{$base}diploma.{$extensao}");
        $extensao = $this->recuperarExtensao($inscricao->documento_comprovante_residencia);
        $inscricao->documento_comprovante_residencia->saveAs("{$base}comprovante_residencia.{$extensao}");
        $extensao = $this->recuperarExtensao($inscricao->documento_curriculo);
        $inscricao->documento_curriculo->saveAs("{$base}curriculo.{$extensao}");
        if (!empty($inscricao->documento_justificativa)) {
            $extensao = $this->recuperarExtensao($inscricao->documento_justificativa);
            $inscricao->documento_justificativa->saveAs("{$base}justificativa.{$extensao}");
        }
    }

    private function recuperarExtensao($nomeArquivo)
    {
        $partes = pathinfo($nomeArquivo);
        return $partes['extension'];
    }

    public function actionDocumentosSucesso($cpf)
    {
        $this->render('documentosSucesso', array(
            'inscricao' => Inscricao::model()->findByCpf($cpf),
        ));
    }

    /**
     * Tela de adição de informações complementares. Os alunos não podem
     * utilizar o sistema enquanto não fornecerem estas informações.
     */
    public function actionInformacoesComplementares()
    {
        $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);

        $this->verificaSePrecisaPreencherInformacoesComplementares($inscricao);

        if (isset($_POST['Inscricao'])) {
            $inscricao->attributes = $_POST['Inscricao'];

            if ($inscricao->validate() && $inscricao->update()) {
                $inscricao->removerPapel('InscritoComInformacoesFaltantes');
                $inscricao->atribuirPapel('Inscrito');
                Yii::log("{$inscricao} preencheu informações faltantes.", 'info', 'system.controllers.InscricaoController');
                Yii::app()->user->setFlash('notificacao', 'Informações salvas com sucesso!');
                $this->redirect(Yii::app()->createUrl('aluno'));
            }
        }

        $this->adicionarArquivosJavascript('/js/informacoesComplementares');

        $this->render('informacoesComplementares', array(
            'model' => $inscricao,
        ));
    }

    private function verificaSePrecisaPreencherInformacoesComplementares($inscricao)
    {
        // Se o usuário não precisa preencher as infromações faltantes, o redireciona
        if (!$inscricao->temPapel('InscritoComInformacoesFaltantes')) {
            $this->redirect(Yii::app()->createUrl('aluno'));
        }
    }

}
