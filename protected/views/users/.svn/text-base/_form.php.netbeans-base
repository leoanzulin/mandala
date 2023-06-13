<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */

$roles = Rights::getAuthItemSelectOptions('2', array('Admin', 'Guest'));
?>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'users-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <?php echo $form->errorSummary($model); ?>

    <?php if ($model->isNewReCord) { ?>
    <div class="row">
        <?php echo $form->labelEx($model, 'user_id'); ?>
        <?php echo $form->textField($model, 'user_id', array('size' => 11, 'maxlength' => 11)); ?>
        <?php echo $form->error($model, 'user_id'); ?>
    </div>
    <?php } ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'user_password'); ?>
        <?php echo $form->passwordField($model, 'user_password', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'user_password'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'user_password_confirm'); ?>
        <?php echo $form->passwordField($model, 'user_password_confirm', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'user_password_confirm'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'user_firstname'); ?>
        <?php echo $form->textField($model, 'user_firstname', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'user_firstname'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'user_lastname'); ?>
        <?php echo $form->textField($model, 'user_lastname', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'user_lastname'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'user_email'); ?>
        <?php echo $form->textField($model, 'user_email', array('size' => 60, 'maxlength' => 255)); ?>
        <?php echo $form->error($model, 'user_email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'user_roles'); ?>
        <?php echo $form->checkBoxList($model, 'user_roles', $roles['Roles'],
            array('labelOptions' => array('style'=>'display:inline'))
        ); ?>
        <?php echo $form->error($model, 'user_roles'); ?>
    </div>
    
    <div class="row buttons">
        <?php echo CHtml::submitButton($model->isNewRecord ? 'Adicionar' : 'Atualizar'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->