<?php
function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function linkDeletar($data)
{
    return array('deletarPagamentoAluno', 'id' => $data->id);
}

function calcularTotal($pagamentos)
{
    $soma = array_reduce($pagamentos, function($acumulado, $pagamento) {
        return $acumulado + $pagamento->valor;
    }, 0);
    return 'Total: ' . formatarDinheiro($soma);
}

function formatarDinheiro($valor)
{
    return 'R$' . number_format($valor, 2, ',', '.');
}
?>

<h1>Gerenciar pagamentos do aluno <?php echo $inscricao->nomeCompleto; ?></h1>

<br>
<ul>
    <li>Tipo de curso: <?php echo $inscricao->tipoDeCursoPorExtenso(); ?></li>
    <?php if ($inscricao->ehAlunoDeEspecializacao()) { ?>
    <li>Número de habilitações: <?php echo count($inscricao->habilitacoes); ?></li>
    <?php } ?>
</ul>

<b>Tipo de bolsa: </b>

<?php $form = $this->beginWidget('CActiveForm', [
	'id' => 'tipo-desconto-form',
	'enableAjaxValidation'=>false,
]); ?>

<?php echo $form->dropDownList(
    $inscricao,
    'tipo_bolsa',
    $tiposBolsa,
    ['empty' => 'Tipo de bolsa', 'style' => 'width: 350px']
); ?>

<button type="submit">Salvar</button>

<?php $this->endWidget(); ?>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'visualizar-matriculas-grid',
    'dataProvider' => $pagamentos,
    'columns' => [
        'data',
        'tipo',

        [
            'name' =>'valor',
            'value' => 'formatarDinheiro($data->valor)',
            // https://forum.yiiframework.com/t/yii-cgridview-footer-with-sum-of-column-values/59382/3
            'footer' => calcularTotal($pagamentos->getData()),
        ],
        'observacoes',
        [
            'class' => 'CButtonColumn',
            'template' => '{delete}',
            // 'updateButtonUrl' => 'linkEditar($data)',
            'deleteButtonUrl' => 'linkDeletar($data)',
        ],
    ],
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
?>

<h2>Novo pagamento</h2>

<?php $form = $this->beginWidget('CActiveForm', [
	'id' => 'novo-pagamento-form',
	'enableAjaxValidation'=>false,
]); ?>

<table>
    <thead><tr><th>Data</th><th>Tipo</th><th>Valor</th><th>Observações</th><th></th></tr></thead>
    <tbody>
    <tr>
        <th>
        <?php echo $form->textField($pagamento, 'data', ['id' => 'datepickerA', 'autocomplete' => 'off']); ?>
        </th>
        <th>
            <?php echo $form->dropDownList(
                $pagamento,
                'tipo',
                $listaTiposPagamentos,
                ['empty' => 'Tipo de pagamento']
            ); ?>
        </th>
        <th>
            <?php echo $form->textField($pagamento, 'valor'); ?>
        </th>
        <th>
            <?php echo $form->textField($pagamento, 'observacoes', array('size' => 256, 'maxlength' => 256)); ?>
        </th>
        <th>
            <?php echo CHtml::submitButton('Salvar', [
                'class' => 'btn btn-lg',
                'name' => 'salvar',
            ]); ?>
        </th>
    </tr>
    </tbody>
</table>

<?php $this->endWidget(); ?>

<script>
    /// https://github.com/Pikaday/Pikaday
    var picker = new Pikaday({
        field: document.getElementById('datepickerA'),
        parse: function(dateString, format) {
            const parts = dateString.split('/');
            return new Date(parseInt(parts[2]), parseInt(parts[1]) - 1, parseInt(parts[0]));
        },
        toString: function(date, format) {
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            return formattedDate = [
                day < 10 ? '0' + day : day,
                month < 10 ? '0' + month : month,
                year,
            ].join('/');
        },
        i18n: {
            previousMonth : 'Mês anterior',
            nextMonth     : 'Próximo mês',
            months        : ['Janeiro','Ffevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            weekdays      : ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            weekdaysShort : ['D','S','T','Q','Q','S','S'],
        },
    });
</script>