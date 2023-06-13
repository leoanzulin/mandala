<?php
function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}
?>

<h1>Gerenciar encontros presenciais</h1>

<?php

echo CHtml::link('Adicionar novo encontro presencial', ['/encontro/cadastrar']);

$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'docente-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'tipo',
        'local',
        'data',
        array(
            'class' => 'CButtonColumn',
            'template' => '{update} {delete}',
            'updateButtonUrl' => 'Yii::app()->createUrl("encontro/editar", array("id" => $data["id"]))',
            'deleteButtonUrl' => 'Yii::app()->createUrl("encontro/deletar", array("id" => $data["id"]))',
        ),
    ),
    'rowCssClassExpression' => 'classeLinha($row, $data)',
));
