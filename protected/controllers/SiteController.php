<?php

class SiteController extends Controller{

    public function actionTest()
    {
        Yii::log("Bateu no test", 'info', 'system.controllers.SiteController');
    }

    public function actionIndex()
    {
        $this->redirect(array('pages/view&id=1'));
    }

    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    public function actionLogin()
    {
        $model = new LoginForm;

        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if ($model->validate() && $model->login() ) {
                $usuario = Usuario::model()->findByPk($model->username);
                $usuario->atualizarUltimoAcesso();

                // Parace que há muitos usuários sem e-mail no sistema, por algum motivo.
                if (empty($usuario->email)) {
                    $inscricao = Inscricao::model()->findByCpf($model->username);
                    if ($inscricao) {
                        $usuario->email = $inscricao->email;
                        $usuario->save();
                    }
                }

                $this->redirecionarParaOLocalAdequado($usuario);
            }
        }

        $this->render('login', array('model' => $model));
    }

    private function redirecionarParaOLocalAdequado($usuario)
    {
        if ($usuario->temPapel('Admin') || $usuario->temPapel('Secretaria')) {
            $this->redirect(Yii::app()->createUrl('admin/gerenciarInscricoes'));
        }

        // Verifica se o usuário preencheu todas as informações necessárias
        // (informações constantes no novo formulário de cadastro). Se não
        // preencheu, o força a preencher.
        if ($usuario->temPapel('InscritoComInformacoesFaltantes')) {
            $this->redirect(Yii::app()->createUrl('inscricao/informacoesComplementares'));
        }

        if ($usuario->temPapel('Colaborador')) {
            $this->redirect(Yii::app()->createUrl('colaborador'));
        }
        // if ($usuario->temPapel('Orientador') || $usuario->temPapel('Professor') || $usuario->temPapel('Tutor')) {
        //     $this->redirect(Yii::app()->createUrl('orientador'));
        // }

        // Aluno com múltiplas inscrições
        // Conforme discussão em e-mails de 18/12/2020, o multi-login não será permitido.
        // if ($this->usuarioTemMultiLoginENaoSelecionouInscricao($usuario)) {
        //     $this->redirect(Yii::app()->createUrl('multiLogin'));
        // }

        // Se chegou aqui, é um aluno comum com papel 'Inscrito'
        // Esta lista de CPFs foi uma requisição da secretaria em e-mail de 12/01/2021
        $cpfsQueDevemLogarNoUsuarioMaisRecente = [
            '06684965920',
            '30084679620',
            '36589462291',
            '43957197830',
            '32386857832',
        ];
        if (in_array($usuario->cpf, $cpfsQueDevemLogarNoUsuarioMaisRecente)) {
            $criteria = new CDbCriteria(['order' => 'id DESC']);
            $inscricao = Inscricao::model()->findByAttributes(['cpf' => $usuario->cpf], $criteria);
        } else {
            $inscricao = Inscricao::model()->findByAttributes(['cpf' => $usuario->cpf]);
        }
        Yii::app()->session['inscricao_id'] = $inscricao->id;
        $this->redirect(Yii::app()->createUrl('aluno'));
    }

    private function usuarioTemMultiLoginENaoSelecionouInscricao($usuario)
    {
        $inscricoes = Inscricao::model()->findAllByAttributes(['cpf' => $usuario->cpf]);
        return count($inscricoes) > 1 && empty(Yii::app()->session['inscricao_id']);
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionEsqueciSenha()
    {
        $houveErro = false;

        if (isset($_POST['solicitar'])) {
            $cpf = $_POST['cpf'];
            $dataNascimento = $_POST['data_nascimento'];

            if ($this->usuarioExiste($cpf, $dataNascimento)) {
                ResetarSenha::fazerSolicitacao($cpf);
                $this->redirect(Yii::app()->createUrl('site/login'));
            } else $houveErro = true;
        }

        $this->render('esqueciSenha', array(
            'houveErro' => $houveErro,
        ));
    }

    private function usuarioExiste($cpf, $dataNascimento)
    {
        if (!preg_match('/^\d\d\d\d\d\d\d\d\d\d\d$/', $cpf))
            return false;
        if (!preg_match('/^\d\d\/\d\d\/\d\d\d\d$/', $dataNascimento))
            return false;
        $inscricao = Inscricao::model()->findByCpf($cpf);
        if (empty($inscricao))
            return false;
        if ($inscricao->data_nascimento != $dataNascimento)
            return false;

        $usuario = Usuario::model()->findByPk($cpf);
        return !empty($usuario);
    }

    /**
     * Este método é chamado pelo servidor todos os dias de manhã por un cronjob. Coloque o disparo
     * de mensagens automatizadas aqui.
     */
    public function actionDispararMensagens()
    {
        // Lembrete de ofertas lecionadas no próximo mês

        // Valor default: '45, 15'
        $periodosString = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_PERIODOS);
        $periodosEmQueAMensagemDeveSerEnviada = $this->transformarStringDePeriodosEmArray($periodosString);

        $hoje = date('y-m-d');
        $ofertas = Oferta::model()->recuperarOfertasDoPresenteEFuturo();
        foreach ($ofertas as $oferta) {
            $dataInicioOferta = $oferta->recuperarDataInicio();
            $numeroDeDiasParaComecarOferta = CalendarioHelper::numeroDeDiasEntre($dataInicioOferta, $hoje);
            if ($hoje <= $dataInicioOferta && in_array($numeroDeDiasParaComecarOferta, $periodosEmQueAMensagemDeveSerEnviada, true)) {
                foreach ($oferta->docentes as $docente) {
                    Email::lembreteOfertaDocente($docente, $oferta);
                }
            }
        }

        // Lembrete de ofertas matriculadas no próximo mês

        // Valor default: '15'
        $periodosString = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_PERIODOS);
        $diasEmQueAMensagemDeveSerEnviada = $this->transformarStringDePeriodosEmArray($periodosString);

        $diaDeHoje = intval(date('d'));
        if (in_array($diaDeHoje, $diasEmQueAMensagemDeveSerEnviada, true)) {
            $inscricoes = $this->recuperarInscricoesEmofertasDoProximoMes();
            foreach ($inscricoes as $inscricao) {
                $listaComponentesHtml = $this->processarComponentes($inscricao['componentes']);
                Email::lembreteInscricoesProximoMesAluno($inscricao['nome'], $inscricao['email'], $listaComponentesHtml);
            }
        }

        // Resumo das ofertas do próximo semestre

        // Valor default: '05/01, 05/07'
        $periodosString = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_PERIODOS);
        $diasEmQueAMensagemDeveSerEnviada = explode(',', $periodosString);
        $diasEmQueAMensagemDeveSerEnviada = array_map(function($d) {
            return trim($d);
        }, $diasEmQueAMensagemDeveSerEnviada);

        $diaMesDeHoje = date('d/m');
        if (in_array($diaMesDeHoje, $diasEmQueAMensagemDeveSerEnviada, true)) {
            $inscricoes = Inscricao::model()->findAllByAttributes([
                'status' => Inscricao::STATUS_MATRICULADO,
                'status_aluno' => Inscricao::STATUS_ALUNO_ATIVO,
            ]);
            $ofertasHtml = $this->recuperarOfertasDoProximoSemestreHtml();
            foreach ($inscricoes as $inscricao) {
                Email::informeOfertasSemestralAluno($inscricao->nomeCompleto, $inscricao->email, $ofertasHtml);
            }
        }
    }

    private function transformarStringDePeriodosEmArray($periodosString)
    {
        $periodosArray = explode(',', $periodosString);
        return array_map(function($periodo) {
            return intval($periodo);
        }, $periodosArray);
    }

    private function recuperarInscricoesEmofertasDoProximoMes()
    {
        $statusMatriculado = Inscricao::STATUS_MATRICULADO;
        $statusAlunoAtivo = Inscricao::STATUS_ALUNO_ATIVO;
        [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();
        $mesesAtualMaisUm = $mes + $ano * 12 + 1;
        $sql = "
SELECT
    i.id, i.nome || ' ' || i.sobrenome AS nome, i.email,
    ARRAY_AGG('#' || c.nome || '#') AS componentes
FROM
    inscricao i
    JOIN inscricao_oferta io ON io.inscricao_id = i.id
    JOIN oferta o ON io.oferta_id = o.id
    JOIN componente_curricular c ON o.componente_curricular_id = c.id
WHERE
    o.ativo IS TRUE
    AND c.ativo IS TRUE
    AND i.ativo IS TRUE
    AND i.status = {$statusMatriculado}
    AND i.status_aluno = '{$statusAlunoAtivo}'
    AND o.mes + o.ano * 12 = {$mesesAtualMaisUm}
GROUP BY
    i.id
;
        ";
        return Yii::app()->db->createCommand($sql)->queryAll();
    }

    private function processarComponentes($componentesString)
    {
        $partes = explode('#', $componentesString);
        $html = '';
        foreach ($partes as $i => $parte) {
            if ($i % 2 == 1) {
                $html .= "<li>{$parte}</li>";
            }
        }
        return $html;
    }

    private function recuperarOfertasDoProximoSemestreHtml()
    {
        [ $mes, $ano ] = CalendarioHelper::mesEAnoAtuais();
        $mesesAtual = $mes + $ano * 12;
        $mesesAtualMaisSeis = $mesesAtual + 5;
        $sql = "
SELECT
    o.ano, o.mes, c.nome
FROM
    oferta o
    JOIN componente_curricular c ON o.componente_curricular_id = c.id
WHERE
    o.ativo IS TRUE
    AND c.ativo IS TRUE
    AND o.mes + o.ano * 12 >= {$mesesAtual}
    AND o.mes + o.ano * 12 <= {$mesesAtualMaisSeis}
ORDER BY
    o.ano, o.mes, c.nome
;
        ";
        $ofertas = Yii::app()->db->createCommand($sql)->queryAll();

        $html = '';
        foreach ($ofertas as $oferta) {
            $html .= "<li>{$oferta['nome']} {$oferta['mes']}/{$oferta['ano']}</li>";
        }
        return $html;
    }

}
