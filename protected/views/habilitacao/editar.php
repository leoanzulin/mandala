<?php
echo CHtml::link('Voltar para lista de habilitações', array('gerenciar'));

Yii::app()->clientScript->registerScript('scripts_locais', "
    function updateColorPicker() {
        $('#color_picker').css('background-color', $('#Habilitacao_cor').val());
    };
    $('#Habilitacao_cor').on('input', function() {
        updateColorPicker();
    });
    updateColorPicker();
");
?>

<h1>Editar habilitação <?php echo $model->nome ?></h1>

<div class="form">

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'componente-form',
    'enableAjaxValidation' => false,
));
?>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->label($model, 'nome'); ?>
        <?php echo $form->textField($model, 'nome', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'nome'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'letra'); ?>
        <?php echo $form->textField($model, 'letra', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'letra'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'cor'); ?>
        <?php echo $form->textField($model, 'cor', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'cor'); ?>
    </div>

    <div class="row">
        <label></label>
        <div id="color_picker" style="float: left; width: 24px; height: 24px; border: 1px solid #999; margin-bottom: 20px"></div>
    </div>

    <div class="row buttons">
        <label></label><?php echo CHtml::submitButton('Salvar'); ?>
    </div>

<?php $this->endWidget(); ?>

</div>
