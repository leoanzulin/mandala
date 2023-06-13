<?php
/* @var $this AlunoController */
/* @var $tcc Tcc */

$link = Yii::app()->user->isSuperuser === true ? 'admin' : 'aluno';
$this->breadcrumbs = [
    'TCC' => ["/{$link}/editarTcc", 'id' => $tcc->id],
    'Caracterização do especialista',
];
?>

<h1>Caracterização do especialista</h1>
<br>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'caracterizacao-especialista-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>
    <?php echo $form->errorSummary($tcc); ?>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'caracterizacao_especialista_perfil'); ?>
        <?php echo $form->textArea($tcc, 'caracterizacao_especialista_perfil',
            [
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Quem é esse especialista?',
            ]);
        ?>
        <?php echo $form->error($tcc, 'caracterizacao_especialista_perfil'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'caracterizacao_especialista_importancia'); ?>
        <?php echo $form->textArea($tcc, 'caracterizacao_especialista_importancia',
            [
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Em que esse especialista contribui?',
            ]);
        ?>
        <?php echo $form->error($tcc, 'caracterizacao_especialista_importancia'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'caracterizacao_especialista_saberes'); ?>
        <?php echo $form->textArea($tcc, 'caracterizacao_especialista_saberes',
            [
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'O que esse especialista deve saber para realizar suas atividades com qualidade?',
            ]);
        ?>
        <?php echo $form->error($tcc, 'caracterizacao_especialista_saberes'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'caracterizacao_especialista_atividades'); ?>
        <?php echo $form->textArea($tcc, 'caracterizacao_especialista_atividades',
            [
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Qual é o campo de atuação desse especialista?'
            ]);
        ?>
        <?php echo $form->error($tcc, 'caracterizacao_especialista_atividades'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'caracterizacao_especialista_desafios'); ?>
        <?php echo $form->textArea($tcc, 'caracterizacao_especialista_desafios',
            [
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Quais desafios ou dificultadores são normalmente enfrentados pelo especialista?'
            ]);
        ?>
        <?php echo $form->error($tcc, 'caracterizacao_especialista_desafios'); ?>
    </div>

    <div class="row buttons" style="margin-top: 30px">
        <label></label>
        <?php echo CHtml::submitButton('Salvar', [
            'class' => 'btn btn-success btn-lg',
            'name' => 'salvar',
        ]); ?>
    </div>

    <?php $this->endWidget(); ?>

</div>
