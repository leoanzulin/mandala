<?php
/* @var $this OfferController */
/* @var $model Offer */
/* @var $form CActiveForm */

$this->breadcrumbs = array(
    'Cursos' => array('course/index'),
    $model->discipline->course->course_name => array('course/view', 'id' => $model->discipline->course_id),
    $model->discipline->discipline_name => array('discipline/view', 'id' => $model->discipline->discipline_id),
    'Editar oferta'
);
?>

<h1>Editando oferta da disciplina '<?php echo CHtml::encode($model->discipline->discipline_name) ?>'</h1>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'offer-update-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'offer_teacher_id'); ?>
        <?php echo $form->dropDownList($model, 'offer_teacher_id',
            CHtml::listData(Users::model()->teachers, 'user_id', 'user_firstname'),
            array('prompt' => 'Selecione um docente')
        ); ?>
        <?php echo $form->error($model, 'offer_teacher_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'offer_start_date'); ?>
        <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $model,
                'attribute' => 'offer_start_date',
                'options' => array(
                    'autoSize' => true,
                    'dateFormat' => 'dd/mm/yy',
                    'buttonImage' => Yii::app()->baseUrl . '/images/calendar.png',
                    'buttonImageOnly' => true,
                    'buttonText' => 'Selecione a data',
                    'showOn' => 'both',
                    'showButtonPanel' => true,
                ),
                'language' => 'pt',
            ));
        ?>
        <?php echo $form->error($model, 'offer_start_date'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'offer_end_date'); ?>
        <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $model,
                'attribute' => 'offer_end_date',
                'options' => array(
                    'autoSize' => true,
                    'dateFormat' => 'dd/mm/yy',
                    'buttonImage' => Yii::app()->baseUrl . '/images/calendar.png',
                    'buttonImageOnly' => true,
                    'buttonText' => 'Selecione a data',
                    'showOn' => 'both',
                    'showButtonPanel' => true,
                ),
                'language' => 'pt',
            ));
        ?>
        <?php echo $form->error($model, 'offer_end_date'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->