<div class="form">

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'docente-form',
    'enableAjaxValidation' => false,
));
?>

    <div class="row">
        <?php echo $form->labelEx($model, 'tipo'); ?>
        <?php echo $form->dropDownList(
            $model,
            'tipo',
            // TODO: Colocar este array em Constantes.php para facilitar atualizações
            [
                'avaliativo' => 'Avaliativo',
                'pedagogico' => 'Pedagógico',
                'banca' => 'Banca TCC',
                'outro' => 'Outro',
            ],
            [ 'empty' => 'Selecione o tipo de encontro' ]
        ); ?>
        <?php echo $form->error($model, 'tipo'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'local'); ?>
        <?php echo $form->textField($model, 'local', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'local'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'data'); ?>
        <?php echo $form->textField($model, 'data', ['id' => 'datepickerA', 'autocomplete' => 'off', 'class' => 'curto']); ?>
        <?php echo $form->error($model, 'data'); ?>
    </div>

    <div class="row">
        <label>Responsáveis</label>
        <select id="responsaveis" onChange="adicionarResponsavel(event)">
            <?php
                echo "<option value>Selecione o colaborador</option>\n";
                foreach ($colaboradores as $label => $colaborador2) { 
                    echo "<optgroup label=\"{$label}\">";
                    foreach ($colaborador2 as $chave => $nome) {
                        echo "<option value=\"{$chave}\">{$nome}</option>\n";
                    }
                    echo "</optgroup>";
                }
            ?>
        </select>
    </div>

    <div class="row">
        <table>
            <tbody id="responsaveis-selecionados">
<?php
    foreach ($model->responsaveis as $responsavel) {
?>
    <tr data-id="<?php echo $responsavel['id']; ?>">
        <td style="width: 150px"></td>
        <td>
            <input name="EncontroPresencial[responsaveis][]" type="hidden" value="<?php echo $responsavel['id']; ?>"></input>
            <?php echo $responsavel['nome']; ?>
        </td>
        <td>
            <button type="button" onClick="removerLinha('<?php echo $responsavel['id']; ?>')">X</button>
        </td>
    </tr>
<?php
    }
?>
            </tbody>
        </table>
    </div>
    <br>

    <div class="row">
        <?php echo $form->labelEx($model, 'atividades'); ?>
        <?php echo $form->textArea($model, 'atividades',
            [
                'maxlength' => 10000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Atividades desenvolvidas no encontro presencial',
            ]);
        ?>
        <?php echo $form->error($model, 'atividades'); ?>
    </div>

    <fieldset>
        <legend>Montar lista de presença</legend>

        <p style="font-size: 1.2em; color: blue">Para selecionar vários alunos, clique e arraste o mouse ou selecione alunos com a tecla <b>Control</b> pressionada</p>

        <div style="display: flex; justify-content: space-evenly; align-items: center">

            <div>
                <p style="font-weight: bold">Alunos fora da lista</p>
                <input type="text" id="filtro-fora" onKeyUp="atualizarListas()" placeholder="Filtrar por nome, CPF ou número UFSCar" style="width: 90%">
                <select id="fora-da-lista" style="display: block; height: 300px; width: 400px; overflow: scroll; border: 1px solid #333" multiple="multiple"></select>
            </div>

            <div style="display: flex; flex-direction: column">
                <button type="button" onClick="adicionarALista()" style="margin-bottom: 10px">&gt;&gt;&gt;</button>
                <button type="button" onClick="removerDaLista()">&lt;&lt;&lt;</button>
            </div>

            <div>
                <p style="font-weight: bold">Alunos dentro da lista</p>
                <input type="text" id="filtro-dentro" onKeyUp="atualizarListas()" placeholder="Filtrar por nome, CPF ou número UFSCar" style="width: 90%">
                <select id="dentro-da-lista" style="display: block; height: 300px; width: 400px; overflow: scroll; border: 1px solid #333" multiple="multiple"></select>
            </div>

        </div>

        <div id="alunos-selecionados"></div>
    </fieldset>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Salvar', [
            'class' => 'btn btn-success btn-lg',
            'name' => 'salvar',
        ]); ?>
    </div>

<?php $this->endWidget(); ?>

</div>

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

    function adicionarResponsavel(event) {
        const id = event.target.value;
        const indice = event.target.selectedIndex;
        const responsavel = event.target[indice].text;

        adicionarServico(id, responsavel);
        document.getElementById('responsaveis').selectedIndex = 0;
    }

    function adicionarServico(id, nome) {
        const coluna0 = document.createElement('td');
        coluna0.setAttribute('style', 'width: 150px');
        const coluna1 = document.createElement('td');
        coluna1.innerHTML = '<input name="EncontroPresencial[responsaveis][]" type="hidden" value="' + id + '"></input>' + nome;
        const coluna4 = document.createElement('td');
        coluna4.addEventListener('click', function() {
            const id = this.parentNode.getAttribute('data-id');
            removerLinha(id);
        });
        coluna4.innerHTML = '<button type="button">X</button>';

        const linha = document.createElement('tr');
        linha.setAttribute('data-id', id);
        linha.appendChild(coluna0);
        linha.appendChild(coluna1);
        linha.appendChild(coluna4);

        const linhaAdicionar = document.getElementById('');
        document.getElementById('responsaveis-selecionados').appendChild(linha);
    }

    function removerLinha(id) {
        const elemento = document.querySelector('#responsaveis-selecionados tr[data-id="' + id + '"]');
        elemento.parentNode.removeChild(elemento);
    };

    const inscricoes = [
        <?php
            $idsAlunosNaLista = array_map(function($aluno) { return $aluno->id; }, $model->alunos);

            foreach ($inscricoes as $inscricao) {
                $i = [
                    'id' => $inscricao->id,
                    'nome' => $inscricao->nomeCompleto,
                    'cpf' => $inscricao->cpf,
                    'numero_ufscar' => $inscricao->numero_ufscar,
                    'dentro' => in_array($inscricao->id, $idsAlunosNaLista),
                ];
                echo json_encode($i) . ',';
            }
        ?>
    ];

    function atualizarListas() {
        const foraDaLista = document.getElementById('fora-da-lista');
        foraDaLista.textContent = '';
        inscricoes.forEach(function (inscricao) {
            if (inscricao.dentro) return;
            if (!estaDentroDoFiltro(inscricao, 'fora')) return;
            const op = document.createElement('option');
            op.setAttribute('value', inscricao.id);
            op.innerHTML = inscricao.nome;
            foraDaLista.appendChild(op);
        });

        const dentroDaLista = document.getElementById('dentro-da-lista');
        const alunosSelecionados = document.getElementById('alunos-selecionados');
        dentroDaLista.textContent = '';
        alunosSelecionados.textContent = '';
        inscricoes.forEach(function (inscricao) {
            if (!inscricao.dentro) return;
            if (!estaDentroDoFiltro(inscricao, 'dentro')) return;
            const op = document.createElement('option');
            op.setAttribute('value', inscricao.id);
            op.innerHTML = inscricao.nome;
            dentroDaLista.appendChild(op);

            const asdf = document.createElement('input');
            asdf.setAttribute('name', 'EncontroPresencial[alunos][]')
            asdf.setAttribute('value', inscricao.id);
            asdf.setAttribute('type', 'hidden');
            alunosSelecionados.appendChild(asdf);
        });
    }

    function adicionarALista() {
        const foraDaLista = document.getElementById('fora-da-lista');
        const selecionados = foraDaLista.selectedOptions;
        for (let option of selecionados) {
            const inscricaoSelecionada = inscricoes.find(function(i) {
                return i.id == option.value;
            });
            inscricaoSelecionada.dentro = true;
        }
        atualizarListas();
    }

    function removerDaLista() {
        const dentroDaLista = document.getElementById('dentro-da-lista');
        const selecionados = dentroDaLista.selectedOptions;
        for (let option of selecionados) {
            const inscricaoSelecionada = inscricoes.find(function(i) {
                return i.id == option.value;
            });
            inscricaoSelecionada.dentro = false;
        }
        atualizarListas();
    }

    function estaDentroDoFiltro(inscricao, nomeFiltro) {
        const filtro = document.getElementById('filtro-' + nomeFiltro).value.toLowerCase();
        if (filtro.trim() == '') return true;
        
        return inscricao.nome.toLowerCase().includes(filtro)
            || inscricao.cpf.includes(filtro)
            || (inscricao.numero_ufscar && inscricao.numero_ufscar.includes(filtro));
    }

    atualizarListas();

</script>