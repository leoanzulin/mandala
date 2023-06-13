<?php
/* @var $this OfferStudentController */
/* @var $model OfferStudent */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'offer-student-_form-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'os_offer_discipline_id'); ?>
		<?php echo $form->textField($model,'os_offer_discipline_id'); ?>
		<?php echo $form->error($model,'os_offer_discipline_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'os_offer_teacher_id'); ?>
		<?php echo $form->textField($model,'os_offer_teacher_id'); ?>
		<?php echo $form->error($model,'os_offer_teacher_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'os_offer_start_date'); ?>
		<?php echo $form->textField($model,'os_offer_start_date'); ?>
		<?php echo $form->error($model,'os_offer_start_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'os_offer_end_date'); ?>
		<?php echo $form->textField($model,'os_offer_end_date'); ?>
		<?php echo $form->error($model,'os_offer_end_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'os_student_id'); ?>
		<?php echo $form->textField($model,'os_student_id'); ?>
		<?php echo $form->error($model,'os_student_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'os_grade'); ?>
		<?php echo $form->textField($model,'os_grade'); ?>
		<?php echo $form->error($model,'os_grade'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'os_frequency'); ?>
		<?php echo $form->textField($model,'os_frequency'); ?>
		<?php echo $form->error($model,'os_frequency'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->