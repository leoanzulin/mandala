<?php

function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function gerarCampoObservacoes($data)
{
    $input = '<input id="observacoes_' . $data->id . '" type="text" name="observacoes[' . $data->cpf . ']" value="' . $data->observacoes . '"';
    if (!$data->candidato_a_bolsa) {
        $input .= ' disabled';
    }
    $input .= '>';
    return $input;
}

function gerarTransformarMatricula($data)
{
    $input = '<a href="' . Yii::app()->createUrl('admin/matricular', array('cpf' => $data->cpf)) . '" onClick="return confirm(\'Tem certeza que deseja matricular o aluno ' . $data->nome . ' (CPF ' . $data->cpf . ')?\')">Matricular</a>';
    return $input;
}

?>

<h1>Visualizar inscritos</h1>
<br>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'inscritos-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'visualizar-matriculas-grid',
    'dataProvider' => $model->searchInscritos(),
    'filter'=>$model,
    'columns' => array(
        'cpf',
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
        ),
        array(
            'headerTemplate' => 'Irá receber bolsa?',
            'name' => 'recebe_bolsa',
            'id' => 'recebe_bolsa',
            'value' => '$data->cpf',
            'class' => 'CCheckBoxColumn',
            'selectableRows' => '10000',
            'checked' => '$data->recebe_bolsa == "sim"',
            'disabled' => '$data->candidato_a_bolsa == "nao"',
            'htmlOptions' => array(
                'style' => 'width: 80px; text-align: center',
            ),
        ),
        array(
            'header' => 'Observações',
            'value' => 'gerarCampoObservacoes($data)',
            'type' => 'raw',
        ), 
        array(
            'class' => 'CButtonColumn',
            'template' => '{view}',
        ),
        array(
            'header' => '',
            'type' => 'raw',
            'value' => 'gerarTransformarMatricula($data)',
        )
    ),
    'rowCssClassExpression' => 'classeLinha($row, $data)',
));

?>

    <?php echo CHtml::submitButton('Salvar alterações', array(
        'name' => 'Salvar',
    )); ?>

<?php $this->endWidget(); ?>