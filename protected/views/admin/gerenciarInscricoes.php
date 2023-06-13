<?php

function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function urlEditar($data) {
    return array('editarInscricao', 'id' => $data->id);
}

?>

<h1>Gerenciar inscrições</h1>
<br>

<p><b>Importante:</b></p>
<ul>
    <li>Depois que as caixas são selecionadas e salvas, não podem ser deselecionadas</li>
    <li><b>Se quiser filtrar inscrições por status, siga estes códigos:</b>
        <ul>
            <li>1: Inscrição feita</li>
            <li>2: Documentos validados</li>
            <li>3: Matrícula paga</li>
        </ul>
    </li>
</ul>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'inscricoes-form',
	'enableAjaxValidation'=>false,
)); ?>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'inscricao-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
	'data_inscricao',
        'cpf',
        array(
            'name' => 'status',
            'header' => 'Status da inscrição',
            'value' => '$data->statusPorExtenso',
        ),
        'nome',
        'sobrenome',
        'email',
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
                    'visible' => '$data->recuperarArquivoDocumento("rg") != ""',
                ),
                'diploma' => array(
                    'label' => 'Diploma',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("diploma", true)',
                    'visible' => '$data->recuperarArquivoDocumento("diploma") != ""',
                ),
                'comprovante_residencia' => array(
                    'label' => 'Comprovante de residência',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("comprovante_residencia", true)',
                    'visible' => '$data->recuperarArquivoDocumento("comprovante_residencia") != ""',
                ),
                'curriculo' => array(
                    'label' => 'Currículo',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("curriculo", true)',
                    'visible' => '$data->recuperarArquivoDocumento("curriculo") != ""',
                ),
                'justificativa' => array(
                    'label' => 'Justificativa de próprio punho',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/documento.png',
                    'url' => '$data->recuperarArquivoDocumento("justificativa", true)',
                    'visible' => '$data->recuperarArquivoDocumento("justificativa") != ""',
                ),
            )
        ),
        array(
            'headerTemplate' => 'Matrícular',
            'name' => 'pagou_matricula',
            'id' => 'pagou_matricula',
            'value' => '$data->id',
            'class' => 'CCheckBoxColumn',
            'selectableRows' => '10000',
            'checked' => '$data->status >= 3',
            'disabled' => '$data->status >= 3',
            'htmlOptions' => array(
                'style' => 'width: 130px;',
            ),
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{view}{update}',
//            'template' => '{view}{aprovar}{recusar}'
// http://www.yiiframework.com/wiki/106/using-cbuttoncolumn-to-customize-buttons-in-cgridview/
            'updateButtonUrl' => 'urlEditar($data)',
        ),
    ),
    'rowCssClassExpression' => 'classeLinha($row, $data)',
));

echo CHtml::submitButton('Exportar', array('name' => 'exportar'));

?>

    <?php echo CHtml::submitButton('Salvar alterações'); ?>

<?php $this->endWidget(); ?>
