<?php
function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function linkEditar($data)
{
    return array('pagamentosDeAluno', 'id' => $data->id);
}

function calcularTotalPago($data)
{
    return array_reduce($data->pagamentos, function($soma, $pagamento) {
        return $soma + $pagamento->valor;
    }, 0.0);
}

?>

<h1>Gerenciar pagamentos de alunos</h1>

<br>
<ul>
    <li>
        <?php echo CHtml::link('Exportar planilha de pagamentos de todos os alunos', ['exportador/planilhaDePagamentosDeAlunos']); ?>
    </li>
</ul>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'visualizar-matriculas-grid',
    'dataProvider' => $model->searchMatriculados(),
    'filter'=>$model,
    'columns' => array(
        'cpf',
        'nomeCompleto',
        [
            'name' => 'recebe_bolsa_search',
            'value' => '$data->recebe_bolsa ? "Sim" : "Não"',
            'htmlOptions' => array(
                'style' => 'width: 80px; text-align: center',
            ),
        ],
        [
            'header' => 'Total pago',
            'value' => 'calcularTotalPago($data)'
        ],
        // 'turma',
        'status_aluno',
        array(
            'class' => 'CButtonColumn',
            'template' => '{update}',
            'updateButtonUrl' => 'linkEditar($data)',
        ),
    ),
    'rowCssClassExpression' => 'classeLinha($row, $data)',
));
?>