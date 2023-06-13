<?php

?>

<h1>Editar componentes</h1>
<table class="simulador">
    <thead><th>ID</th><th>Nome</th><th>G</th><th>D</th><th>M</th><th>T</th><th>P</th><th>Carga</th></thead>
    <tbody>
<?php foreach ($componentes as $componente) {
    $prioridades = $componente->prioridades();
    echo "<tr>"
            . "<td>{$componente->id}</td>"
            . '<td>' . CHtml::link($componente->nome, array('admin/editarComponente', 'id' => $componente->id)) . '</td>'
            . '<td class="' . $prioridades['classes'][3] . '">' . $prioridades['prioridadesLetra'][3] . '</td>'
            . '<td class="' . $prioridades['classes'][4] . '">' . $prioridades['prioridadesLetra'][4] . '</td>'
            . '<td class="' . $prioridades['classes'][1] . '">' . $prioridades['prioridadesLetra'][1] . '</td>'
            . '<td class="' . $prioridades['classes'][2] . '">' . $prioridades['prioridadesLetra'][2] . '</td>'
            . '<td class="' . $prioridades['classes'][5] . '">' . $prioridades['prioridadesLetra'][5] . '</td>'
            . '<td style="text-align: center">' . $componente->carga_horaria . '</td>'
. '</tr>';
} ?>
</tbody>
</table>
