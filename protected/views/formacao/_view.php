<?php
/* @var $this FormacaoController */
/* @var $data Formacao */
?>

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('nivel')); ?>:</b>
	<?php echo CHtml::encode($data->nivel); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('curso')); ?>:</b>
	<?php echo CHtml::encode($data->curso); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ano_conclusao')); ?>:</b>
	<?php echo CHtml::encode($data->ano_conclusao); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('instituicao')); ?>:</b>
	<?php echo CHtml::encode($data->instituicao); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('inscricao_id')); ?>:</b>
	<?php echo CHtml::encode($data->inscricao_id); ?>
	<br />


</div>