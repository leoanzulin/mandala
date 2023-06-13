<?php

function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function linkAvaliar($data)
{
    return ['editarTcc', 'id' => $data->id];
}
?>

<h1>Gerenciar TCC</h1>
<br>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'tccs-form',
	'enableAjaxValidation'=>false,
)); ?>

<h2>Todos os trabalhos</h2>

<?php

$columns = [
    'titulo',
    [
        'header' => 'Aluno',
        'name' => 'inscricao_nome_completo_search',
        'value' => '$data->inscricao->nome . " " . $data->inscricao->sobrenome',
        'htmlOptions' => ['style' => 'width: 300px;'],
        'filterHtmlOptions' => ['style' => 'width: 300px;'],
    ],
    [
        'header' => 'Fase do TCC',
        'name' => 'fase_search',
        'value' => '$data->getFase()',
        'filter' => false,
        'htmlOptions' => ['style' => 'width: 200px;'],
    ],
    [
        'class' => 'CButtonColumn',
        'template' => '{update}',
        'updateButtonUrl' => 'linkAvaliar($data)',
        'updateButtonLabel' => 'Editar',
    ],
];

// https://forum.yiiframework.com/t/cgridview-filter-dropdown-from-array/49519/9
$columnsComFiltroDeTcc = $columns;
$columnsComFiltroDeTcc[2]['filter'] = Tcc::FASES;
$columnsComFiltroDeTcc[2]['filterHtmlOptions'] = ['style' => 'width: 200px;'];

$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'gerenciar-tccs-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => $columnsComFiltroDeTcc,
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
?>

<h2>Trabalhos com atribuição de pré-orientador pendente</h2>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'gerenciar-tccs2-grid',
    'dataProvider' => $model->search(Tcc::FASE_VERSAO_PARA_VALIDACAO_ENTREGUE),
    'filter' => $model,
    'columns' => $columns,
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
?>

<h2>Trabalhos com atribuição de banca pendente</h2>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'gerenciar-tccs3-grid',
    'dataProvider' => $model->search(Tcc::FASE_VERSAO_DA_BANCA_ENTREGUE),
    'filter' => $model,
    'columns' => $columns,
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
?>

<h2>Trabalhos com atribuição de orientador final pendente</h2>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'gerenciar-tccs4-grid',
    'dataProvider' => $model->search(Tcc::FASE_VERSAO_FINAL_ENTREGUE),
    'filter' => $model,
    'columns' => $columns,
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);

$this->endWidget();
?>

