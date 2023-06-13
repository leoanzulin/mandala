<?php

/**
 * Classe base para gerar PDFs de qualquer tipo. Sua funcionalidade básica é
 * carregar um modelo HTML e gerar um arquivo PDF a partir dele.
 */
class GeradorDePdf
{

    // Armazena o documento HTML que corresponderá ao PDF gerado
    protected $html = '';
    // Folha de estilo aplicada ao HTML
    protected $css = '';
    // Armazena os elementos HTML que compõem o cabeçalho e rodapé
    protected $header = '';
    protected $footer = '';
    // URL base dos arquivos a serem lidos
    protected $urlBase = '';
    // Título do documento
    protected $titulo = '';
    // Parâmetros adicionais para a geração do documento
    protected $parametros = [];
    private $mpdf;

    protected function definirParametros($parametros)
    {
        $this->parametros = $parametros;
    }

    protected function definirTitulo($titulo = 'declaracao.pdf')
    {
        $this->titulo = $titulo;
    }

    protected function definirUrlBase($urlBase)
    {
        $this->urlBase = $urlBase;
    }

    // TODO: Verificar se o arquivo existe
    protected function carregarArquivo($arquivo, $caminhoAbsoluto = false)
    {
        $caminho = $caminhoAbsoluto ?
            $arquivo :
            __DIR__ . "{$this->urlBase}{$arquivo}";
        Yii::trace("GeradorDePdf: Carregando arquivo {$caminho}");

        ob_start();
        require $caminho;
        $conteudo = ob_get_contents();
        ob_end_clean();
        return $conteudo;
    }

    protected function carregarModelo($arquivo, $caminhoAbsoluto = false)
    {
        $this->html = $this->carregarArquivo($arquivo, $caminhoAbsoluto);
    }

    protected function carregarCabecalho($arquivo, $caminhoAbsoluto = false)
    {
        $this->header = $this->carregarArquivo($arquivo, $caminhoAbsoluto);
    }

    protected function definirRodape($footer = '{PAGENO}')
    {
        $this->footer = $footer;
    }

    protected function carregarRodape($arquivo, $caminhoAbsoluto = false)
    {
        $this->footer = $this->carregarArquivo($arquivo, $caminhoAbsoluto);
    }

    protected function carregarFolhaDeEstilo($arquivo, $caminhoAbsoluto = false)
    {
        $this->css = $this->carregarArquivo($arquivo, $caminhoAbsoluto);
    }

    protected function processarUrlBase()
    {
        $url = __DIR__ . $this->urlBase;
        $this->header = str_replace('{URL_BASE}', $url, $this->header);
        $this->footer = str_replace('{URL_BASE}', $url, $this->footer);
        $this->html = str_replace('{URL_BASE}', $url, $this->html);

        $urlAbsoluta = Yii::app()->getBaseUrl();
        $this->header = str_replace('{URL_BASE_ABSOLUTA}', $urlAbsoluta, $this->header);
        $this->footer = str_replace('{URL_BASE_ABSOLUTA}', $urlAbsoluta, $this->footer);
        $this->html = str_replace('{URL_BASE_ABSOLUTA}', $urlAbsoluta, $this->html);
    }

    private function gerarPdf()
    {
        require_once Yii::app()->getBasePath() . '/vendor/autoload.php';

        $this->adicionarFonteCorbelAosParametros();

        $this->mpdf = new \Mpdf\Mpdf($this->parametros);

        $this->mpdf->setAutoTopMargin = 'pad';
        $this->mpdf->allow_charset_conversion = true;
        $this->mpdf->charset_in = 'UTF-8';
        $this->mpdf->showImageErrors = true;

        $this->mpdf->SetTitle($this->titulo);
        $this->mpdf->SetHeader($this->header);
        $this->mpdf->SetFooter($this->footer);

        $this->mpdf->WriteHtml($this->css, 1);
        $this->mpdf->WriteHTML($this->html);
    }

    private function adicionarFonteCorbelAosParametros()
    {
        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        $this->parametros['fontDir'] = array_merge($fontDirs, [
            __DIR__ . '/../../fonts',
        ]);
        $this->parametros['fontdata'] = $fontData + [
            'corbel' => [
                'R' => 'corbel.ttf',
                'B' => 'corbelb.ttf',
            ],
        ];
    }

    protected function apresentarPdf($nomeArquivo)
    {
        $this->gerarPdf();
        $this->apresentarNoNavegador($nomeArquivo);
    }

    protected function salvarNoServidorEApresentarPdf($nomeArquivo, $caminhoCompleto)
    {
        $this->gerarPdf();
        $this->salvarEmDisco($caminhoCompleto);
        $this->apresentarNoNavegador($nomeArquivo);
    }

    private function salvarEmDisco($caminhoCompleto)
    {
        $this->mpdf->Output($caminhoCompleto, 'F');
    }

    private function apresentarNoNavegador($nomeArquivo)
    {
        $this->mpdf->Output($nomeArquivo, 'I');
    }

}
