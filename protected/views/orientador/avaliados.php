<?php

function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function linkVisualizar($data)
{
    return ['avaliar', 'id' => $data->id];
}

function gerarLink($tcc)
{
    return CHtml::link($tcc->titulo, ['avaliar', 'id' => $tcc->id]);
}

$this->breadcrumbs = [
    'TCC' => ['/orientador'],
    'Avaliados',
];

?>

<h1>Trabalhos avaliados</h1>
<br>

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
        'template' => '{view}',
        'viewButtonUrl' => 'linkVisualizar($data)',
        'viewButtonLabel' => 'Visualizar',
    ],
];

// https://forum.yiiframework.com/t/cgridview-filter-dropdown-from-array/49519/9
$columnsComFiltroDeTcc = $columns;
$columnsComFiltroDeTcc[2]['filter'] = Tcc::FASES;
$columnsComFiltroDeTcc[2]['filterHtmlOptions'] = ['style' => 'width: 200px;'];

$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'gerenciar-tccs-grid',
    'dataProvider' => $model->searchTccsQueOriento(Yii::app()->user->id),
    'filter' => $model,
    'columns' => $columnsComFiltroDeTcc,
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
?>

</ul>
