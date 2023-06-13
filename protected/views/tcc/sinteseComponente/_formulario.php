<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'sintese-componente-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>
    <?php echo $form->errorSummary($sinteseComponente); ?>

    <div class="row">
        <?php echo $form->labelEx($sinteseComponente, 'componente_curricular_id'); ?>
        <?php
        echo $form->dropDownList(
            $sinteseComponente,
            'componente_curricular_id',
            $listaComponentes,
            ['empty' => 'Escolha o componente curricular']
        );
        ?>
        <?php echo $form->error($sinteseComponente, 'componente_curricular_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($sinteseComponente, 'descricao'); ?>
        <?php echo $form->textArea($sinteseComponente, 'descricao', ['maxlength' => 3000, 'rows' => 10, 'cols' => 200]); ?>
        <?php echo $form->error($sinteseComponente, 'descricao'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($sinteseComponente, 'reflexao'); ?>
        <?php echo $form->textArea($sinteseComponente, 'reflexao', ['maxlength' => 3000, 'rows' => 10, 'cols' => 200]); ?>
        <?php echo $form->error($sinteseComponente, 'reflexao'); ?>
    </div>

    <div class="row buttons" style="margin-top: 30px">
        <label></label>
        <?php echo CHtml::submitButton('Salvar', [
            'class' => 'btn btn-success btn-lg',
            'name' => 'salvar',
        ]); ?>
    </div>
    <?php if (!$sinteseComponente->isNewRecord) { ?>
        <div class="row buttons" style="margin-top: 10px">
            <label></label>
            <?php echo CHtml::submitButton('Excluir síntese', [
                'class' => 'btn btn-danger btn-lg',
                'name' => 'excluir',
                'onClick' => 'js:return confirm("Tem certeza que deseja excluir esta síntese?");',
            ]); ?>
        </div>
    <?php } ?>

    <?php $this->endWidget(); ?>

</div>