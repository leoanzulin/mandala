<?php
Yii::app()->clientScript->registerScript('scripts_locais', "
    // Acopla a máscara ao campo de telefone
    mascara_celular('#Tutor_telefone');
");
?>

<h1>Cadastrar novo tutor</h1>

<div class="form">

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'tutor-form',
    'enableAjaxValidation' => false,
));
?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <div class="row">
        <?php echo $form->labelEx($model, 'cpf'); ?>
        <?php echo $form->textField($model, 'cpf', array('size' => 11, 'maxlength' => 11, 'class' => 'curto')); ?>
        <?php echo $form->error($model, 'cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'nome'); ?>
        <?php echo $form->textField($model, 'nome', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'nome'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'sobrenome'); ?>
        <?php echo $form->textField($model, 'sobrenome', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'sobrenome'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'email'); ?>
        <?php echo $form->textField($model, 'email', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'telefone'); ?>
        <?php echo $form->textField($model, 'telefone', array('size' => 256, 'maxlength' => 256, 'class' => 'curto')); ?>
        <?php echo $form->error($model, 'telefone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'endereco'); ?>
        <?php echo $form->textField($model, 'endereco', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'endereco'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'numero'); ?>
        <?php echo $form->textField($model, 'numero', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'numero'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'bairro'); ?>
        <?php echo $form->textField($model, 'bairro', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'bairro'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'complemento'); ?>
        <?php echo $form->textField($model, 'complemento', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'complemento'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'cep'); ?>
        <?php $this->widget('CMaskedTextField', array(
            'model' => $model,
            'attribute' => 'cep',
            'mask' => '99999-999',
            'htmlOptions' => array('size' => 9, 'maxlength' => 9, 'class' => 'curto'),
        )); ?>
        <?php echo $form->error($model, 'cep'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'mestrando_ou_doutorando_ufscar'); ?>
        <?php echo $form->checkBox($model, 'mestrando_ou_doutorando_ufscar'); ?>
        <?php echo $form->error($model, 'mestrando_ou_doutorando_ufscar'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'valor_bolsa'); ?>
        <?php echo $form->textField($model, 'valor_bolsa', array('size' => 256, 'maxlength' => 256, 'class' => 'curto')); ?>
        <?php echo $form->error($model, 'valor_bolsa'); ?>
    </div>

    <div class="row buttons">
        <label></label><?php echo CHtml::submitButton('Salvar'); ?>
    </div>

<?php $this->endWidget(); ?>

</div>
