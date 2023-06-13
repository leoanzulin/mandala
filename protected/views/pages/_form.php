<?php
/* @var $this PagesController */
/* @var $model Pages */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pages-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'page_name'); ?>
		<?php echo $form->textField($model,'page_name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'page_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'page_shortname'); ?>
		<?php echo $form->textField($model,'page_shortname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'page_shortname'); ?>
	</div>
    
	<div class="row">
		<?php echo $form->labelEx($model,'page_description'); ?>
        <?php // echo $form->textArea($model,'page_description',array('rows'=>6, 'cols'=>50)); ?>
        <?php
        $this->widget('application.extensions.tinymce.ETinyMce', array(
            'model' => $model,
            'attribute' => "page_description",
            'editorTemplate' => 'full',
            'useSwitch' => false,
            'htmlOptions' => array('rows' => 6, 'cols' => 30, 'style' => 'width:600px; height:400px;'),
            'options' => array(
                'theme_advanced_buttons1' => 'undo,redo,|,bold,italic,underline,|,forecolor,backcolor,emoticons,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,',
                'theme_advanced_buttons2' => 'bullist,numlist,|,hr,|,link,image,|,code,',
                'theme_advanced_buttons3' => '',
                'theme_advanced_buttons4' => '',
                'theme_advanced_toolbar_location' => 'top',
                'theme_advanced_statusbar_location' => 'none',
                'theme_advanced_path_location' => 'none',
                'theme_advanced_fonts' => "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Century Gothic=century gothic;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
            )
        ));
        ?>

		<?php echo $form->error($model,'page_description'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Criar' : 'Salvar'); ?>
	</div>
    
<?php $this->endWidget(); ?>

</div>
