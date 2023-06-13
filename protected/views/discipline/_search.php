<?php
/* @var $this DisciplineController */
/* @var $model Discipline */
/* @var $form CActiveForm */
?>

<div class="wide form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    ));
    ?>

    <div class="row">
        <?php echo $form->label($model, 'discipline_id'); ?>
        <?php echo $form->textField($model, 'discipline_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'course_id'); ?>
        <?php echo $form->textField($model, 'course_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'discipline_name'); ?>
        <?php echo $form->textField($model, 'discipline_name', array('size' => 60, 'maxlength' => 255)); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'menu'); ?>
        <?php echo $form->textArea($model, 'menu', array('rows' => 6, 'cols' => 50)); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'workload'); ?>
        <?php echo $form->textField($model, 'workload'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'objective'); ?>
        <?php echo $form->textArea($model, 'objective', array('rows' => 6, 'cols' => 50)); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Search'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
