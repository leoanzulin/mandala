<?php
function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function calcularTotal($compras)
{
    $soma = array_reduce($compras, function($acumulado, $compra) {
        return $acumulado + $compra->valor;
    }, 0);
    return 'Total: ' . formatarDinheiro($soma);
}

function formatarDinheiro($valor)
{
    return 'R$' . number_format($valor, 2, ',', '.');
}

function imprimirTotal($data)
{
    return formatarDinheiro($data->valor);
}

function getColaborador($data)
{
    // https://stackoverflow.com/questions/7322682/best-way-to-store-json-in-an-html-attribute
    return '<span>' . $data->getColaborador()->nomeCompleto . '</span>' .
    '<input type="hidden" id="compra' . $data->id . '" value="' . htmlentities(json_encode($data->asArray()), ENT_QUOTES, 'UTF-8') . '">';
}

function linkDeletar($data)
{
    return array('deletarCompra', 'id' => $data->id);
}
?>

<h1>Gerenciar compras</h1>

<br>
<ul>
    <li>
        <?php echo CHtml::link('Exportar planilha de todas as compras', ['exportador/planilhaDeCompras']); ?>
    </li>
</ul>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'compras-grid',
    'dataProvider' => $compras,
    'columns' => [
        'data',
        [
            'header' => 'Colaborador',
            'name' => 'colaborador.nomeCompleto',
            'type' => 'raw',
            'value' => 'getColaborador($data)'
        ],
        'descricao',
        'local',
        [
            'header' => 'Valor',
            'value' => 'formatarDinheiro($data->valor)',
            'footer' => calcularTotal($compras->getData()),
        ],
        [
            'class' => 'ButtonColumn',
            'template' => '{update}{delete}',
            'updateButtonOptions' => [
                'onClick' => 'return editarCompra({id});',
            ],
            'updateButtonUrl' => '#',
            'deleteButtonUrl' => 'linkDeletar($data)',
        ],
    ],
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
?>

<h2 id="nova-compra">Nova compra</h2>

<?php $form = $this->beginWidget('CActiveForm', [
	'id' => 'nova-compra-form',
	'enableAjaxValidation'=>false,
]); ?>

<div class="row">
    <?php echo $form->labelEx($compra, 'colaborador'); ?>
    <?php
        echo $form->dropDownList(
            $compra,
            'colaborador',
            $colaboradores,
            [ 'empty' => 'Selecione o colaborador' ]
        );
    ?>
    <?php echo $form->error($compra, 'colaborador'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($compra, 'data'); ?>
    <?php echo $form->textField($compra, 'data', ['autocomplete' => 'off']); ?>
    <?php echo $form->error($compra, 'data'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($compra, 'descricao'); ?>
    <?php echo $form->textField($compra, 'descricao'); ?>
    <?php echo $form->error($compra, 'descricao'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($compra, 'local'); ?>
    <?php echo $form->textField($compra, 'local'); ?>
    <?php echo $form->error($compra, 'local'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($compra, 'valor'); ?>
    <?php echo $form->textField($compra, 'valor'); ?>
    <?php echo $form->error($compra, 'valor'); ?>
</div>

<input type="hidden" id="Compra_id" name="Compra[id]" value="">

<div class="row buttons" style="margin-top: 30px">
    <label></label>
    <?php echo CHtml::submitButton('Salvar', [
        'id' => 'botao-salvar',
        'class' => 'btn btn-lg btn-success',
        'name' => 'salvar',
        'onClick' => 'return validarFormularioCompleto();'
    ]); ?>
    <button
        id="botao-cancelar"
        class="btn btn-lg"
        name="cancelar"
        onclick="return cancelarEdicao();"
        type="button"
        style="display: none"
    >Cancelar</button>
</div>

<?php $this->endWidget(); ?>

<script>
    /// https://github.com/Pikaday/Pikaday
    var picker = new Pikaday({
        field: document.getElementById('Compra_data'),
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

    function validarFormularioCompleto() {
        const campoColaborador = document.getElementById('Compra_colaborador');
        const campoData = document.getElementById('Compra_data');
        const campoDescricao = document.getElementById('Compra_descricao');
        const campoLocal = document.getElementById('Compra_local');
        const campoValor = document.getElementById('Compra_valor');

        let erros = '';
        if (campoColaborador.value == '') erros += '- Selecione um colaborador\n';
        if (campoData.value == '') erros += '- Preencha a data\n';
        if (campoDescricao.value == '') erros += '- Preencha a descrição\n';
        if (campoLocal.value == '') erros += '- Preencha o local\n';
        if (campoValor.value == '') erros += '- Preencha o valor\n';

        if (erros != '') {
            erros = 'Pendências no cadastro:\n' + erros;
            alert(erros);
            return false;
        }
        return true;
    }

    function editarCompra(id) {
        const elemento = document.getElementById('compra' + id);
        const compra = JSON.parse(elemento.value);

        document.getElementById('Compra_id').value = compra.id;
        document.getElementById('Compra_colaborador').value = compra.tipo_colaborador + '_' + compra.colaborador_cpf;
        document.getElementById('Compra_data').value = compra.data;
        document.getElementById('Compra_descricao').value = compra.descricao;
        document.getElementById('Compra_local').value = compra.local;
        document.getElementById('Compra_valor').value = compra.valor;

        const botaoCancelar = document.getElementById('botao-cancelar');
        botaoCancelar.style.display = 'inline-block';
        const botaoSalvar = document.getElementById('botao-salvar');
        botaoSalvar.value = 'Atualizar';

        const formularioCompra = document.getElementById("nova-compra");
        formularioCompra.scrollIntoView();
        // Previne seguir o link do botão de update
        return false;
    }

    function cancelarEdicao() {
        const botaoCancelar = document.getElementById('botao-cancelar');
        botaoCancelar.style.display = 'none';
        const botaoSalvar = document.getElementById('botao-salvar');
        botaoSalvar.value = 'Salvar';

        document.getElementById('Compra_id').value = '';
        document.getElementById('Compra_colaborador').value = '';
        document.getElementById('Compra_data').value = '';
        document.getElementById('Compra_descricao').value = '';
        document.getElementById('Compra_local').value = '';
        document.getElementById('Compra_valor').value = '';
    }

</script>