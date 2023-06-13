<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'colaborador-form',
        'enableAjaxValidation' => false,
    ));
    ?>
    <?php echo $form->errorSummary($colaborador); ?>

    <div class="row">
        <?php echo $form->labelEx($colaborador, 'cpf'); ?>
        <?php echo $form->textField($colaborador, 'cpf', array('size' => 11, 'maxlength' => 11, 'class' => 'curto')); ?>
        <?php echo $form->error($colaborador, 'cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($colaborador, 'nome'); ?>
        <?php echo $form->textField($colaborador, 'nome', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($colaborador, 'nome'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($colaborador, 'sobrenome'); ?>
        <?php echo $form->textField($colaborador, 'sobrenome', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($colaborador, 'sobrenome'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($colaborador, 'email'); ?>
        <?php echo $form->textField($colaborador, 'email', array('size' => 256, 'maxlength' => 256)); ?>
        <?php echo $form->error($colaborador, 'email'); ?>
    </div>

    <div class="row buttons" style="margin-top: 30px">
        <label></label>
        <?php echo CHtml::submitButton('Salvar', [
            'class' => 'btn btn-success btn-lg',
            'name' => 'salvar',
        ]); ?>
    </div>
    <?php if (!$colaborador->isNewRecord) { ?>
        <div class="row buttons" style="margin-top: 10px">
            <label></label>
            <?php echo CHtml::submitButton('Excluir colaborador', [
                'class' => 'btn btn-danger btn-lg',
                'name' => 'excluir',
                'onClick' => 'js:return confirm("Tem certeza que deseja excluir este colaborador?");',
            ]); ?>
        </div>
    <?php } ?>

    <?php $this->endWidget(); ?>

</div>