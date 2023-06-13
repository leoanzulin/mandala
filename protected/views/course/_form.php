<?php
/* @var $this CourseController */
/* @var $model Course */
/* @var $form CActiveForm */
?>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'course-form',
        'enableAjaxValidation' => false,
    ));

    ?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <div class="row">
        <?php echo $form->labelEx($model, 'course_name'); ?>
        <?php echo $form->textField($model, 'course_name', array('size' => 60, 'maxlength' => 500)); ?>
        <?php echo $form->error($model, 'course_name'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Salvar'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->