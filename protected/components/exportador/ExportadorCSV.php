<?php

/**
 * Gera arquivos CSV.
 */
class ExportadorCSV extends ExportadorDeArquivos
{

    // Configurações padrão do CSV
    private $csv_terminated = "\r\n";
    private $csv_separator = ",";
    private $csv_enclosed = '"';
    private $csv_escaped = "\\";
    //
    private $quantidadeDeCampos = 0;
    private $saida = '';

    public function __construct($cabecalho, $nomeDoArquivo, $extensao = 'csv')
    {
        parent::__construct($cabecalho, $nomeDoArquivo, $extensao);
    }

    protected function setup()
    {
        // Nenhum setup é necessário
    }

    protected function processar()
    {
        $this->processarCabecalho();

        foreach ($this->dados as $registro) {
            $linha = '';
            $i = 0;

            foreach ($this->cabecalho as $coluna) {
                $valor = $this->transformarDado($coluna, $registro[$coluna]);

                // Previne que os zeros à esquerda sejam removidos
                // https://superuser.com/questions/568429/excel-csv-import-treating-quoted-strings-of-numbers-as-numeric-values-not-strin
                // Atualização 06/02/2021: Glauber vai subir arquivos direto para o sistema da UFSCar, não precisa disto
                // if ($coluna === 'cpf') {
                //     $linha .= '="' . $valor . '"';
                if ($valor == '1' || $valor != '') {
                    if ($this->csv_enclosed == '') {
                        $linha .= $valor;
                    } else {
                        $linha .= $this->csv_enclosed . str_replace($this->csv_enclosed, $this->csv_escaped . $this->csv_enclosed, $valor) . $this->csv_enclosed;
                    }
                }

                if ($i < $this->quantidadeDeCampos - 1) {
                    $linha .= $this->csv_separator;
                }
                $i++;
            }

            $this->saida .= $linha;
            $this->saida .= $this->csv_terminated;
        }
    }

    private function processarCabecalho()
    {
        if (!is_array($this->cabecalho)) {
            $this->cabecalho = explode(',', $this->cabecalho);
        }

        $this->quantidadeDeCampos = count($this->cabecalho);
        $this->saida = implode(',', $this->cabecalho) . $this->csv_terminated;
    }

    protected function output()
    {
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($this->saida));
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=\"{$this->nomeDoArquivo}.{$this->extensao}\"");
        // https://stackoverflow.com/questions/2223882/whats-the-difference-between-utf-8-and-utf-8-without-bom
        // Adiciona UTF-8 BOM para o Excel reconhecer o arquivo como UTF-8
        echo "\xEF\xBB\xBF";
        echo $this->saida;
    }

}
