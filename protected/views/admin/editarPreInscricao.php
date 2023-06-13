<?php
/* @var $this InscricaoController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerScript('scripts_locais', "
    // Acopla a máscara aos campos de telefone
    mascara_celular('#Inscricao_telefone_celular');
    mascara_celular('#Inscricao_telefone_alternativo');
    mascara_celular('#Inscricao_telefone_comercial');
");

$this->breadcrumbs = array(
    'Gerenciar pré-inscrições' => array('gerenciarPreInscricoes'),
    $model->id,
);
?>

<h1>Editar perfil</h1>

<div class="form" ng-app="editarPerfilApp" ng-controller="controlador">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'editar-perfil-form',
	'enableAjaxValidation'=>false,
)); ?>

    <?php echo $form->errorSummary($model); ?>
    
	<p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <fieldset><legend>Dados pessoais</legend>
    
	<div class="row">
		<?php echo $form->label($model,'cpf'); ?>
        <p><?php echo $model->cpf; ?></p>
	</div>

	<div class="row">
		<?php echo $form->label($model,'nome'); ?>
        <p><?php echo $model->nome; ?></p>
	</div>

	<div class="row">
		<?php echo $form->label($model,'sobrenome'); ?>
        <p><?php echo $model->sobrenome; ?></p>
	</div>

    <div class="row">
		<?php echo $form->label($model,'tipo_identidade'); ?>
        <p><?php echo $model->tipo_identidade; ?></p>
    </div>
        
    <div class="row">
        <?php echo $form->label($model,'identidade'); ?>
        <p><?php echo $model->identidade; ?></p>
    </div>

    <div class="row">
        <?php echo $form->label($model,'orgao_expedidor'); ?>
        <p><?php echo $model->orgao_expedidor; ?></p>
    </div>
        
    <div class="row">
		<?php echo $form->labelEx($model,'sexo'); ?>
        <?php echo $form->radioButtonList($model, 'sexo', array(
            'm' => 'Masculino',
            'f' => 'Feminino',
        ), array('separator' => ' ',)); ?>
		<?php echo $form->error($model,'sexo'); ?>
	</div>
        
    <div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>
        
	<div class="row">
		<?php echo $form->labelEx($model,'data_nascimento'); ?>
        <?php $this->widget('CMaskedTextField', array(
            'model' => $model,
            'attribute' => 'data_nascimento',
            'mask' => '99/99/9999',
            'htmlOptions' => array('size' => 10, 'maxlength' => 10, 'class' => 'curto'),
        )); ?>
		<?php echo $form->error($model,'data_nascimento'); ?>
	</div>

    <div class="row">
		<?php echo $form->labelEx($model,'naturalidade'); ?>
		<?php echo $form->textField($model,'naturalidade',array('size'=>60,'maxlength'=>256, 'placeholder'=>'Cidade em que nasceu')); ?>
		<?php echo $form->error($model,'naturalidade'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nome_mae'); ?>
		<?php echo $form->textField($model,'nome_mae',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'nome_mae'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'nome_pai'); ?>
		<?php echo $form->textField($model,'nome_pai',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'nome_pai'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'estado_civil'); ?>
        <?php echo $form->dropDownList(
            $model, 'estado_civil', array(
                'solteiro' => 'Solteiro',
                'casado' => 'Casado',
                'amasiado' => 'Amasiado',
                'divorciado' => 'Divorciado',
                'viuvo' => 'Viúvo',
                'outros' => 'Outros',
            ),
            array('empty' => 'Escolha o estado civil')
        ); ?>
		<?php echo $form->error($model,'estado_civil'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telefone_fixo'); ?>
        <?php $this->widget('CMaskedTextField', array(
            'model' => $model,
            'attribute' => 'telefone_fixo',
            'mask' => '(99)9999-9999',
            'htmlOptions' => array('size' => 13, 'maxlength' => 13, 'class' => 'curto'),
        )); ?>
		<?php echo $form->error($model,'telefone_fixo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telefone_celular'); ?>
		<?php echo $form->textField($model,'telefone_celular',array('size'=>30,'maxlength'=>30, 'class' => 'curto')); ?>
		<?php echo $form->error($model,'telefone_celular'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'telefone_alternativo'); ?>
		<?php echo $form->textField($model,'telefone_alternativo',array('size'=>30,'maxlength'=>30, 'class' => 'curto')); ?>
		<?php echo $form->error($model,'telefone_alternativo'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'whatsapp'); ?>
        <?php echo $form->textField($model,'whatsapp', array('size'=>30, 'maxlength' => 30, 'class' => 'curto')); ?>
    </div>
        
    <div class="row">
        <?php echo $form->labelEx($model,'skype'); ?>
        <?php echo $form->textField($model,'skype', array('size'=>30, 'maxlength' => 256)); ?>
    </div>
        
	<div class="row">
		<?php echo $form->labelEx($model,'cep'); ?>
        <?php $this->widget('CMaskedTextField', array(
            'model' => $model,
            'attribute' => 'cep',
            'mask' => '99999-999',
            'htmlOptions' => array('size' => 9, 'maxlength' => 9, 'class' => 'curto'),
        )); ?>
		<?php echo $form->error($model,'cep'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'endereco'); ?>
		<?php echo $form->textField($model,'endereco',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'endereco'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'numero'); ?>
		<?php echo $form->textField($model,'numero',array('size'=>30,'maxlength'=>30, 'class' => 'curto')); ?>
		<?php echo $form->error($model,'numero'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'complemento'); ?>
		<?php echo $form->textField($model,'complemento',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'complemento'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cidade'); ?>
		<?php echo $form->textField($model,'cidade',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'cidade'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'estado'); ?>
        <?php echo $form->dropDownList(
            $model, 'estado', array(
                'AC' => 'AC', 'AL' => 'AL', 'AM' => 'AM', 'AP' => 'AP', 'BA' => 'BA',
                'CE' => 'CE', 'DF' => 'DF', 'ES' => 'ES', 'GO' => 'GO', 'MA' => 'MA',
                'MG' => 'MG', 'MS' => 'MS', 'MT' => 'MT', 'PA' => 'PA', 'PB' => 'PB',
                'PE' => 'PE', 'PI' => 'PI', 'PR' => 'PR', 'RJ' => 'RJ', 'RN' => 'RN',
                'RO' => 'RO', 'RR' => 'RR', 'RS' => 'RS', 'SC' => 'SC', 'SE' => 'SE',
                'SP' => 'SP', 'TO' => 'TO'
            ),
            array('empty' => 'Escolha o estado', 'class' => 'curto')
        ); ?>
		<?php echo $form->error($model,'estado'); ?>
	</div>

    </fieldset>

    <h2>Formação</h2>

<ul>
<?php
foreach ($model->formacoes as $formacao) {
    $tabelaNivel = array('graduacao' => 'Graduação', 'especializacao' => 'Especialização', 'mestrado' => 'Mestrado', 'doutorado' => 'Doutorado');
    echo "<li>{$tabelaNivel[$formacao->nivel]} - {$formacao->curso} - {$formacao->instituicao} ({$formacao->ano_conclusao})</li>\n";
}
?>
</ul>
<br>

<h2>Habilitações escolhidas</h2>
    
    <div class="row">
	<?php echo $form->label($model,'habilitacao1'); ?>
        <?php echo $form->dropDownList(
            $model, 'habilitacao1', array(
                '1' => 'Mídias na Educação',
                '2' => 'Produção e Uso de Tecnologias para Educação',
                '3' => 'Gestão da Educação a Distância',
                '4' => 'Docência na Educação a Distância',
                '5' => 'Projeto e Desenho Pedagógico (Design Instrucional)'
            ),
            array('0' => 'Nenhuma')
        ); ?>
	<?php echo $form->error($model,'habilitacao1'); ?>
    </div>
    <div class="row">
	<?php echo $form->label($model,'habilitacao2'); ?>
        <?php echo $form->dropDownList(
            $model, 'habilitacao2', array(
		'0' => 'Nenhuma',
                '1' => 'Mídias na Educação',
                '2' => 'Produção e Uso de Tecnologias para Educação',
                '3' => 'Gestão da Educação a Distância',
                '4' => 'Docência na Educação a Distância',
                '5' => 'Projeto e Desenho Pedagógico (Design Instrucional)'
            ),
            array('0' => 'Nenhuma')
        ); ?>
	<?php echo $form->error($model,'habilitacao2'); ?>
    </div>
    
    
<h2>Documentos</h2>

<?php if (empty($model->documento_cpf)) { ?>
<p>Ainda não enviou os documentos</p>
<?php } else { ?>

<ul>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('cpf', true); ?>">CPF</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('rg', true); ?>">RG</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('diploma', true); ?>">Diploma</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('comprovante_residencia', true); ?>">Comprovante de residência</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('curriculo', true); ?>">Currículo</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('justificativa', true); ?>">Justificativa de próprio punho</a></li>
</ul>

<?php } ?>

<fieldset><legend>Modalidade do curso</legend>
    <h3><b><?php echo $model->modalidadePorExtenso; ?></b></h3>
</fieldset>

    <fieldset><legend>É candidato à bolsa de estudos?</legend>
    <h3><?php if ($model->candidato_a_bolsa) echo 'Sim'; else echo 'Não'; ?></h3>
    </fieldset>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Salvar alterações', array(
            'onClick' => 'js:return validar();',
        )); ?>
	</div>
    
<?php $this->endWidget(); ?>
