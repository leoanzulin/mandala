<?php

function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function linkAvaliar($data)
{
    return ['avaliar', 'id' => $data->id];
}

function gerarLink($tcc)
{
    return CHtml::link($tcc->titulo, ['avaliar', 'id' => $tcc->id]);
}

$this->breadcrumbs = [
    'TCC' => ['/orientador'],
    'Pendentes',
];

?>

<h1>Trabalhos pendentes de avaliação</h1>
<br>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'trabalhos-pendentes-form',
	'enableAjaxValidation'=>false,
)); ?>

<h2>Validação</h2>
<ul>
<?php
    if (empty($validacao)) {
        echo "<p>Não há nenhum TCC para avaliar</p>";
    }
    foreach ($validacao as $tcc) {
        echo "<li>". gerarLink($tcc) ."</li>";
    }
?>
</ul>

<h2>Banca</h2>
<ul>
<?php
    if (empty($banca)) {
        echo "<p>Não há nenhum TCC para avaliar</p>";
    }
    foreach ($banca as $tcc) {
        echo "<li>". gerarLink($tcc) ."</li>";
    }
?>
</ul>

<h2>Versão final</h2>
<ul>
<?php
    if (empty($final)) {
        echo "<p>Não há nenhum TCC para avaliar</p>";
    }
    foreach ($final as $tcc) {
        echo "<li>". gerarLink($tcc) ."</li>";
    }
?>
</ul>

<?php
/*
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'trabalhos-pendentes-grid',
    'dataProvider' => $tcc->searchPendentesValidacao(),
    'filter' => $tcc,
    'columns' => [
        [
            'header' => 'Aluno',
            'name' => 'inscricao.nome',
        ],
        [
            'name' => 'titulo',
            'header' => 'TCC',
        ],
        [
            'header' => 'Habilitação',
            'name' => 'habilitacao.nome',
        ],
        [
            'class' => 'CButtonColumn',
            'template' => '{update}',
            'updateButtonUrl' => 'linkAvaliar($data)',
            'updateButtonLabel' => 'Avaliar',
        ],
    ],
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
*/
?>

<?php $this->endWidget(); ?>
