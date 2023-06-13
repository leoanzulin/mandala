<?php
/* @var $this AdminController */
/* @var $tcc Tcc */
/* @var $listaDocentes */
/* @var $listaTutores */
/* @var $listaHabilitacoes */

$this->breadcrumbs = array(
    'Gerenciar TCC' => array('admin/gerenciarTccs'),
    'Editar TCC',
    $tcc->titulo,
);
?>

<h1>Editar TCC</h1>
<br>

<?php $this->renderPartial('/tcc/_formularioTcc', array(
    'tcc' => $tcc,
    'habilitacao' => $tcc->habilitacao,
    'listaDocentes' => $listaDocentes,
    // 'listaTutores' => $listaTutores,
    'ehCoordenacao' => true,
)); ?>
