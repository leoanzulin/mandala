<?php
/* @var $this EnrollmentController */
/* @var $model Enrollment */
/* @var $form CActiveForm */

$this->breadcrumbs = array(
    'Fazer inscrição em cursos' => array('/enrollment'),
    $model->course->course_name,
);

Yii::app()->clientScript->registerScript('scripts_locais', "
    // Acopla a máscara ao campo de celular
    mascara_celular('#Enrollment_enr_mobile');

    $('#input_siape').hide();

    $('#Enrollment_enr_public_server_0').click(function(){
        $('#input_siape').hide();
    });
    $('#Enrollment_enr_public_server_1').click(function(){
        $('#input_siape').show();
    });
    
    $('#enrollment-create-form').bind('reset', function(){
        $('#input_siape').hide();
    });
");

?>

<h2>Fazer inscrição no curso '<?php echo $model->course->course_name ?>'</h2>

<!--<h3>Informações do curso</h3>-->

<h3>Formulário de inscrição</h3>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'enrollment-create-form',
        'enableAjaxValidation' => false,
    ));

    ?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <?php //echo $form->errorSummary($model);  ?>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_document'); ?>
        <p class="hint" style="font-size:smaller; color:#999">(Apenas números)</p>
        <?php echo $form->textField($model, 'enr_document'); ?>
        <?php echo $form->error($model, 'enr_document'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_firstname'); ?>
        <?php echo $form->textField($model, 'enr_firstname'); ?>
        <?php echo $form->error($model, 'enr_firstname'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_lastname'); ?>
        <?php echo $form->textField($model, 'enr_lastname'); ?>
        <?php echo $form->error($model, 'enr_lastname'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_email'); ?>
        <?php echo $form->textField($model, 'enr_email'); ?>
        <?php echo $form->error($model, 'enr_email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'email_confirmation'); ?>
        <?php echo $form->textField($model, 'email_confirmation'); ?>
        <?php echo $form->error($model, 'email_confirmation'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'enr_phone'); ?>
        <?php
        $this->widget('CMaskedTextField', array(
            'model' => $model,
            'attribute' => 'enr_phone',
            'mask' => '(99)9999-9999',
            'htmlOptions' => array('size' => 15, 'maxlength' => 15, 'placeholder' => '(__)____-____'),
        ));

        ?>
        <?php echo $form->error($model, 'enr_phone'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_mobile'); ?>
        <?php
        echo $form->textField($model, 'enr_mobile', array(
            'placeholder' => '(__)____-____'
        ));

        ?>
        <?php echo $form->error($model, 'enr_mobile'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_zipcode'); ?>
        <?php
        $this->widget('CMaskedTextField', array(
            'model' => $model,
            'attribute' => 'enr_zipcode',
            'mask' => '99999-999',
            'htmlOptions' => array('size' => 9, 'maxlength' => 9, 'placeholder' => '_____-___'),
        ));

        ?>
        <?php echo $form->error($model, 'enr_zipcode'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_address'); ?>
        <?php echo $form->textField($model, 'enr_address'); ?>
        <?php echo $form->error($model, 'enr_address'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_complement'); ?>
        <?php echo $form->textField($model, 'enr_complement'); ?>
        <?php echo $form->error($model, 'enr_complement'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'enr_city'); ?>
        <?php echo $form->textField($model, 'enr_city'); ?>
        <?php echo $form->error($model, 'enr_city'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_state'); ?>
        <?php
        echo $form->dropDownList(
            $model, 'enr_state', array(
                'AC' => 'AC', 'AL' => 'AL', 'AM' => 'AM', 'AP' => 'AP', 'BA' => 'BA',
                'CE' => 'CE', 'DF' => 'DF', 'ES' => 'ES', 'GO' => 'GO', 'MA' => 'MA',
                'MG' => 'MG', 'MS' => 'MS', 'MT' => 'MT', 'PA' => 'PA', 'PB' => 'PB',
                'PE' => 'PE', 'PI' => 'PI', 'PR' => 'PR', 'RJ' => 'RJ', 'RN' => 'RN',
                'RO' => 'RO', 'RR' => 'RR', 'RS' => 'RS', 'SC' => 'SC', 'SE' => 'SE',
                'SP' => 'SP', 'TO' => 'TO'
            ), array('empty' => 'Escolha o estado')
        );
        ?>
        <?php echo $form->error($model, 'enr_state'); ?>
    </div>
    
    <div class="row">
        <?php echo $form->labelEx($model, 'enr_formation'); ?>
        <?php echo $form->textField($model, 'enr_formation'); ?>
        <?php echo $form->error($model, 'enr_formation'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model, 'enr_formation_area'); ?>
        <?php echo $form->textField($model, 'enr_formation_area'); ?>
        <?php echo $form->error($model, 'enr_formation_area'); ?>
    </div>

    <div class="row">
        <?php
        echo $form->labelEx($model, 'enr_public_server');
        echo $form->radioButtonList($model, 'enr_public_server', array(
            false => 'Não',
            true => 'Sim',
        ), array(
            'template' => '{input} {label}',
            'labelOptions' => array('style' => 'display:inline; margin: 10px')
        ));
        ?>
        <?php echo $form->error($model, 'enr_public_server'); ?>
    </div>

    <div class="row" id="input_siape">
        <?php echo $form->labelEx($model, 'enr_siape'); ?>
        <?php echo $form->textField($model, 'enr_siape'); ?>
        <?php echo $form->error($model, 'enr_siape'); ?>
    </div>
    
    <div class="row buttons">
        <?php echo CHtml::submitButton('Fazer inscrição'); ?>
        <?php echo CHtml::resetButton('Cancelar'); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->