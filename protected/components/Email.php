<?php

/**
 * Classe reponsável por enviar e-mails.
 * 
 * Os templates de e-mails encontram-se na pasta
 * 
 *     protected/components/templates_de_emails
 * 
 * em formato html. Utilize placeholders no formato "{placeholder}" para
 * preencher os valores variáveis quando for enviar um e-mail.
 */
class Email
{

    /*
      Formato do cabeçalho:
      From: Coordenação EDUTEC <noreply@sead-02.sead.ufscar.br>\r\n
      Reply-To: Coordenação EDUTEC <edutec@ead.ufscar.br>\r\n
      Cc: Coordenação EDUTEC <edutec@ead.ufscar.br>\r\n
      MIME-Version: 1.0\r\n
      Content-Type: text/html; charset=UTF-8\r\n
     */
    private static $headers = array(
        'from' => 'Coordenação EDUTEC <noreply@sead-02.sead.ufscar.br>',
        'reply-to' => 'Coordenação EDUTEC <edutec@ead.ufscar.br>',
        'cc' => 'Coordenação EDUTEC <edutec@ead.ufscar.br>',
        'final' => "MIME-Version: 1.0\r\nContent-Type: text/html; charset=UTF-8\r\n",
    );

    private static function buildHeader()
    {
        return 'From: ' . self::$headers['from'] . "\r\n" .
                'Reply-To: ' . self::$headers['reply-to'] . "\r\n" .
                'Cc: ' . self::$headers['cc'] . "\r\n" .
                self::$headers['final'];
    }

    private static function buildHeaderSemCc()
    {
        return 'From: ' . self::$headers['from'] . "\r\n" .
                'Reply-To: ' . self::$headers['reply-to'] . "\r\n" .
                self::$headers['final'];
    }

    private static function incluirTemplate($arquivo, $placeholders, $valores)
    {
        $template = '';
        ob_start();
        include 'templates_de_emails/' . $arquivo;
        $template = ob_get_contents();
        ob_end_clean();
        return str_replace($placeholders, $valores, $template);
    }

    private static function incluirTemplateBanco($chave, $placeholders, $valores)
    {
        $template = '<!DOCTYPE html>
<html>
<head>
    <title>Educação e tecnologias</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>';
        $template .= Configuracao::propriedade($chave);
        $template .= '</body></html>';
        return str_replace($placeholders, $valores, $template);
    }

    public static function mensagemPreInscricao($nome, $email, $cpf, $senha)
    {
        Yii::log("Enviando e-mail para {$nome} ({$email}) sobre inscrição", 'info', 'system.components.Email');
        $to = "{$nome} <{$email}>\r\n";
        $subject = "[Educação e Tecnologias] Confirmação de recebimento de formulário de inscrição";
        $message = self::incluirTemplate('inscricao.html',
                array('{nome}', '{cpf}', '{senha}'),
                array($nome, $cpf, $senha)
        );
        mail(trim($email), $subject, $message, self::buildHeader());
    }

    public static function mensagemInternaUsuarioSeInscreveu($nome, $email, $cpf)
    {
        Yii::log("Enviando e-mail para edutec@ead.ufscar.br sobre inscrição de {$nome} - {$cpf}", 'info', 'system.components.Email');
        $to = "edutec@ead.ufscar.br";
        $subject = "[Educação e Tecnologias] Usuário {$cpf} se inscreveu no sistema";
        $message = self::incluirTemplate('interna_inscricao_realizada.html',
                array('{nome}', '{cpf}'),
                array($nome, $cpf)
        );
        mail($to, $subject, $message, self::buildHeader());
    }

    public static function mensagemPreInscricaoSemSenha($nome, $email)
    {
        Yii::log("Enviando e-mail para {$nome} ({$email}) sobre inscrição", 'info', 'system.components.Email');
        $to = "{$nome} <{$email}>\r\n";
        $subject = "[Educação e Tecnologias] Confirmação de recebimento de formulário de inscrição";
        $message = self::incluirTemplate('inscricao_sem_senha.html', ['{nome}'], [$nome]);
        mail(trim($email), $subject, $message, self::buildHeader());
    }

    public static function mensagemResetarEmail($email, $link)
    {
        Yii::log("Enviando e-mail para {$email} sobre troca de senha", 'info', 'system.components.Email');
        $to = "{$email}\r\n";
        $subject = "[Educação e Tecnologias] Solicitação de troca de senha";
        $message = self::incluirTemplate('resetar_email.html', ['{link}'], [$link]);
        mail(trim($email), $subject, $message, self::buildHeader());
    }

    /**
     * Mensagem enviada para os primeiros pré-inscritos que se cadastraram e
     * não haviam preenchido todas as informaçẽos necessárias.
     */
    public static function mensagemUsuarioCriado($nome, $email, $cpf, $senha)
    {
        Yii::log("Enviando e-mail para {$nome} ({$email} - {$cpf}) sobre criação de usuário", 'info', 'system.components.Email');
        $to = "{$nome} <{$email}>\r\n";
        $subject = "[Educação e Tecnologias] Usuário criado";
        $message = self::incluirTemplate('usuario_criado.html',
                array('{nome}', '{cpf}', '{senha}'),
                array($nome, $cpf, $senha)
        );
        mail(trim($email), $subject, $message, self::buildHeader());
    }

    /**
     * E-mail interno enviado para a coordenação informando que um usuário
     * enviou os seus documentos.
     */
    public static function mensagemInternaUsuarioEnviouDocumentos($nome, $email, $cpf)
    {
        Yii::log("Enviando e-mail para edutec@ead.ufscar.br sobre envio de documentos de {$nome} - {$cpf}", 'info', 'system.components.Email');
        $to = "edutec@ead.ufscar.br\r\n";
        $subject = "[Educação e Tecnologias] Usuário {$cpf} enviou documentos";
        $message = self::incluirTemplate('interna_enviou_documentos.html',
                array('{nome}', '{cpf}'),
                array($nome, $cpf)
        );
        mail('edutec@ead.ufscar.br', $subject, $message, self::buildHeader());
    }

    public static function mensagemLinkParaPagamento($nome, $email)
    {
        Yii::log("Enviando e-mail para {$nome} ({$email}) sobre pagamento de matrícula", 'info', 'system.components.Email');
        $to = "{$nome} <{$email}>\r\n";
        $subject = "[Educação e Tecnologias] Último passo para se inscrever no curso";
        $message = self::incluirTemplate('link_pagamento_matricula.html', array('{nome}'), array($nome));
        mail($to, $subject, $message, self::buildHeader());
    }

    public static function emailConfirmacaoInscricaoEmComponentes($aluno, $email)
    {
        Yii::log("Enviando e-mail para {$aluno} ({$email}) sobre confirmação de inscrição em componentes", 'info', 'system.components.Email');
        $to = "{$aluno} <{$email}>\r\n";
        $subject = "[Educação e Tecnologias] Confirmação de inscrição em componentes";
        $message = self::incluirTemplate('confirmar_inscricoes_componentes.html', ['{aluno}'], [$aluno]);
        mail(trim($email), $subject, $message, self::buildHeader());
    }

    public static function lembreteOfertaDocente($docente, $oferta)
    {
        Yii::log("Enviando e-mail de lembrete para {$docente} ({$docente->email}) sobre oferta que irá lecionar no próximo mês", 'info', 'system.components.Email');
        $to = "{$docente->nomeCompleto} <{$docente->email}>\r\n";
        // $subject = "[Educação e Tecnologias] Lembrete de oferta de componente no próximo mês";
        // $message = self::incluirTemplate('lembrete_oferta_docente.html', ['{docente}', '{oferta}'], [$docente->nomeCompleto, $oferta->recuperarNome()]);
        $subject = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_ASSUNTO);
        $message = self::incluirTemplateBanco(Configuracao::MENSAGEM_LEMBRETE_DOCENTE_CORPO, ['{docente}', '{oferta}'], [$docente->nomeCompleto, $oferta->recuperarNome()]);
        mail($to, $subject, $message, self::buildHeader());
    }

    public static function lembreteInscricoesProximoMesAluno($nome, $email, $componentes)
    {
        Yii::log("Enviando e-mail de lembrete para {$nome} ({$email}) sobre inscrições em ofertas no próximo mês", 'info', 'system.components.Email');
        $to = "{$nome} <{$email}>\r\n";
        // $subject = "[Educação e Tecnologias] Lembrete de inscrições feitas em componentes no próximo mês";
        // $message = self::incluirTemplate('lembrete_ofertas_aluno.html', ['{aluno}', '{componentes}'], [$nome, $componentes]);
        $subject = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_ASSUNTO);
        $message = self::incluirTemplateBanco(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_MES_CORPO, ['{aluno}', '{componentes}'], [$nome, $componentes]);
        mail($to, $subject, $message, self::buildHeader());
    }

    public static function informeOfertasSemestralAluno($nome, $email, $componentes)
    {
        Yii::log("Enviando e-mail de informe semestral para {$nome} ({$email}) sobre ofertas deste semestre", 'info', 'system.components.Email');
        $to = "{$nome} <{$email}>\r\n";
        // $subject = "[Educação e Tecnologias] Informe de ofertas de componentes deste semestre";
        // $message = self::incluirTemplate('informe_ofertas_semestral_aluno.html', ['{aluno}', '{componentes}'], [$nome, $componentes]);
        $subject = Configuracao::propriedade(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_ASSUNTO);
        $message = self::incluirTemplateBanco(Configuracao::MENSAGEM_LEMBRETE_ALUNO_PROXIMO_SEMESTRE_CORPO, ['{aluno}', '{componentes}'], [$nome, $componentes]);
        mail($to, $subject, $message, self::buildHeader());
    }

}
