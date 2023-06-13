<?php

?>

<h1>Editar encontro presencial</h1>

<br>
<?php echo CHtml::link('Exportar lista de presença', ['/exportador/listaDePresenca', 'id' => $model->id], [ 'style' => 'font-size: 20px' ] ); ?>
<br><br>
<?php echo CHtml::link('Marcar presença de alunos', ['/encontro/marcarPresenca', 'id' => $model->id], [ 'style' => 'font-size: 20px' ] ); ?>
<br><br><br>

<?php $this->renderPartial('_formulario', array(
    'model' => $model,
    'colaboradores' => $colaboradores,
    'inscricoes' => $inscricoes,
)); ?>
