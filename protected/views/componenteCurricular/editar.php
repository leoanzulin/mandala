<?php
echo CHtml::link('Voltar para lista de componentes', array('gerenciar'));
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
    $prioridades = $model->prioridades();
    foreach ($prioridades as $prioridade) {
?>
    <div class="row">
        <label for="ComponenteCurricular_prioridade<?php echo $prioridade['habilitacao_id']; ?>"><?php echo $prioridade['habilitacao_nome']; ?></label>
        <select id="ComponenteCurricular_prioridade<?php echo $prioridade['habilitacao_id']; ?>" name="ComponenteCurricular[prioridades][<?php echo $prioridade['habilitacao_id']; ?>]">
            <option value="<?php echo Constantes::LETRA_PRIORIDADE_NECESESARIA; ?>" <?php if ($prioridade['prioridade'] == Constantes::PRIORIDADE_NECESSARIA) echo 'selected'; ?>><?php echo Constantes::LETRA_PRIORIDADE_NECESESARIA; ?></option>
            <option value="<?php echo Constantes::LETRA_PRIORIDADE_OPTATIVA; ?>" <?php if ($prioridade['prioridade'] == Constantes::PRIORIDADE_OPTATIVA) echo 'selected'; ?>><?php echo Constantes::LETRA_PRIORIDADE_OPTATIVA; ?></option>
            <option value="<?php echo Constantes::LETRA_PRIORIDADE_LIVRE; ?>" <?php if ($prioridade['prioridade'] == Constantes::PRIORIDADE_LIVRE) echo 'selected'; ?>><?php echo Constantes::LETRA_PRIORIDADE_LIVRE; ?></option>
        </select>
    </div>
<?php
    }
?>

<div class="row buttons">
    <label></label>
    <?php echo CHtml::submitButton('Salvar'); ?>
</div>

<?php if ($model->ativo) { ?>

    <div class="row" style="margin-top: 30px">
        <label></label>
        <?php echo CHtml::submitButton('Desativar componente', [
            'class' => 'btn btn-danger btn-lg',
            'name' => 'excluir',
            'onClick' => 'js:return confirm("Tem certeza que deseja desativar este componente?");',
        ]); ?>
    </div>

<?php } ?>

<?php $this->endWidget(); ?>


<h2>Lista de alunos deste componente</h2>

<?php

if (empty($model->ofertas)) {
    echo "<p>Este componente nunca foi ofertado</p>";
} else {
    foreach ($model->ofertas as $oferta) {
        echo "<p style=\"font-size: 1.5em\">Oferta de {$oferta->mes}/{$oferta->ano} (" . count($oferta->inscricoes) . " alunos)</p>";
        echo "<ul>";
        foreach ($oferta->inscricoes as $inscricao) {
            echo "<li>{$inscricao->nomeCompleto}</li>";
        };
        echo "</ul>";
    }
}
?>

</div>
