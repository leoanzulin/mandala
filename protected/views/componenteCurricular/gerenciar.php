<h1>Gerenciar componentes</h1>

<?php echo CHtml::link('Cadastrar novo componente curricular', array('cadastrar')); ?>
<br><br>

<table class="simulador">
    <thead>
        <!-- <th>ID</th> -->
        <th>Nome</th>
        <?php foreach ($habilitacoes as $habilitacao) echo "<th>{$habilitacao->letra}</th>"; ?>
        <!-- <th>Carga</th> -->
    </thead>
    <tbody>
<?php foreach ($componentes as $componente) {
    $prioridades = $componente->prioridades();
    echo "<tr><!--<td>{$componente->id}</td>--><td>" . CHtml::link($componente->nome, array('editar', 'id' => $componente->id)) . '</td>';
    foreach ($prioridades as $prioridade) {
        echo '<td style="text-align: center; background-color: ' . $prioridade['cor'] . '">' . $prioridade['letra'] . '</td>';
    }
} ?>
    </tbody>
</table>

<h2>Componentes desativados</h2>

<table class="simulador">
    <thead>
        <!-- <th>ID</th> -->
        <th>Nome</th>
        <?php foreach ($habilitacoes as $habilitacao) echo "<th>{$habilitacao->letra}</th>"; ?>
        <!-- <th>Carga</th> -->
    </thead>
    <tbody>
<?php foreach ($componentesDesativados as $componente) {
    $prioridades = $componente->prioridades();
    echo "<tr><!--<td>{$componente->id}</td>--><td>" . CHtml::link($componente->nome, array('editar', 'id' => $componente->id)) . '</td>';
    foreach ($prioridades as $prioridade) {
        echo '<td style="text-align: center; background-color: ' . $prioridade['cor'] . '">' . $prioridade['letra'] . '</td>';
    }
} ?>
    </tbody>
</table>