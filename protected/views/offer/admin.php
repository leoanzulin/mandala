<?php
/* @var $this OfferController */
/* @var $model Offer */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'offer-admin-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'offer_discipline_id'); ?>
		<?php echo $form->textField($model,'offer_discipline_id'); ?>
		<?php echo $form->error($model,'offer_discipline_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'offer_teacher_id'); ?>
		<?php echo $form->textField($model,'offer_teacher_id'); ?>
		<?php echo $form->error($model,'offer_teacher_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'offer_start_date'); ?>
		<?php echo $form->textField($model,'offer_start_date'); ?>
		<?php echo $form->error($model,'offer_start_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'offer_end_date'); ?>
		<?php echo $form->textField($model,'offer_end_date'); ?>
		<?php echo $form->error($model,'offer_end_date'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->