<?php
function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}
?>

<h1>Gerenciar colaboradores</h1>

<?php

echo CHtml::link('Adicionar novo colaborador', ['/colaborador/cadastrar']);

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'docente-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'cpf',
        'nome',
        'sobrenome',
        'email',
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}',
            'updateButtonUrl' => 'Yii::app()->createUrl("/colaborador/editar", array("cpf" => $data["cpf"]))',
        ),
    ),
    'rowCssClassExpression' => 'classeLinha($row, $data)',
));
