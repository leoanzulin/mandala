<?php
$this->breadcrumbs = array(
    'Envio de documentos',
);
?>

<h1>Envio de documentos para inscrição (CPF <?php echo $model->cpf; ?>)</h1>

<p>Instruções:</p>
<ul>
    <li>Apenas <b>cópias simples</b> dos documentos são necessárias</li>
    <li>Formatos aceitos: PDF, JPG, GIF, PNG</li>
    <li>Para o currículo: DOC, DOCX, PDF, JPG, GIF, PNG</li>
    <li>Justificativa: Opcional, em caso de pedido de bolsa, o documento deve ser escrito de próprio punho</li>
    <li>Justificativa de próprio punho e documentos comprobatórios (holerites, declarações etc.) em um único arquivo (ZIP).</li>
    <li>Tamanho máximo dos arquivos: 2MB</li>
</ul>
<br>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id' => 'inscricao-form',
	'enableAjaxValidation' => false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'documento_cpf'); ?>
        <?php echo $form->fileField($model, 'documento_cpf'); ?>
        <?php echo $form->error($model, 'documento_cpf'); ?>
    </div>
    <br>

    <div class="row">
        <?php echo $form->labelEx($model, 'documento_rg'); ?>
        <?php echo $form->fileField($model, 'documento_rg'); ?>
        <?php echo $form->error($model, 'documento_rg'); ?>
    </div>
    <br>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'documento_diploma'); ?>
        <?php echo $form->fileField($model, 'documento_diploma'); ?>
        <?php echo $form->error($model, 'documento_diploma'); ?>
    </div>
    <br>

    <div class="row">
        <?php echo $form->labelEx($model, 'documento_comprovante_residencia'); ?>
        <?php echo $form->fileField($model, 'documento_comprovante_residencia'); ?>
        <?php echo $form->error($model, 'documento_comprovante_residencia'); ?>
    </div>
    <br>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'documento_curriculo'); ?>
        <?php echo $form->fileField($model, 'documento_curriculo'); ?>
        <?php echo $form->error($model, 'documento_curriculo'); ?>
    </div>
    <br>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'documento_justificativa'); ?>
        <?php echo $form->fileField($model, 'documento_justificativa'); ?>
        <?php echo $form->error($model, 'documento_justificativa'); ?>
    </div>
    <br>

	<div class="row buttons">
        <label></label>
		<?php echo CHtml::submitButton('Enviar'); ?>
	</div>

<?php $this->endWidget(); ?>