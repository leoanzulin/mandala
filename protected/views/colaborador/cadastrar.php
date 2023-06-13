<?php
$this->breadcrumbs = [
    'Colaboradores' => ['/colaborador/gerenciar'],
    'Cadastrar'
];
?>

<h1>Cadastrar colaborador</h1>
<br>

<?php $this->renderPartial('/colaborador/_formulario', array(
    'colaborador' => $colaborador,
)); ?>
