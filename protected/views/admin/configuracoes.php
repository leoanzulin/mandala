<?php
$meses = array();
for ($i = 1; $i <= 12; $i++) {
    $meses[$i] = $i;
}

$anos = array();
for ($i = 2016; $i <= 2018; $i++) {
    $anos[$i] = $i;
}

?>

<h1>Configurações do sistema</h1>
<br>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'configuracao-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <div class="row">
        <?php echo $form->label($model, 'inicioPeriodo'); ?>
        <?php echo $form->dropDownList($model, 'mesInicio', $meses, array('class' => 'muito-curto')); ?>
        <?php echo $form->dropDownList($model, 'anoInicio', $anos, array('class' => 'muito-curto')); ?>
        <?php echo $form->error($model, 'mesInicio'); ?>
        <?php echo $form->error($model, 'anoInicio'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'fimPeriodo'); ?>
        <?php echo $form->dropDownList($model, 'mesFim', $meses, array('class' => 'muito-curto')); ?>
        <?php echo $form->dropDownList($model, 'anoFim', $anos, array('class' => 'muito-curto')); ?>
        <?php echo $form->error($model, 'mesFim'); ?>
        <?php echo $form->error($model, 'anoFim'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($model, 'turma'); ?>
        <?php echo $form->numberField($model, 'turma', array('type' => 'number')); ?>
        <?php echo $form->error($model, 'turma'); ?>
    </div>

    <div class="row buttons">
        <label></label>
    <?php
    echo CHtml::submitButton('Salvar', array(
        'onClick' => 'js:return validar();',
    ));
    ?>
    </div>

<?php $this->endWidget(); ?>

</div>
