<?php

function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function linkVisualizarInscricoes($data) {
    return "<a href=\"" . Yii::app()->createUrl('admin/visualizarInscricoes', array('id' => $data->id)) . "\">" . $data->cpf . "</a><br>";
}

function statusTcc($data) {
    $html = '<ul style="text-align: left;">';
    foreach ($data->tccs as $tcc) {
        $url = Yii::app()->createUrl('admin/editarTcc', array('id' => $tcc->id));
        $html .= "<li><a href=\"{$url}\">{$tcc->titulo}</a></li>";
    }
    $urlIcone = Yii::app()->request->baseUrl . '/images/mais.png';
    $url = Yii::app()->createUrl('admin/cadastrarTcc', [ 'id' => $data->id ]);
    $html .= '</ul>';
    $html .= "<a href=\"{$url}\"><img src=\"{$urlIcone}\" style=\"width: 20px;\" title=\"Cadastrar TCC\"></a>";
    return $html;
}

function certificados($data) {
    $tipo = '';
    switch ($data->tipo_curso) {
        case Inscricao::TIPO_CURSO_EXTENSAO:
            $tipo = 'extensao';
            break;
        case Inscricao:: TIPO_CURSO_APERFEICOAMENTO:
            $tipo = 'aperfeicoamento';
            break;
        case Inscricao::TIPO_CURSO_ESPECIALIZACAO:
            $tipo = 'especializacao';
            break;
    }
    $parametros = [ 'inscricaoId' => $data->id, 'tipo' => $tipo ];

    $urlIcone = Yii::app()->request->baseUrl . '/images/documento.png';
    $urlIconeTxt = Yii::app()->request->baseUrl . '/images/documento_txt.png';
    $urlIconeXls = Yii::app()->request->baseUrl . '/images/documento_xls.png';

    $html = '';
    if ($data->tipo_curso == Inscricao::TIPO_CURSO_ESPECIALIZACAO) {
        foreach ($data->habilitacoes as $habilitacao) {
            $parametros['habilitacaoId'] = $habilitacao->id;
            $url = Yii::app()->createUrl('admin/gerarCertificado', $parametros);
            $html .= "<a href=\"{$url}\" target=\"_blank\" title=\"Certificado para habilitacao {$habilitacao->nome}\"><img src=\"{$urlIcone}\"></a>";
            $parametros['formatoTexto'] = true;
            $url = Yii::app()->createUrl('admin/gerarCertificado', $parametros);
            // $html .= "<a href=\"{$url}\" target=\"_blank\" title=\"Certificado para habilitacao {$habilitacao->nome} em formato XLS\"><img src=\"{$urlIconeXls}\"></a>";
            $html .= "<a href=\"{$url}\" target=\"_blank\" title=\"Certificado para habilitacao {$habilitacao->nome} em formato TXT\"><img src=\"{$urlIconeTxt}\"></a>";
            $parametros['formatoTexto'] = false;
        }
    } else {
        $url = Yii::app()->createUrl('admin/gerarCertificado', $parametros);
        $html .= "<a href=\"{$url}\" target=\"_blank\" title=\"Certificado\"><img src=\"{$urlIcone}\"></a>";
        $parametros['formatoTexto'] = true;
        $url = Yii::app()->createUrl('admin/gerarCertificado', $parametros);
        $html .= "<a href=\"{$url}\" target=\"_blank\" title=\"Certificado em formato TXT\"><img src=\"{$urlIconeTxt}\"></a>";
        // $html .= "<a href=\"{$url}\" target=\"_blank\" title=\"Certificado em formato XLS\"><img src=\"{$urlIconeXls}\"></a>";
    }

    return $html;
}

?>

<h1>Emitir certificados</h1>
<br>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'tccs-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'visualizar-matriculas-grid',
    'dataProvider' => $model->searchMatriculados(),
    'filter' => $model,
    'columns' => array(
        array(
            'header' => 'CPF',
            'name' => 'cpf_search',
            'type' => 'html',
            'value' => 'linkVisualizarInscricoes($data)',
        ),
        'nome',
        'sobrenome',
        array(
            'header' => 'Certificados',
            'type' => 'raw',
            'value' => 'certificados($data)',
        ),
    ),
    'rowCssClassExpression' => 'classeLinha($row, $data)',
));

?>

<?php $this->endWidget(); ?>
