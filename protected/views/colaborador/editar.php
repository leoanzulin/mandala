<?php
$this->breadcrumbs = [
    'Colaboradores' => ['/colaborador/gerenciar'],
    'Editar',
    $colaborador->nomeCompleto,
];
?>

<h1>Editar colaborador</h1>
<br>

<?php $this->renderPartial('/colaborador/_formulario', array(
    'colaborador' => $colaborador,
)); ?>
