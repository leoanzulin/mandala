<?php
/* @var $this OfferController */
/* @var $offer Offer */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'offer-_form-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($offer); ?>

	<div class="row">
		<?php echo $form->labelEx($offer,'offer_discipline_id'); ?>
		<?php echo $form->textField($offer,'offer_discipline_id'); ?>
		<?php echo $form->error($offer,'offer_discipline_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($offer,'offer_teacher_id'); ?>
		<?php echo $form->textField($offer,'offer_teacher_id'); ?>
		<?php echo $form->error($offer,'offer_teacher_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($offer,'offer_start_date'); ?>
		<?php echo $form->textField($offer,'offer_start_date'); ?>
		<?php echo $form->error($offer,'offer_start_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($offer,'offer_end_date'); ?>
		<?php echo $form->textField($offer,'offer_end_date'); ?>
		<?php echo $form->error($offer,'offer_end_date'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->