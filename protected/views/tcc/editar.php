<?php
/* @var $this AlunoController */
/* @var $tcc Tcc */
/* @var $habilitacao Habilitacao */
/* @var $listaDocentes */

$this->breadcrumbs = [
    'TCC' => ['/aluno/tcc'],
    'Editar',
    $tcc->titulo,
];
?>

<h1>Editar TCC</h1>
<br>

<?php $this->renderPartial('/tcc/_formularioTccTeste', array(
    'tcc' => $tcc,
    'habilitacao' => $habilitacao,
    'listaDocentes' => $listaDocentes,
    'ehCoordenacao' => $ehCoordenacao,
)); ?>
