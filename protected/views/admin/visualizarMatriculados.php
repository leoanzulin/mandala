<?php

function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function gerarStatus($data)
{
    $statusPossiveis = array('Alterar status', 'Ativo', 'Inscrito', 'Desistente', 'Trancado', 'Cancelado', 'Formado');

    foreach ($statusPossiveis as $status) {
        $selected[$status] = '';
    }

    if (!empty($data->status_aluno)) {
        $selected[$data->status_aluno] = 'selected';
    }

    $select = "<select name=\"status_aluno[{$data->id}]\" id=\"status_aluno_{$data->id}\" style=\"width:120px;\">";
    $select .= "<option value=\"\">Selecione um status</option>";
    foreach ($statusPossiveis as $status) {
        $select .= "<option value=\"{$status}\" {$selected[$status]}>{$status}</option>";
    }
    $select .= '</select>';

    return $select;
}

function linkVisualizarInscricoes($data) {
    return "<a href=\"" . Yii::app()->createUrl('admin/visualizarInscricoes', array('id' => $data->id)) . "\">" . $data->cpf . "</a><br>"
            . "<a href=\"" . Yii::app()->createUrl('admin/editarInscricoesInscricao', array('id' => $data->id)) . "\">(editar inscrições)</a>"
            . "<a href=\"" . Yii::app()->createUrl('admin/visualizarHistorico', array('id' => $data->id)) . "\">(histórico)</a>"
        ;
}

function linkEditar($data) {
    return array('editarInscricao', 'id' => $data->id);
}

function linkEditarInscricoes($data) {
    return array('editarInscricoesInscricao', 'id' => $data->id);
}

?>

<h1>Visualizar alunos matriculados</h1>
<br>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'matriculados-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'visualizar-matriculas-grid',
    'dataProvider' => $model->searchMatriculados(),
    'filter'=>$model,
    'columns' => array(
        array(
            'header' => 'CPF',
            'name' => 'cpf_search',
            'type' => 'html',
            'value' => 'linkVisualizarInscricoes($data)',
        ),
        'nome',
        'sobrenome',
        'email',
        array(
            'header' => 'Documentos',
            'class' => 'CButtonColumn',
            'template' => '{cpf} {rg} {diploma} {comprovante_residencia} {curriculo} {justificativa}',
            'htmlOptions' => array(
                'style' => 'width: 120px',
            ),
            'buttons' => array(
                'cpf' => array(
                    'label' => 'CPF',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("cpf", true)',
                    'visible' => '$data->recuperarArquivoDocumento("cpf") != ""',
                ),
                'rg' => array(
                    'label' => 'RG',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("rg", true)',
                    'visible' => '$data->recuperarArquivoDocumento("cpf") != ""',
                ),
                'diploma' => array(
                    'label' => 'Diploma',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("diploma", true)',
                    'visible' => '$data->recuperarArquivoDocumento("cpf") != ""',
                ),
                'comprovante_residencia' => array(
                    'label' => 'Comprovante de residência',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("comprovante_residencia", true)',
                    'visible' => '$data->recuperarArquivoDocumento("cpf") != ""',
                ),
                'curriculo' => array(
                    'label' => 'Currículo',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("curriculo", true)',
                    'visible' => '$data->recuperarArquivoDocumento("cpf") != ""',
                ),
                'justificativa' => array(
                    'label' => 'Justificativa de próprio punho',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("justificativa", true)',
                    'visible' => '$data->recuperarArquivoDocumento("cpf") != ""',
                ),
            )
        ),
        array(
            'name' => 'candidato_a_bolsa_search',
            'value' => '$data->candidato_a_bolsa == "sim" ? "Sim" : "Não"',
            'htmlOptions' => array(
                'style' => 'width: 80px; text-align: center',
            ),
        ),
        array(
            'name' => 'recebe_bolsa_search',
            'value' => '$data->recebe_bolsa == "sim" ? "Sim" : "Não"',
            'htmlOptions' => array(
                'style' => 'width: 80px; text-align: center',
            ),
        ),
        'observacoes',
        'turma',
        array(
            'header' => 'Status',
            'value' => 'gerarStatus($data)',
            'type' => 'raw',   
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{view}{update}',
            'updateButtonUrl' => 'linkEditar($data)',
        ),
    ),
    'rowCssClassExpression' => 'classeLinha($row, $data)',
));

?>

<?php echo CHtml::submitButton('Salvar alterações'); ?>

<?php $this->endWidget(); ?>
