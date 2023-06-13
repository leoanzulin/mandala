<?php

/**
 * Classe abstrata que os exportadores de arquivos devem estender.
 */
abstract class ExportadorDeArquivos
{

    protected $cabecalho;
    protected $dados;
    protected $nomeDoArquivo;
    protected $extensao;
    protected $transformacoesDeDados;

    public function __construct($cabecalho, $nomeDoArquivo, $extensao)
    {
        $this->cabecalho = $cabecalho;
        $this->dados = null;
        $this->nomeDoArquivo = $nomeDoArquivo;
        $this->extensao = $extensao;
        $this->transformacoesDeDados = null;
    }

    public function gerar()
    {
        $this->setup();
        $this->processar();
        $this->output();
        Yii::app()->end();
    }

    abstract protected function setup();

    /**
     * Realiza uma query diretamente no banco de dados para recuperar informações.
     * As colunas recuperadas devem corresponder às colunas passadas no cabeçalho.
     * 
     * @param string $sql 
     */
    public function recuperarDadosAPartirDeQuery($sql)
    {
        $this->dados = Yii::app()->db->createCommand($sql)->queryAll();
    }

    /**
     * $dados deve ser um array de arrays associativos onde cada registro tem
     * todas as colunas definidas no cabeçalho.
     * 
     * @param array $dados
     */
    public function atribuirDados($dados)
    {
        $this->dados = $dados;
    }

    /**
     * 
     *     [campo] => 'booleano',
     *     [campo] => [array]
     * 
     * @param type $transformacoes
     */
    public function transformacoesDeDados($transformacoes)
    {
        $this->transformacoesDeDados = $transformacoes;
    }

    protected function transformarDado($coluna, $valor)
    {
        if ($this->transformacoesDeDados && !empty($this->transformacoesDeDados[$coluna])) {
            switch ($this->transformacoesDeDados[$coluna]) {
                case 'booleano':
                    return $valor == 1 ? 'sim' : 'não';
                case 'habilitacao':
                    return $this->formatarArrayDeHabilitacoes($valor);
                case 'data':
                    return $this->formatarData($valor);
                default:
                    return $valor;
            }
        }
        return $valor;
    }

    private function formatarArrayDeHabilitacoes($stringHabilitacoes)
    {
        $habilitacoes = explode(',', substr($stringHabilitacoes, 1, -1));
        foreach ($habilitacoes as &$habilitacao) $habilitacao = substr($habilitacao, 1, -1);
        sort($habilitacoes);
        return implode(', ', $habilitacoes);
    }

    private function formatarData($data)
    {
        if (preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)$/', $data, $matches)) {
            return "{$matches[3]}/{$matches[2]}/{$matches[1]}";
        }
        return $data;
    }

    abstract protected function processar();

    /**
     * Método que faz a geração do arquivo.
     */
    abstract protected function output();

}
