<?php
/* @var $this AlunoController */
/* @var $aluno Inscricao */
/* @var $habilitacoes Habilitacao[] */

$this->breadcrumbs = ['TCC', 'Entrega', $tcc->titulo];

?>

<h1>Entrega de TCC: <?php echo $tcc->titulo; ?></h1>
<br>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'entregar-tcc-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>

<fieldset>
    <legend>Versão de validação</legend>

<div class="row">
    <?php echo $form->labelEx($tcc, 'validacao_arquivo'); ?>
    <?php echo $form->fileField($tcc, 'validacao_arquivo', ['disabled' => !$tcc->podeEntregarVersaoValidacao()]); ?>
    <?php echo $form->error($tcc, 'validacao_arquivo'); ?>
</div>

<?php if (!empty($tcc->validacao_arquivo)) { ?>
    <div class="row">
        <label>Arquivo</label>
        <a href="<?php echo $tcc->recuperarArquivoValidacao(); ?>" target="_blank">Visualizar</a>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'validacao_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'validacao_data_entrega', ['disabled' => true]); ?>
    </div>
<?php } ?>

</fieldset>

<?php if ($tcc->recuperarStatus() >= Tcc::FASE_APROVADO_PELO_PRE_ORIENTADOR) { ?>

<fieldset>
    <legend>Versão para banca</legend>

<div class="row">
    <?php echo $form->labelEx($tcc, 'banca_arquivo'); ?>
    <?php echo $form->fileField($tcc, 'banca_arquivo', ['disabled' => !$tcc->podeEntregarVersaoBanca()]); ?>
    <?php echo $form->error($tcc, 'banca_arquivo'); ?>
</div>

<?php if (!empty($tcc->banca_arquivo)) { ?>
    <div class="row">
        <label>Arquivo</label>
        <a href="<?php echo $tcc->recuperarArquivoBanca(); ?>" target="_blank">Visualizar</a>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'banca_data_entrega', ['disabled' => true]); ?>
    </div>
<?php } ?>

<div class="row">
    <?php echo $form->labelEx($tcc, 'banca_data_apresentacao'); ?>
    <?php echo $form->textField($tcc, 'banca_data_apresentacao', ['disabled' => true]); ?>
</div>

</fieldset>

<?php } ?>

<?php if ($tcc->recuperarStatus() >= Tcc::FASE_APROVADO_PELA_BANCA) { ?>

<fieldset>
    <legend>Versão final</legend>

<div class="row">
    <?php echo $form->labelEx($tcc, 'final_arquivo_doc'); ?>
    <?php echo $form->fileField($tcc, 'final_arquivo_doc', ['disabled' => !$tcc->podeEntregarVersaoFinal()]); ?>
    <?php echo $form->error($tcc, 'final_arquivo_doc'); ?>
</div>

<?php if (!empty($tcc->final_arquivo_doc)) { ?>
    <div class="row">
        <label>Arquivo</label>
        <a href="<?php echo $tcc->recuperarArquivoFinalDoc(); ?>" target="_blank">Visualizar</a>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'final_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'final_data_entrega', ['disabled' => true]); ?>
    </div>
<?php } ?>

<div class="row">
    <?php echo $form->labelEx($tcc, 'final_arquivo_pdf'); ?>
    <?php echo $form->fileField($tcc, 'final_arquivo_pdf', ['disabled' => !$tcc->podeEntregarVersaoFinal()]); ?>
    <?php echo $form->error($tcc, 'final_arquivo_pdf'); ?>
</div>

<?php if (!empty($tcc->final_arquivo_pdf)) { ?>
    <div class="row">
        <label>Arquivo</label>
        <a href="<?php echo $tcc->recuperarArquivoFinalPdf(); ?>" target="_blank">Visualizar</a>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'final_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'final_data_entrega', ['disabled' => true]); ?>
    </div>
<?php } ?>

</fieldset>

<?php } ?>

<label></label>
<?php echo CHtml::submitButton('Salvar', [
    'class' => 'btn btn-success btn-lg',
    'name' => 'salvar',
]); ?>

<?php $this->endWidget(); ?>

</div>
