<?php
function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}
?>

<h1>Gerenciar docentes</h1>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'docente-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'cpf',
        'nome',
        'sobrenome',
        'email',
        'endereco',
        array(
            'name' => 'mestrando_ou_doutorando_ufscar_search',
            'value' => '$data->mestrando_ou_doutorando_ufscar ? "Sim" : "Não"',
            'htmlOptions' => array(
                'style' => 'width: 80px; text-align: center',
            ),
        ),
        'valor_bolsa',
        array(
            'class' => 'CButtonColumn',
            'template' => '{view}{update}',
            'viewButtonUrl' => 'Yii::app()->createUrl("/admin/visualizarDocente", array("cpf" => $data["cpf"]))',
            'updateButtonUrl' => 'Yii::app()->createUrl("/admin/editarDocente", array("cpf" => $data["cpf"]))',
        ),
    ),
    'rowCssClassExpression' => 'classeLinha($row, $data)',
));
