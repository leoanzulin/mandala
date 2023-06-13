<?php
/* @var $this FormacaoController */
/* @var $model Formacao */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'formacao-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'nivel'); ?>
		<?php echo $form->textField($model,'nivel',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'nivel'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'curso'); ?>
		<?php echo $form->textField($model,'curso',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'curso'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ano_conclusao'); ?>
		<?php echo $form->textField($model,'ano_conclusao'); ?>
		<?php echo $form->error($model,'ano_conclusao'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'instituicao'); ?>
		<?php echo $form->textField($model,'instituicao',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'instituicao'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'inscricao_id'); ?>
		<?php echo $form->textField($model,'inscricao_id'); ?>
		<?php echo $form->error($model,'inscricao_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->