<?php
/* @var $this EnrollmentController */
/* @var $model Enrollment */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'enrollment-admin-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'course_id'); ?>
		<?php echo $form->textField($model,'course_id'); ?>
		<?php echo $form->error($model,'course_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_document'); ?>
		<?php echo $form->textField($model,'enr_document'); ?>
		<?php echo $form->error($model,'enr_document'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_firstname'); ?>
		<?php echo $form->textField($model,'enr_firstname'); ?>
		<?php echo $form->error($model,'enr_firstname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_lastname'); ?>
		<?php echo $form->textField($model,'enr_lastname'); ?>
		<?php echo $form->error($model,'enr_lastname'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_email'); ?>
		<?php echo $form->textField($model,'enr_email'); ?>
		<?php echo $form->error($model,'enr_email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_phone'); ?>
		<?php echo $form->textField($model,'enr_phone'); ?>
		<?php echo $form->error($model,'enr_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_mobile'); ?>
		<?php echo $form->textField($model,'enr_mobile'); ?>
		<?php echo $form->error($model,'enr_mobile'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_zipcode'); ?>
		<?php echo $form->textField($model,'enr_zipcode'); ?>
		<?php echo $form->error($model,'enr_zipcode'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_address'); ?>
		<?php echo $form->textField($model,'enr_address'); ?>
		<?php echo $form->error($model,'enr_address'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_complement'); ?>
		<?php echo $form->textField($model,'enr_complement'); ?>
		<?php echo $form->error($model,'enr_complement'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_city'); ?>
		<?php echo $form->textField($model,'enr_city'); ?>
		<?php echo $form->error($model,'enr_city'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_state'); ?>
		<?php echo $form->textField($model,'enr_state'); ?>
		<?php echo $form->error($model,'enr_state'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_formation'); ?>
		<?php echo $form->textField($model,'enr_formation'); ?>
		<?php echo $form->error($model,'enr_formation'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_formation_area'); ?>
		<?php echo $form->textField($model,'enr_formation_area'); ?>
		<?php echo $form->error($model,'enr_formation_area'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_siape'); ?>
		<?php echo $form->textField($model,'enr_siape'); ?>
		<?php echo $form->error($model,'enr_siape'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_date'); ?>
		<?php echo $form->textField($model,'enr_date'); ?>
		<?php echo $form->error($model,'enr_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_public_server'); ?>
		<?php echo $form->textField($model,'enr_public_server'); ?>
		<?php echo $form->error($model,'enr_public_server'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'enr_payment'); ?>
		<?php echo $form->textField($model,'enr_payment'); ?>
		<?php echo $form->error($model,'enr_payment'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->