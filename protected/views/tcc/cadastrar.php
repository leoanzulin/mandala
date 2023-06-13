<?php
/* @var $this AlunoController */
/* @var $tcc Tcc */
/* @var $habilitacao Habilitacao */
/* @var $listaDocentes */

$this->breadcrumbs = [
    'TCC' => ['/aluno/tcc'],
    'Cadastrar'
];
?>

<h1>Cadastrar TCC</h1>
<br>

<?php $this->renderPartial('/tcc/_formularioTcc', array(
    'tcc' => $tcc,
    'habilitacao' => $habilitacao,
    'listaDocentes' => $listaDocentes,
    'ehCoordenacao' => $ehCoordenacao ?? false,
)); ?>
