<?php
/* @var $this InscricaoController */
/* @var $model Inscricao */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'inscricao-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'cpf'); ?>
		<?php echo $form->textField($model,'cpf',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'cpf'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nome'); ?>
		<?php echo $form->textField($model,'nome',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'nome'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sobrenome'); ?>
		<?php echo $form->textField($model,'sobrenome',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'sobrenome'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'data_nascimento'); ?>
		<?php echo $form->textField($model,'data_nascimento'); ?>
		<?php echo $form->error($model,'data_nascimento'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nome_mae'); ?>
		<?php echo $form->textField($model,'nome_mae',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'nome_mae'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nome_pai'); ?>
		<?php echo $form->textField($model,'nome_pai',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'nome_pai'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'estado_civil'); ?>
		<?php echo $form->textField($model,'estado_civil',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'estado_civil'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telefone_fixo'); ?>
		<?php echo $form->textField($model,'telefone_fixo',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'telefone_fixo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telefone_celular'); ?>
		<?php echo $form->textField($model,'telefone_celular',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'telefone_celular'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telefone_alternativo'); ?>
		<?php echo $form->textField($model,'telefone_alternativo',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'telefone_alternativo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cep'); ?>
		<?php echo $form->textField($model,'cep',array('size'=>8,'maxlength'=>8)); ?>
		<?php echo $form->error($model,'cep'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'endereco'); ?>
		<?php echo $form->textField($model,'endereco',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'endereco'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'numero'); ?>
		<?php echo $form->textField($model,'numero',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'numero'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'complemento'); ?>
		<?php echo $form->textField($model,'complemento',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'complemento'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cidade'); ?>
		<?php echo $form->textField($model,'cidade',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'cidade'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'estado'); ?>
		<?php echo $form->textField($model,'estado',array('size'=>2,'maxlength'=>2)); ?>
		<?php echo $form->error($model,'estado'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cargo_atual'); ?>
		<?php echo $form->textField($model,'cargo_atual',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'cargo_atual'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'empresa'); ?>
		<?php echo $form->textField($model,'empresa',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'empresa'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telefone_comercial'); ?>
		<?php echo $form->textField($model,'telefone_comercial',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'telefone_comercial'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'forma_pagamento'); ?>
		<?php echo $form->textField($model,'forma_pagamento',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'forma_pagamento'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'candidato_bolsa'); ?>
		<?php echo $form->checkBox($model,'candidato_bolsa'); ?>
		<?php echo $form->error($model,'candidato_bolsa'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'curso_id'); ?>
		<?php echo $form->textField($model,'curso_id'); ?>
		<?php echo $form->error($model,'curso_id'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->