<?php
/* @var $this AdminController */
/* @var $tcc Tcc */
/* @var $listaDocentes */
/* @var $listaHabilitacoes */

$this->breadcrumbs = array(
    'Gerenciar TCC' => array('admin/gerenciarTccs'),
    'Cadastrar',
);
?>

<h1>Cadastrar TCC</h1>
<br>

<?php $this->renderPartial('_formularioTcc', array(
    'tcc' => $tcc,
    'listaDocentes' => $listaDocentes,
    'listaHabilitacoes' => $listaHabilitacoes,
)); ?>
