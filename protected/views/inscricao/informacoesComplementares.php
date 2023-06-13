<?php
/* @var $this InscricaoController */
/* @var $dataProvider CActiveDataProvider */
?>

<h1>Informações complementares</h1>
<br>

<p>Caro(a) <?php echo $model->nome ?>, antes de continuar, precisamos de mais algumas informações.</p>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'informacoes-complementares-form',
	'enableAjaxValidation'=>false,
)); ?>

    <?php echo $form->errorSummary($model); ?>
    
    <br>
    <p class="explicacao">Campos com * são obrigatórios</p>

    <fieldset><legend>Informações pessoais</legend>

    <div class="row">
		<?php echo $form->labelEx($model,'tipo_identidade'); ?>
        <?php echo $form->dropDownList($model, 'tipo_identidade', array(
            'rg' => 'RG',
            'cnh' => 'CNH',
            'passaporte' => 'Passaporte'
        ));?>
		<?php echo $form->error($model,'tipo_identidade'); ?>
    </div>
        
    <div class="row">
        <?php echo $form->labelEx($model,'identidade'); ?>
        <?php echo $form->textField($model,'identidade',array('size'=>20,'maxlength'=>20, 'class' => 'curto')); ?>
        <?php echo $form->error($model,'identidade'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'orgao_expedidor'); ?>
        <?php echo $form->textField($model,'orgao_expedidor',array('size'=>50,'maxlength'=>50, 'class' => 'curto')); ?>
        <?php echo $form->error($model,'orgao_expedidor'); ?>
    </div>

    <br>
    <div class="row">
        <?php echo $form->labelEx($model,'whatsapp'); ?>
        <?php echo $form->textField($model,'whatsapp',array('size'=>30,'maxlength'=>30, 'class' => 'curto')); ?>
        <?php echo $form->error($model,'whatsapp'); ?>
    </div>

    <div class="row">
		<?php echo $form->labelEx($model, 'skype'); ?>
		<?php echo $form->textField($model, 'skype',array('size'=>30,'maxlength'=>30, 'class' => 'curto')); ?>
		<?php echo $form->error($model, 'skype'); ?>
    </div>
        
    </fieldset>
    
    <fieldset><legend>Habilitações</legend>

        <h3>* Habilitação prioritária</h3>

        <?php echo $form->error($model, 'habilitacao1'); ?>
        
        <div class="row checkboxes">
            <table>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao1', array('id' => 'habilitacao1_g', 'value' => '3', 'uncheckValue' => null)); ?></td><td><label for="habilitacao1_g">Gestão da Educação a Distância</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao1', array('id' => 'habilitacao1_d', 'value' => '4', 'uncheckValue' => null)); ?></td><td><label for="habilitacao1_d">Docência Virtual</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao1', array('id' => 'habilitacao1_m', 'value' => '1', 'uncheckValue' => null)); ?></td><td><label for="habilitacao1_m">Mídias na Educação</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao1', array('id' => 'habilitacao1_t', 'value' => '2', 'uncheckValue' => null)); ?></td><td><label for="habilitacao1_t">Produção e Uso de Tecnologias para Educação</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao1', array('id' => 'habilitacao1_p', 'value' => '5', 'uncheckValue' => null)); ?></td><td><label for="habilitacao1_p">Design Instrucional (Projeto e Desenho Pedagógico)</label></td></tr>
            </table>
        </div>
        
        <h3>* Habilitação secundária (demandará investimento adicional)</h3>

        <?php echo $form->error($model, 'habilitacao2'); ?>

        <div class="row checkboxes">
            
            <table>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao2', array('id' => 'habilitacao2_x', 'value' => '0', 'uncheckValue' => null)); ?></td><td><label for="habilitacao2_x">Nenhuma</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao2', array('id' => 'habilitacao2_g', 'value' => '3', 'uncheckValue' => null)); ?></td><td><label for="habilitacao2_g">Gestão da Educação a Distância</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao2', array('id' => 'habilitacao2_d', 'value' => '4', 'uncheckValue' => null)); ?></td><td><label for="habilitacao2_d">Docência Virtual</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao2', array('id' => 'habilitacao2_m', 'value' => '1', 'uncheckValue' => null)); ?></td><td><label for="habilitacao2_m">Mídias na Educação</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao2', array('id' => 'habilitacao2_t', 'value' => '2', 'uncheckValue' => null)); ?></td><td><label for="habilitacao2_t">Produção e Uso de Tecnologias para Educação</label></td></tr>
                <tr data-habilitacao="edtec"><td><?php echo $form->radioButton($model, 'habilitacao2', array('id' => 'habilitacao2_p', 'value' => '5', 'uncheckValue' => null)); ?></td><td><label for="habilitacao2_p">Design Instrucional (Projeto e Desenho Pedagógico)</label></td></tr>
            </table>
        </div>

    </fieldset>
    
    <fieldset><legend>* Modalidade do curso</legend>
    <?php echo $form->error($model,'modalidade'); ?>

    <?php echo $form->radioButton($model, 'modalidade', array('id' => 'modalidade_distancia', 'value' => 'distancia', 'uncheckValue' => null)); ?>
    <label for="modalidade_distancia">A distância</label>
    <p class="explicacao">&nbsp;</p>

    <?php echo $form->radioButton($model, 'modalidade', array('id' => 'modalidade_presencial', 'value' => 'presencial', 'uncheckValue' => null)); ?>
    <label for="modalidade_presencial">Presencial</label>
    <p class="explicacao">(aulas na sexta-feira a noite e sábado manhã e tarde)</p>

    <?php //echo CHtml::radioButton('modalidade', false, array('id' => 'modalidade_mista', 'value' => 'mista')); ?>
    <?php echo $form->radioButton($model, 'modalidade', array('id' => 'modalidade_mista', 'value' => 'mista', 'uncheckValue' => null)); ?>
    <label for="modalidade_mista">Mista</label>
    <p class="explicacao">(com alguns componentes pela Educação a Distância e outros presencialmente).</p>

    </fieldset>
    
    <fieldset><legend>* É candidato à bolsa de estudos?</legend>
    <?php echo $form->error($model,'candidato_a_bolsa'); ?>

    <?php echo $form->radioButton($model, 'candidato_a_bolsa', array('id' => 'candidato_bolsa_nao', 'value' => 'nao', 'uncheckValue' => null)); ?>
    <label for="candidato_bolsa_nao">Não</label>
    <?php echo $form->radioButton($model, 'candidato_a_bolsa', array('id' => 'candidato_bolsa_sim', 'value' => 'sim', 'uncheckValue' => null)); ?>
    <label for="candidato_bolsa_sim">Sim</label>
    </fieldset>

    <fieldset><legend>Se desejar, deixe comentários e sugestões no campo abaixo</legend>
    <?php echo $form->textArea($model, 'comentarios', array(
        'maxlength' => 3000, 'rows' => 6, 'cols' => 100
    )); ?>
    </fieldset>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Continuar', array(
            'onClick' => 'js:return validar();',
        )); ?>
	</div>

<?php $this->endWidget(); ?>