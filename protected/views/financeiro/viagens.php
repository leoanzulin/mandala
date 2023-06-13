<?php
function classeLinha($row, $data)
{
    return ($row % 2) ? 'linha-par' : 'linha-impar';
}

function calcularTotal($viagens)
{
    $soma = array_reduce($viagens, function($acumulado, $viagem) {
        $totalViagem = array_reduce($viagem->despesas, function($acumulado, $despesa) {
            return $acumulado + $despesa->valor;
        }, 0);
        return $acumulado + $totalViagem;
    }, 0);
    return 'Total: ' . formatarDinheiro($soma);
}

function formatarDinheiro($valor)
{
    return 'R$' . number_format($valor, 2, ',', '.');
}

function imprimirPeriodo($data)
{
    return "{$data->data_ida} a {$data->data_volta}";
}

function imprimirDespesas($data)
{
    $html = '<table class="tabela-despesas"><tbody>';
    foreach ($data->despesas as $despesa) {
        $html .= "<tr><td>{$despesa->tipo}</td><td>" . formatarDinheiro($despesa->valor) . "</td></tr>";
    }
    $html .= '</tbody></table>';
    return $html;
}

function imprimirTotal($data)
{
    $total = 0.0;
    foreach ($data->despesas as $despesa) {
        $total += $despesa->valor;
    }
    return formatarDinheiro($total);
}

function getColaborador($data)
{
    // https://stackoverflow.com/questions/7322682/best-way-to-store-json-in-an-html-attribute
    return '<span>' . $data->getColaborador()->nomeCompleto . '</span>' .
    '<input type="hidden" id="viagem' . $data->id . '" value="' . htmlentities(json_encode($data->asArray()), ENT_QUOTES, 'UTF-8') . '">';
}

function linkDeletar($data)
{
    return array('deletarViagem', 'id' => $data->id);
}
?>

<h1>Gerenciar viagens</h1>

<br>
<ul>
    <li>
        <?php echo CHtml::link('Exportar planilha de todas as viagens', ['exportador/planilhaDeViagens']); ?>
    </li>
</ul>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'id' => 'viagens-grid',
    'dataProvider' => $viagens,
    'columns' => [
        [
            'header' => 'Colaborador',
            'name' => 'colaborador.nomeCompleto',
            'type' => 'raw',
            'value' => 'getColaborador($data)'
        ],
        'local',
        [
            'header' => 'Período',
            'type' => 'html',
            'value' => 'imprimirPeriodo($data)',
        ],
        [
            'header' => 'Despesas',
            'type' => 'html',
            'value' => 'imprimirDespesas($data)',
        ],
        [
            'header' => 'Total',
            'value' => 'imprimirTotal($data)',
            'footer' => calcularTotal($viagens->getData()),
        ],
        [
            'class' => 'ButtonColumn',
            'template' => '{update}{delete}',
            'updateButtonOptions' => [
                'onClick' => 'return editarViagem({id});',
            ],
            'updateButtonUrl' => '#',
            'deleteButtonUrl' => 'linkDeletar($data)',
        ],
    ],
    'rowCssClassExpression' => 'classeLinha($row, $data)',
]);
?>

<h2 id="nova-viagem">Nova viagem</h2>

<?php $form = $this->beginWidget('CActiveForm', [
	'id' => 'nova-viagem-form',
	'enableAjaxValidation'=>false,
]); ?>

<div class="row">
    <?php echo $form->labelEx($viagem, 'colaborador'); ?>
    <?php
        echo $form->dropDownList(
            $viagem,
            'colaborador',
            $colaboradores,
            [ 'empty' => 'Selecione o colaborador' ]
        );
    ?>
    <?php echo $form->error($viagem, 'colaborador'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($viagem, 'data_ida'); ?>
    <?php echo $form->textField($viagem, 'data_ida', ['autocomplete' => 'off']); ?>
    <?php echo $form->error($viagem, 'data_ida'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($viagem, 'data_volta'); ?>
    <?php echo $form->textField($viagem, 'data_volta', ['autocomplete' => 'off']); ?>
    <?php echo $form->error($viagem, 'data_volta'); ?>
</div>

<div class="row">
    <?php echo $form->labelEx($viagem, 'local'); ?>
    <?php echo $form->textField($viagem, 'local'); ?>
    <?php echo $form->error($viagem, 'local'); ?>
</div>

<p style="font-size: 1.4em">Despesas</p>
<table class="tabela-despesas-formulario">
    <thead><tr><th>Tipo</th><th>Valor (R$)</th><th></th></tr></thead>
    <tbody id="despesas">
        <tr id="linha-adicionar">
            <td>
                <select id="form-tipo">
                    <option value="">Tipo</option>
                    <option value="hospedagem">Hospedagem</option>
                    <option value="passagem_aerea">Passagem aérea</option>
                    <option value="passagem_terrestre">Passagem terrestre</option>
                    <option value="transporte_terrrestre">Transporte terrestre (locação de veículo)</option>
                    <option value="taxi_uber">Taxi/uber</option>
                    <option value="alimentacao">Alimentação</option>
                </select>
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
    <label>Valor total</label>
    <input id="Viagem_valor_total" type="text" value="" disabled>
</div>

<input type="hidden" id="Viagem_id" name="Viagem[id]" value="">

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
        field: document.getElementById('Viagem_data_ida'),
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

    var picker = new Pikaday({
        field: document.getElementById('Viagem_data_volta'),
        onSelect: function(date) {
            const year = date.getFullYear();
            const month = date.getMonth() + 1;
            const day = date.getDate();
            const formattedDate = [
                day < 10 ? '0' + day : day,
                month < 10 ? '0' + month : month,
                year,
            ].join('/');
            document.getElementById('Viagem_data_volta').value = formattedDate
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

        adicionarDespesa(
            idLinha,
            document.getElementById('form-tipo').value,
            document.getElementById('form-valor').value,
        )
        atualizarValorTotal();
        limparFormulario();

        idLinha++;
    };

    function adicionarDespesa(idLinha, tipo, valor) {
        const coluna1 = document.createElement('td');
        coluna1.innerHTML = '<input name="Viagem[despesas][' + idLinha + '][tipo]" readonly type="text" value="' + tipo + '"></input>';
        const coluna3 = document.createElement('td');
        coluna3.innerHTML = '<input data-tipo="valor" name="Viagem[despesas][' + idLinha + '][valor]" readonly type="text" value="' + valor + '"></input>';
        const coluna4 = document.createElement('td');
        coluna4.addEventListener('click', function() {
            const id = this.parentNode.getAttribute('data-id');
            removerLinha(id);
        });
        coluna4.innerHTML = '<button type="button">-</button>';

        const linha = document.createElement('tr');
        linha.setAttribute('data-id', idLinha);
        linha.appendChild(coluna1);
        linha.appendChild(coluna3);
        linha.appendChild(coluna4);

        const linhaAdicionar = document.getElementById('linha-adicionar');
        document.getElementById('despesas').insertBefore(linha, linhaAdicionar);
    }

    function validar() {
        const tipo = document.getElementById('form-tipo');
        const valor = document.getElementById('form-valor');

        if (tipo.value.trim() == '') {
            tipo.focus();
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
        const nodosValores = document.querySelectorAll('#despesas tr td input[data-tipo="valor"]');
        let total = 0.0;
        nodosValores.forEach(function(nodo) {
            total += parseFloat(nodo.value);
        });
        document.getElementById('Viagem_valor_total').value = total;
    }

    function limparFormulario() {
        const tipo = document.getElementById('form-tipo');
        const valor = document.getElementById('form-valor');
        tipo.value = '';
        valor.value = '';
        tipo.focus();
    }

    function removerLinha(id) {
        const elemento = document.querySelector('#despesas tr[data-id="' + id + '"]');
        elemento.parentNode.removeChild(elemento);
        atualizarValorTotal();
    };

    function validarFormularioCompleto() {
        const campoColaborador = document.getElementById('Viagem_colaborador');
        const campoDataIda = document.getElementById('Viagem_data_ida');
        const campoDataVolta = document.getElementById('Viagem_data_volta');
        const campoLocal = document.getElementById('Viagem_local');
        const campoValorTotal = document.getElementById('Viagem_valor_total');

        let erros = '';
        if (campoColaborador.value == '') erros += '- Selecione um colaborador\n';
        if (campoDataIda.value == '') erros += '- Selecione uma data de ida\n';
        if (campoDataVolta.value == '') erros += '- Selecione uma data de volta\n';
        if (campoLocal.value == '') erros += '- Preencha um local\n';
        if (campoValorTotal.value == '' || campoValorTotal.value == '0') erros += '- Adicione pelo menos uma despesa\n';

        if (erros != '') {
            erros = 'Pendências no cadastro:\n' + erros;
            alert(erros);
            return false;
        }
        return true;
    }

    function editarViagem(id) {
        const elemento = document.getElementById('viagem' + id);
        const viagem = JSON.parse(elemento.value);
        console.log(viagem);

        document.getElementById('Viagem_id').value = viagem.id;
        document.getElementById('Viagem_colaborador').value = viagem.tipo_colaborador + '_' + viagem.colaborador_cpf;
        document.getElementById('Viagem_data_ida').value = viagem.data_ida;
        document.getElementById('Viagem_data_volta').value = viagem.data_volta;
        document.getElementById('Viagem_local').value = viagem.local;

        viagem.despesas.forEach(function(despesa) {
            adicionarDespesa(
                idLinha,
                despesa.tipo,
                despesa.valor.slice(0, -2),
            );
            idLinha++;
        });
        atualizarValorTotal();

        const botaoCancelar = document.getElementById('botao-cancelar');
        botaoCancelar.style.display = 'inline-block';
        const botaoSalvar = document.getElementById('botao-salvar');
        botaoSalvar.value = 'Atualizar';

        const formularioViagem = document.getElementById("nova-viagem");
        formularioViagem.scrollIntoView();
        // Previne seguir o link do botão de update
        return false;
    }

    function cancelarEdicao() {
        const botaoCancelar = document.getElementById('botao-cancelar');
        botaoCancelar.style.display = 'none';
        const botaoSalvar = document.getElementById('botao-salvar');
        botaoSalvar.value = 'Salvar';

        removerDespesas();
        document.getElementById('Viagem_id').value = '';
        document.getElementById('Viagem_colaborador').value = '';
        document.getElementById('Viagem_data_ida').value = '';
        document.getElementById('Viagem_data_volta').value = '';
        document.getElementById('Viagem_local').value = '';
        document.getElementById('Viagem_valor_total').value = '';
    }

    function removerDespesas() {
        const despesas = document.querySelectorAll('#despesas tr[data-id]');
        despesas.forEach(function(despesa) {
            despesa.remove();
        });
    }

</script>