<?php
echo CHtml::link('Voltar para lista de componentes', array('gerenciar'));
?>

<h1>Cadastrar nova componente curricular</h1>

<div class="form">

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'componente-form',
    'enableAjaxValidation' => false,
));
?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

<?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'nome'); ?>
        <?php echo $form->textField($model, 'nome', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'nome'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'carga_horaria'); ?>
        <?php echo $form->textField($model, 'carga_horaria', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($model, 'carga_horaria'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'ementa'); ?>
        <?php echo $form->textArea($model, 'ementa', array(
            'maxlength' => 1048576, 'rows' => 6, 'cols' => 100
        )); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'bibliografia'); ?>
        <?php echo $form->textArea($model, 'bibliografia', array(
            'maxlength' => 1048576, 'rows' => 6, 'cols' => 100
        )); ?>
    </div>

<h2>Prioridades por habilitação</h2>
<?php
    foreach ($habilitacoes as $habilitacao) {
?>
    <div class="row">
        <label for="ComponenteCurricular_prioridade<?php echo $habilitacao->id; ?>"><?php echo $habilitacao->nome; ?></label>
        <select id="ComponenteCurricular_prioridade<?php echo $habilitacao->id; ?>" name="ComponenteCurricular[prioridades][<?php echo $habilitacao->id; ?>]">
            <option value="<?php echo Constantes::LETRA_PRIORIDADE_NECESESARIA; ?>"><?php echo Constantes::LETRA_PRIORIDADE_NECESESARIA; ?></option>
            <option value="<?php echo Constantes::LETRA_PRIORIDADE_OPTATIVA; ?>"><?php echo Constantes::LETRA_PRIORIDADE_OPTATIVA; ?></option>
            <option value="<?php echo Constantes::LETRA_PRIORIDADE_LIVRE; ?>" selected><?php echo Constantes::LETRA_PRIORIDADE_LIVRE; ?></option>
        </select>
    </div>
<?php
    }
?>

    <div class="row buttons">
        <label></label><?php echo CHtml::submitButton('Salvar'); ?>
    </div>

<?php $this->endWidget(); ?>

</div>
