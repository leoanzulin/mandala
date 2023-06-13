<?php
function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function calcularTotal($pagamentos)
{
    $soma = array_reduce($pagamentos, function($acumulado, $pagamento) {
        return $acumulado + $pagamento->valor_total;
    }, 0);
    return 'Total: ' . formatarDinheiro($soma);
}

function calcularTotalPago($pagamentos)
{
    $soma = array_reduce($pagamentos, function($acumulado, $pagamento) {
        return $acumulado + $pagamento->valor_pago;
    }, 0);
    return 'Total: ' . formatarDinheiro($soma);
}

function calcularTotalSobra($pagamentos)
{
    $soma = array_reduce($pagamentos, function($acumulado, $pagamento) {
        return $acumulado + $pagamento->valor_pago - $pagamento->valor_total;
    }, 0);
    return 'Total: ' . formatarDinheiro($soma);
}

function formatarDinheiro($valor)
{
    return 'R$' . number_format($valor, 2, ',', '.');
}

$mesAnterior = $mes - 1;
$anoAnterior = $ano;
if ($mesAnterior == 0) {
    $mesAnterior = 12;
    $anoAnterior = $ano - 1;
}

$proximoMes = $mes + 1;
$proximoAno = $ano;
if ($proximoMes == 13) {
    $proximoMes = 1;
    $proximoAno = $ano + 1;
}

function imprimirServicos($data)
{
    $html = '<table class="tabela-servicos"><tbody>';
    foreach ($data->servicos as $servico) {
        $html .= "<tr><td>{$servico->funcao}</td><td>{$servico->descricao}</td><td>" . formatarDinheiro($servico->valor) . "</td></tr>";
    }
    $html .= '</tbody></table>';
    return $html;
}

function getColaborador($data)
{
    // https://stackoverflow.com/questions/7322682/best-way-to-store-json-in-an-html-attribute
    return '<span>' . $data->getColaborador()->nomeCompleto . '</span>' .
    '<input type="hidden" id="pagamento' . $data->id . '" value="' . htmlentities(json_encode($data->asArray()), ENT_QUOTES, 'UTF-8') . '">';
}

function linkDeletar($data)
{
    return array('deletarPagamentoColaborador', 'id' => $data->id);
}
?>

<h1>Gerenciar pagamentos de colaboradores <?php echo $mes . '/' . $ano ?></h1>

<div style="margin-bottom: 50px">
<?php echo CHtml::link('&lt; Ir para mês anterior', ['pagamentosDeColaboradores', 'mes' => $mesAnterior, 'ano' => $anoAnterior], [
    'style' => 'float: left'
]); ?>
<?php echo CHtml::link('Ir para próximo mês &gt;', ['pagamentosDeColaboradores', 'mes' => $proximoMes, 'ano' => $proximoAno], [
    'style' => 'float: right'
]); ?>
</div>

<br>
<ul>
    <li>
        <?php echo CHtml::link('Exportar planilha de todos os pagamentos de colaboradores', ['exportador/planilhaDePagamentosDeColaboradores']); ?>
    </li>
    <li>
        <?php
            echo CHtml::link(
                'Exportar planilha dos pagamentos de colaboradores de ' . CalendarioHelper::nomeDoMes($mes) . ' de ' . $ano,
                ['exportador/planilhaDePagamentosDeColaboradoresDoMesEAno', 'mes' => $mes, 'ano' => $ano]
            );
        ?>
    </li>
</ul>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'pagamentos-colaboradores-grid',
    'dataProvider' => $pagamentos,
    'columns' => [
        'data',
        [
            'header' => 'Colaborador',
            'name' => 'colaborador.nomeCompleto',
            'type' => 'raw',
            'value' => 'getColaborador($data)'
        ],
        [
            'header' => 'Serviços',
            'type' => 'html',
            'value' => 'imprimirServicos($data)',
        ],
        [
            'name' => 'valor_total',
            'value' => 'formatarDinheiro($data->valor_total)',
            'footer' => calcularTotal($pagamentos->getData()),
        ],
        [
            'name' => 'valor_pago',
            'value' => 'formatarDinheiro($data->valor_pago)',
            'footer' => calcularTotalPago($pagamentos->getData()),
        ],
        [
            'header' => 'Sobra',
            'value' => 'formatarDinheiro($data->valor_pago - $data->valor_total)',
            'footer' => calcularTotalSobra($pagamentos->getData()),
        ],
        [
            'name' => 'forma_pagamento',
        ],
        [
            'class' => 'ButtonColumn',
            'template' => '{update}{delete}',
            'updateButtonOptions' => [
                'onClick' => 'return editarPagamento({id});',
            ],
            'updateButtonUrl' => '#',
            'deleteButtonUrl' => 'linkDeletar($data)',
        ],
    ],
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
?>

<h2 id="novo-pagamento">Novo pagamento</h2>

<?php $form = $this->beginWidget('CActiveForm', [
	'id' => 'novo-pagamento-form',
	'enableAjaxValidation'=>false,
]); ?>

<div class="row">
    <?php echo $form->labelEx($pagamento, 'colaborador'); ?>
    <?php
        echo $form->dropDownList(
            $pagamento,
            'colaborador',
            $colaboradores,
            [ 'empty' => 'Selecione o colaborador' ]
        );
    ?>
    <?php echo $form->error($pagamento, 'colaborador'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($pagamento, 'data'); ?>
    <?php echo $form->textField($pagamento, 'data', ['autocomplete' => 'off']); ?>
    <?php echo $form->error($pagamento, 'data'); ?>
</div>

<p style="font-size: 1.4em">Serviços</p>
<table class="tabela-servicos-formulario">
    <thead><tr><th>Função</th><th>Descrição</th><th>Valor (R$)</th><th></th></tr></thead>
    <tbody id="servicos">
        <tr id="linha-adicionar">
            <td>
                <select id="form-funcao">
                    <option value="">Função</option>
                    <option value="docente">Docente</option>
                    <option value="tutor">Tutor</option>
                    <option value="coordenacao">Coordenação</option>
                    <option value="suporte">Suporte</option>
                    <option value="secretaria">Secretaria</option>
                </select>
            </td>
            <td>
                <input id="form-descricao" type="text"></input>
            </td>
            <td>
                <input id="form-valor" type="text"></input>
            </td>
            <td>
                <button type="button" onClick="adicionarLinha()">+</button>
            </td>
        </tr>
    </tbody>
</table>

<div class="row">
    <?php echo $form->labelEx($pagamento, 'valor_total'); ?>
    <?php echo $form->textField($pagamento, 'valor_total', ['readonly' => true]); ?>
    <?php echo $form->error($pagamento, 'valor_total'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($pagamento, 'valor_pago'); ?>
    <?php echo $form->textField($pagamento, 'valor_pago'); ?>
    <?php echo $form->error($pagamento, 'valor_pago'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($pagamento, 'forma_pagamento'); ?>
    <?php
        echo $form->dropDownList(
            $pagamento,
            'forma_pagamento',
            [
                'pidict' => 'PIDICT',
                'rpa' => 'RPA',
                'empresa' => 'Empresa',
                'terceiros' => 'Terceiros',
            ],
            [ 'empty' => 'Selecione a forma de pagamento' ]
        );
    ?>
    <?php echo $form->error($pagamento, 'forma_pagamento'); ?>
</div>

<?php /*
<div class="row">
    <?php echo $form->labelEx($pagamento, 'observacoes'); ?>
    <?php echo $form->textField($pagamento, 'observacoes'); ?>
    <?php echo $form->error($pagamento, 'observacoes'); ?>
</div>
*/ ?>

<input type="hidden" id="PagamentoColaborador_id" name="PagamentoColaborador[id]" value="">

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
        field: document.getElementById('PagamentoColaborador_data'),
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

    var idLinha = 1;

    function adicionarLinha() {
        if (!validar()) return;

        adicionarServico(
            idLinha,
            document.getElementById('form-funcao').value,
            document.getElementById('form-descricao').value,
            document.getElementById('form-valor').value,
        )
        atualizarValorTotal();
        limparFormulario();

        idLinha++;
    };

    function adicionarServico(idLinha, funcao, descricao, valor) {
        const coluna1 = document.createElement('td');
        coluna1.innerHTML = '<input name="PagamentoColaborador[servicos][' + idLinha + '][funcao]" readonly type="text" value="' + funcao + '"></input>';
        const coluna2 = document.createElement('td');
        coluna2.innerHTML = '<input name="PagamentoColaborador[servicos][' + idLinha + '][descricao]" readonly type="text" value="' + descricao + '"></input>';
        const coluna3 = document.createElement('td');
        coluna3.innerHTML = '<input data-tipo="valor" name="PagamentoColaborador[servicos][' + idLinha + '][valor]" readonly type="text" value="' + valor + '"></input>';
        const coluna4 = document.createElement('td');
        coluna4.addEventListener('click', function() {
            const id = this.parentNode.getAttribute('data-id');
            removerLinha(id);
        });
        coluna4.innerHTML = '<button type="button">-</button>';

        const linha = document.createElement('tr');
        linha.setAttribute('data-id', idLinha);
        linha.appendChild(coluna1);
        linha.appendChild(coluna2);
        linha.appendChild(coluna3);
        linha.appendChild(coluna4);

        const linhaAdicionar = document.getElementById('linha-adicionar');
        document.getElementById('servicos').insertBefore(linha, linhaAdicionar);
    }

    function validar() {
        const funcao = document.getElementById('form-funcao');
        const descricao = document.getElementById('form-descricao');
        const valor = document.getElementById('form-valor');

        if (funcao.value.trim() == '') {
            funcao.focus();
            return false;
        }
        const valorComPonto = valor.value.trim().replace(',', '.');
        if (valorComPonto == '' || isNaN(valorComPonto) || valorComPonto < 0) {
            valor.focus();
            return false;
        }
        valor.value = valorComPonto;
        return true;
    }

    function atualizarValorTotal() {
        const nodosValores = document.querySelectorAll('#servicos tr td input[data-tipo="valor"]');
        let total = 0.0;
        nodosValores.forEach(function(nodo) {
            total += parseFloat(nodo.value);
        });
        document.getElementById('PagamentoColaborador_valor_total').value = total;
    }

    function limparFormulario() {
        const funcao = document.getElementById('form-funcao');
        const descricao = document.getElementById('form-descricao');
        const valor = document.getElementById('form-valor');
        funcao.value = '';
        descricao.value = '';
        valor.value = '';
        funcao.focus();
    }

    function removerLinha(id) {
        const elemento = document.querySelector('#servicos tr[data-id="' + id + '"]');
        elemento.parentNode.removeChild(elemento);
        atualizarValorTotal();
    };

    function validarFormularioCompleto() {
        const campoColaborador = document.getElementById('PagamentoColaborador_colaborador');
        const campoData = document.getElementById('PagamentoColaborador_data');
        const campoValorTotal = document.getElementById('PagamentoColaborador_valor_total');
        const campoValorPago = document.getElementById('PagamentoColaborador_valor_pago');
        const campoFormaPagamento = document.getElementById('PagamentoColaborador_forma_pagamento');

        let erros = '';
        if (campoColaborador.value == '') erros += '- Selecione um colaborador\n';
        if (campoData.value == '') erros += '- Selecione uma data\n';
        if (campoValorTotal.value == '' || campoValorTotal.value == '0') erros += '- Adicione pelo menos um serviço\n';
        if (campoValorPago.value == '' || isNaN(campoValorPago.value) || campoValorPago.value <= 0) erros += '- Preencha o valor pago\n';
        if (campoFormaPagamento.value == '') erros += '- Seleciona uma forma de pagamento\n';

        if (erros != '') {
            erros = 'Pendências no cadastro:\n' + erros;
            alert(erros);
            return false;
        }
        return true;
    }

    function editarPagamento(id) {
        const elemento = document.getElementById('pagamento' + id);
        const pagamento = JSON.parse(elemento.value);
        console.log(pagamento);

        document.getElementById('PagamentoColaborador_id').value = pagamento.id;
        document.getElementById('PagamentoColaborador_colaborador').value = pagamento.tipo_colaborador + '_' + pagamento.colaborador_cpf;
        document.getElementById('PagamentoColaborador_data').value = pagamento.data;
        // document.getElementById('PagamentoColaborador_valor_total').value = pagamento.valor_total.slice(0, -2);
        document.getElementById('PagamentoColaborador_valor_pago').value = pagamento.valor_pago.slice(0, -2);
        document.getElementById('PagamentoColaborador_forma_pagamento').value = pagamento.forma_pagamento;

        pagamento.servicos.forEach(function(servico) {
            adicionarServico(
                idLinha,
                servico.funcao,
                servico.descricao,
                servico.valor.slice(0, -2),
            );
            idLinha++;
        });
        atualizarValorTotal();

        const botaoCancelar = document.getElementById('botao-cancelar');
        botaoCancelar.style.display = 'inline-block';
        const botaoSalvar = document.getElementById('botao-salvar');
        botaoSalvar.value = 'Atualizar';

        const formularioServico = document.getElementById("novo-pagamento");
        formularioServico.scrollIntoView();
        // Previne seguir o link do botão de update
        return false;
    }

    function cancelarEdicao() {
        const botaoCancelar = document.getElementById('botao-cancelar');
        botaoCancelar.style.display = 'none';
        const botaoSalvar = document.getElementById('botao-salvar');
        botaoSalvar.value = 'Salvar';

        removerServicos();
        document.getElementById('PagamentoColaborador_id').value = '';
        document.getElementById('PagamentoColaborador_colaborador').value = '';
        document.getElementById('PagamentoColaborador_data').value = '';
        document.getElementById('PagamentoColaborador_valor_total').value = '';
        document.getElementById('PagamentoColaborador_valor_pago').value = '';
        document.getElementById('PagamentoColaborador_forma_pagamento').value = '';
    }

    function removerServicos() {
        const servicos = document.querySelectorAll('#servicos tr[data-id]');
        servicos.forEach(function(servico) {
            servico.remove();
        });
    }

</script>