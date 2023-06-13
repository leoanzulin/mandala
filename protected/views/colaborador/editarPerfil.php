<?php
/* @var $this ColaboradorController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = [
    'Meu perfil' => ['colaborador/perfil'],
    'Editar perfil',
];
?>

<h1>Editar perfil</h1>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id' => 'editar-perfil-form',
        'enableAjaxValidation' => false,
    ]);
    ?>

    <?php echo $form->errorSummary($model); ?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <fieldset><legend>Dados pessoais</legend>

        <div class="row">
            <?php echo $form->label($model, 'cpf'); ?>
            <p><?php echo $model->cpf; ?></p>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'nome'); ?>
            <p><?php echo $model->nome; ?></p>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'sobrenome'); ?>
            <p><?php echo $model->sobrenome; ?></p>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'email'); ?>
            <?php echo $form->textField($model, 'email'); ?>
            <?php echo $form->error($model, 'email'); ?>
        </div>

<?php if ($model instanceof Docente || $model instanceof Tutor) { ?>

        <div class="row">
            <?php echo $form->labelEx($model, 'telefone'); ?>
            <?php echo $form->textField($model, 'telefone', array('size' => 30, 'maxlength' => 30, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'telefone'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'endereco'); ?>
            <?php echo $form->textField($model, 'endereco', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'endereco'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'numero'); ?>
            <?php echo $form->textField($model, 'numero', array('size' => 30, 'maxlength' => 30, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'numero'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'bairro'); ?>
            <?php echo $form->textField($model, 'bairro', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'bairro'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'complemento'); ?>
            <?php echo $form->textField($model, 'complemento', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'complemento'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'cep'); ?>
            <?php
            $this->widget('CMaskedTextField', array(
                'model' => $model,
                'attribute' => 'cep',
                'mask' => '99999-999',
                'htmlOptions' => array('size' => 9, 'maxlength' => 9, 'class' => 'curto'),
            ));
            ?>
            <?php echo $form->error($model, 'cep'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'mestrando_ou_doutorando_ufscar'); ?>
            <?php echo $form->checkBox($model, 'mestrando_ou_doutorando_ufscar'); ?>
            <?php echo $form->error($model, 'mestrando_ou_doutorando_ufscar'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'titulo'); ?>
            <?php echo $form->textField($model, 'titulo', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'titulo'); ?>
        </div>

<?php } ?>

    </fieldset>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Salvar alterações'); ?>
    </div>

<?php $this->endWidget(); ?>
