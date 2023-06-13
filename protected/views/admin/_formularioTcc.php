<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'tcc-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>
    <?php echo $form->errorSummary($tcc); ?>

    <div class="row">
        <label>Aluno</label><p><?php echo $tcc->inscricao->nomeCompleto; ?></p>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'habilitacao_id'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'habilitacao_id',
            $listaHabilitacoes,
            array('empty' => 'Habilitação')
        );
        ?>
        <?php echo $form->error($tcc, 'habilitacao_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'titulo'); ?>
        <?php echo $form->textField($tcc, 'titulo', array('size' => 60, 'maxlength' => 256)); ?>
        <?php echo $form->error($tcc, 'titulo'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'orientador_cpf'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'orientador_cpf',
            $listaDocentes,
            array('empty' => 'Selecione o orientador')
        );
        ?>
        <?php echo $form->error($tcc, 'orientador_cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'coorientador_cpf'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'coorientador_cpf',
            $listaDocentes,
            array('empty' => 'Coorientador (se houver)')
        );
        ?>
        <?php echo $form->error($tcc, 'orientador_cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'data_apresentacao'); ?>
        <?php
        $this->widget('CMaskedTextField', array(
            'model' => $tcc,
            'attribute' => 'data_apresentacao',
            'mask' => '99/99/9999',
            'htmlOptions' => array('size' => 10, 'maxlength' => 10, 'class' => 'curto'),
        ));
        ?>
        <?php echo $form->error($tcc, 'data_apresentacao'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'nota'); ?>
        <?php echo $form->textField($tcc, 'nota', array('size' => 30, 'maxlength' => 256, 'class' => 'curto')); ?>
        <?php echo $form->error($tcc, 'nota'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'status'); ?>
        <?php
        echo $form->dropDownList(
            $tcc, 'status', array(
                'agendado' => 'Agendado',
                'aprovado' => 'Aprovado',
                'reprovado' => 'Reprovado',
            ), array('empty' => 'Status')
        );
        ?>
        <?php echo $form->error($tcc, 'status'); ?>
    </div>

    <?php if ($tcc->isNewRecord) { ?>
        <div class="row buttons">
            <label></label>
            <?php echo CHtml::submitButton('Cadastrar', [
                'class' => 'btn btn-success btn-lg',
            ]); ?>
        </div>
    <?php } else { ?>
        <div class="row buttons" style="margin-top: 30px">
            <label></label>
            <?php echo CHtml::submitButton('Salvar', [
                'class' => 'btn btn-success btn-lg',
                'name' => 'salvar',
            ]); ?>
        </div>
        <div class="row buttons" style="margin-top: 10px">
            <label></label>
            <?php echo CHtml::submitButton('Excluir TCC', [
                'class' => 'btn btn-danger btn-lg',
                'name' => 'excluir',
                'onClick' => 'js:return confirm("Tem certeza que deseja excluir este TCC?");',
            ]); ?>
        </div>
    <?php
    }
    $this->endWidget();
    ?>

</div>