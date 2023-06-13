<?php

/**
 * Gera arquivos XLS.
 */
class ExportadorXLS extends ExportadorDeArquivos
{

    private $_objPHPExcel;

    public function __construct($cabecalho, $nomeDoArquivo, $extensao = 'xls')
    {
        parent::__construct($cabecalho, $nomeDoArquivo, $extensao);
    }

    protected function setup()
    {
        Yii::import('ext.phpexcel.XPHPExcel');
        $this->_objPHPExcel = XPHPExcel::createPHPExcel();
        $this->_objPHPExcel->getProperties()->setCreator("SEad / UFSCar");
        $this->_objPHPExcel->setActiveSheetIndex(0);
    }

    protected function processar()
    {
        $letra = 'A';
        foreach ($this->cabecalho as $coluna) {
            $this->_objPHPExcel->getActiveSheet()->getColumnDimension($letra)->setAutoSize(true);
            $this->_objPHPExcel->getActiveSheet()->setCellValue($letra . '1', $coluna);
            $this->_objPHPExcel->getActiveSheet()->getStyle($letra . '1')->getFont()->setBold(true);
            $letra++;
        }

        $i = 2;
        foreach ($this->dados as $registro) {
            $letra = 'A';
            foreach ($this->cabecalho as $coluna) {
                $valor = $this->transformarDado($coluna, $registro[$coluna]);
                // TODO: MELHORAR ISTO DEPOIS
                $cellDataType = in_array($coluna, ['valor', 'serviços (valor)', 'valor_total', 'valor_pago', 'sobra']) ? PHPExcel_Cell_DataType::TYPE_NUMERIC : PHPExcel_Cell_DataType::TYPE_STRING;
                $this->_objPHPExcel->getActiveSheet()->setCellValueExplicit($letra . $i, $valor, $cellDataType);
                $letra++;
            }
            $i++;
        }
    }

    protected function output()
    {
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"{$this->nomeDoArquivo}.{$this->extensao}\"");
        header("Cache-Control: max-age=0");
        $exceldoc = PHPExcel_IOFactory::createWriter($this->_objPHPExcel, 'Excel5');
        $exceldoc->save('php://output');
    }

}
