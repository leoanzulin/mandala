<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

?>

<fieldset style="text-align: center">

<h1>Acesso</h1><br />

<p>Por favor, informe seu CPF e sua senha: </p><br />

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
	<div style="margin-left: 20px; margin-bottom: 20px;">
		<?php echo $form->error($model,'username'); ?>
		<?php echo $form->error($model,'password'); ?>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	<div id="login">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
	</div>

	<div id="login">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
	</div>

	<div id="login" style="margin-bottom:20px;">
		<?php echo $form->checkBox($model,'rememberMe'); ?>
		<?php echo $form->label($model,'rememberMe'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Acessar o Sistema'); ?>
	</div>
    
<?php $this->endWidget(); ?>

    <p><?php echo CHtml::link('Esqueci minha senha', array('esqueciSenha')) ?></p>

</div>
</fieldset><!-- form -->
