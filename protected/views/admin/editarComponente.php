<?php
echo CHtml::link('Voltar para lista de componentes', array('admin/gerenciarComponentes'));
?>

<h1>Editar componente <?php echo $model->nome ?></h1>

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
    $idsHabilitacoes = array(3, 4, 1, 2, 5);
    $prioridades = $model->prioridades();
    foreach ($idsHabilitacoes as $id) {
        $habilitacao = Habilitacao::model()->findByPk($id);
?>

    <div class="row">
        <label for="ComponenteCurricular_prioridade<?php echo $id; ?>"><?php echo $habilitacao->nome; ?></label>
        <select id="ComponenteCurricular_prioridade<?php echo $id; ?>" name="ComponenteCurricular[prioridade<?php echo $id; ?>]">
            <option value="N"<?php if ($prioridades['prioridades'][$id] == 0) echo ' selected'; ?>>N</option>
            <option value="O"<?php if ($prioridades['prioridades'][$id] == 1) echo ' selected'; ?>>O</option>
            <option value="L"<?php if ($prioridades['prioridades'][$id] == 2) echo ' selected'; ?>>L</option>
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
