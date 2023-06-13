<?php
/* @var $this OfferController */
/* @var $offer Offer */
/* @var $discipline Discipline */
/* @var $form CActiveForm */

$this->breadcrumbs = array(
    'Cursos' => array('course/index'),
    $discipline->course->course_name => array('course/view', 'id' => $discipline->course_id),
    $discipline->discipline_name => array('discipline/view', 'id' => $discipline->discipline_id),
    'Criar nova oferta'
);
?>

<h1>Criar nova oferta para a disciplina '<?php echo $discipline->discipline_name; ?>'</h1>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'offer-create-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <?php echo $form->errorSummary($offer); ?>

    <div class="row">
        <?php echo $form->labelEx($offer, 'offer_teacher_id'); ?>
        <?php echo $form->dropDownList($offer, 'offer_teacher_id',
            CHtml::listData(Users::model()->teachers, 'user_id', 'user_firstname'),
            array('prompt' => 'Selecione um docente')
        ); ?>
        <?php echo $form->error($offer, 'offer_teacher_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($offer, 'offer_start_date'); ?>
        <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $offer,
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
        <?php echo $form->error($offer, 'offer_start_date'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($offer, 'offer_end_date'); ?>
        <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $offer,
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
        <?php echo $form->error($offer, 'offer_end_date'); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Submit'); ?>
    </div>

    <?php $this->endWidget(); ?>

</div><!-- form -->