<?php
/* @var $this AlunoController */
/* @var $tcc Tcc */
/* @var $habilitacao Habilitacao */
/* @var $listaDocentes */

$link = Yii::app()->user->isSuperuser === true ? 'admin' : 'aluno';
$this->breadcrumbs = [
    'TCC' => ["/{$link}/editarTcc", 'id' => $tcc->id],
    'Proposta pedagógica com uso de TDIC',
    'Editar',
];
?>

<h1>Editar proposta pedagógica com uso de TDIC</h1>
<br>

<?php $this->renderPartial('/tcc/propostaPedagogica/_formulario', [
    'propostaPedagogica' => $propostaPedagogica,
    'tcc' => $tcc,
]); ?>
