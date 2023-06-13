<?php
/* @var $this AlunoController */
/* @var $tcc Tcc */
/* @var $habilitacao Habilitacao */
/* @var $listaDocentes */

$link = Yii::app()->user->isSuperuser === true ? 'admin' : 'aluno';
$this->breadcrumbs = [
    'TCC' => ["/{$link}/editarTcc", 'id' => $tcc->id],
    'Síntese de componente',
    'Cadastrar',
];
?>

<h1>Cadastrar síntese de componente</h1>
<br>

<?php $this->renderPartial('/tcc/sinteseComponente/_formulario', [
    'listaComponentes' => $listaComponentes,
    'sinteseComponente' => $sinteseComponente,
    'tcc' => $tcc,
    // 'habilitacao' => $habilitacao,
    // 'listaDocentes' => $listaDocentes,
]); ?>
