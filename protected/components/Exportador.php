<?php

class Exportador
{

    /**
     * Ponto único para a geração de arquivos de exportação (listas e planilhas)
     * em formatos XLS e CSV.
     * 
     * @param type $cabecalho
     * @param type $dados
     * @param type $filename
     * @param type $formato
     */
    public static function exportar($cabecalho, $dados, $filename, $formato)
    {
        self::verificarSeFormatoEhValido($formato);

        $classe = 'Exportador' . strtoupper($formato);
        $exportador = new $classe($cabecalho, $filename);

        if (is_string($dados)) {
            $exportador->recuperarDadosAPartirDeQuery($dados);
        } else if (is_array($dados)) {
            $exportador->atribuirDados($dados);
        }

        $exportador->transformacoesDeDados(array(
            'candidato_a_bolsa' => 'booleano',
            'recebe_bolsa' => 'booleano',
            'habilitacoes' => 'habilitacao',
            'data_matricula' => 'data',
            'data_nascimento' => 'data',
        ));

        $exportador->gerar();
    }

    private static function verificarSeFormatoEhValido($formato)
    {
        $formatosValidos = array('xls', 'csv');
        if (!in_array($formato, $formatosValidos)) {
            throw new Exception("Formato de arquivo de exportação inválido: {$formato}");
        }
    }

}
