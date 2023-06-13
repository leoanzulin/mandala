<?php

class ConfiguracoesForm extends CFormModel
{

    public $mesInicio;
    public $anoInicio;
    public $mesFim;
    public $anoFim;
    public $diaAberturaConfirmacao;
    public $diaFechamentoConfirmacao;
    public $novaSenha;
    public $novaSenhaConfirmacao;

    public $mensagemLembreteDocentePeriodos;
    public $mensagemLembreteDocenteAssunto;
    public $mensagemLembreteDocenteCorpo;
    public $mensagemLembreteAlunoProximoMesPeriodos;
    public $mensagemLembreteAlunoProximoMesAssunto;
    public $mensagemLembreteAlunoProximoMesCorpo;
    public $mensagemLembreteAlunoProximoSemestrePeriodos;
    public $mensagemLembreteAlunoProximoSemestreAssunto;
    public $mensagemLembreteAlunoProximoSemestreCorpo;

    public function rules()
    {
        return array(
            // array('mesInicio, anoInicio, mesFim, anoFim, diaAberturaConfirmacao, diaFechamentoConfirmacao', 'required'),
            // array('diaAberturaConfirmacao, diaFechamentoConfirmacao', 'ehDia'),
            // array('mesInicio, mesFim', 'ehMes'),
            // array('anoInicio, anoFim', 'ehAno'),
            array('novaSenha, novaSenhaConfirmacao,
                mensagemLembreteDocentePeriodos, mensagemLembreteDocenteAssunto, mensagemLembreteDocenteCorpo,
                mensagemLembreteAlunoProximoMesPeriodos, mensagemLembreteAlunoProximoMesAssunto, mensagemLembreteAlunoProximoMesCorpo,
                mensagemLembreteAlunoProximoSemestrePeriodos, mensagemLembreteAlunoProximoSemestreAssunto, mensagemLembreteAlunoProximoSemestreCorpo',
                'safe'
            ),
        );
    }

    public function attributeLabels()
    {
        return array(
            'inicioPeriodo' => 'Início do período ofertado',
            'fimPeriodo' => 'Fim do período ofertado',
            'diaAberturaConfirmacao' => 'Dia de abertura',
            'diaFechamentoConfirmacao' => 'Dia de fechamento',
            'novaSenha' => 'Nova senha',
            'novaSenhaConfirmacao' => 'Confirme a senha',

            'mensagemLembreteDocentePeriodos' => 'Período',
            'mensagemLembreteDocenteAssunto' => 'Assunto',
            'mensagemLembreteDocenteCorpo' => 'Mensagem',
            'mensagemLembreteAlunoProximoMesPeriodos' => 'Período',
            'mensagemLembreteAlunoProximoMesAssunto' => 'Assunto',
            'mensagemLembreteAlunoProximoMesCorpo' => 'Mensagem',
            'mensagemLembreteAlunoProximoSemestrePeriodos' => 'Período',
            'mensagemLembreteAlunoProximoSemestreAssunto' => 'Assunto',
            'mensagemLembreteAlunoProximoSemestreCorpo' => 'Mensagem',
        );
    }

    public function ehDia($atributo, $params)
    {
        $dia = $this->$atributo;
        if (!preg_match('/^\d\d?$/', $dia) || $dia < 1 || $dia > 31) {
            $this->addError($atributo, 'Dia inválido');
        }
    }

    public function ehMes($atributo, $params)
    {
        $mes = $this->$atributo;
        if (!preg_match('/^\d\d?$/', $mes) || $mes < 1 || $mes > 12) {
            $this->addError($atributo, 'Mẽs inválido');
        }
    }

    public function ehAno($atributo, $params)
    {
        if (!preg_match('/^\d\d\d\d$/', $this->$atributo)) {
            $this->addError($atributo, 'O ano deve ser informado com 4 dígitos');
        }
    }

    /**
     * Inicializa os atributos de configuração com os valores presentes no banco.
     */
    protected function afterConstruct()
    {
        parent::afterConstruct();

        $this->mensagemLembreteDocentePeriodos = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_PERIODOS);
        $this->mensagemLembreteDocenteAssunto = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_ASSUNTO);
        $this->mensagemLembreteDocenteCorpo = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_CORPO);
        $this->mensagemLembreteAlunoProximoMesPeriodos = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_PERIODOS);
        $this->mensagemLembreteAlunoProximoMesAssunto = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_ASSUNTO);
        $this->mensagemLembreteAlunoProximoMesCorpo = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_CORPO);
        $this->mensagemLembreteAlunoProximoSemestrePeriodos = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_PERIODOS);
        $this->mensagemLembreteAlunoProximoSemestreAssunto = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_ASSUNTO);
        $this->mensagemLembreteAlunoProximoSemestreCorpo = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_CORPO);
    }

    public function salvar()
    {
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_PERIODOS, $this->mensagemLembreteDocentePeriodos);
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_ASSUNTO, $this->mensagemLembreteDocenteAssunto);
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_CORPO, $this->mensagemLembreteDocenteCorpo);
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_PERIODOS, $this->mensagemLembreteAlunoProximoMesPeriodos);
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_ASSUNTO, $this->mensagemLembreteAlunoProximoMesAssunto);
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_CORPO, $this->mensagemLembreteAlunoProximoMesCorpo);
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_PERIODOS, $this->mensagemLembreteAlunoProximoSemestrePeriodos);
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_ASSUNTO, $this->mensagemLembreteAlunoProximoSemestreAssunto);
        Configuracao::salvarPropriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_CORPO, $this->mensagemLembreteAlunoProximoSemestreCorpo);

        $this->trocarSenhaSeEstiverOk();
    }

    private function trocarSenhaSeEstiverOk()
    {
        if ($this->senhasEstaoOk()) {
            $usuario = Usuario::model()->findByPk('admin');
            $usuario->trocarSenha($this->novaSenha);
        }
    }

    private function senhasEstaoOk()
    {
        $senha = $this->novaSenha;
        $confirmarSenha = $this->novaSenhaConfirmacao;

        // Senha vazia
        if (empty($senha) || empty($confirmarSenha))
            return false;
        // Senha contém espaços
        if ($senha != trim($senha) || $confirmarSenha != trim($confirmarSenha))
            return false;
        if ($senha !== $confirmarSenha)
            return false;

        return true;
    }

}
