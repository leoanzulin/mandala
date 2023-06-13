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
    'Meu perfil' => array('aluno/perfil'),
    'Editar perfil',
);
?>

<h1>Editar perfil</h1>

<div class="form" ng-app="inscricaoApp" ng-controller="controlador">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'editar-perfil-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <?php echo $form->errorSummary($model); ?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <fieldset><legend>Dados pessoais</legend>

        <div class="row">
            <?php echo $form->label($model, 'cpf'); ?>
            <p><?php echo $model->cpf; ?></p>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'nome'); ?>
            <p><?php echo $model->nome; ?></p>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'sobrenome'); ?>
            <p><?php echo $model->sobrenome; ?></p>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'tipo_identidade'); ?>
            <p><?php echo $model->tipo_identidade; ?></p>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'identidade'); ?>
            <p><?php echo $model->identidade; ?></p>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'orgao_expedidor'); ?>
            <p><?php echo $model->orgao_expedidor; ?></p>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'sexo'); ?>
            <?php
            echo $form->radioButtonList($model, 'sexo', array(
                'm' => 'Masculino',
                'f' => 'Feminino',
                    ), array('separator' => ' ',));
            ?>
            <?php echo $form->error($model, 'sexo'); ?>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'email'); ?>
            <p><?php echo $model->email; ?></p>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'data_nascimento'); ?>
            <?php
            $this->widget('CMaskedTextField', array(
                'model' => $model,
                'attribute' => 'data_nascimento',
                'mask' => '99/99/9999',
                'htmlOptions' => array('size' => 10, 'maxlength' => 10, 'class' => 'curto'),
            ));
            ?>
            <?php echo $form->error($model, 'data_nascimento'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'naturalidade'); ?>
            <?php echo $form->textField($model, 'naturalidade', array('size' => 60, 'maxlength' => 256, 'placeholder' => 'Cidade em que nasceu')); ?>
            <?php echo $form->error($model, 'naturalidade'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'nome_mae'); ?>
            <?php echo $form->textField($model, 'nome_mae', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'nome_mae'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'nome_pai'); ?>
            <?php echo $form->textField($model, 'nome_pai', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'nome_pai'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'estado_civil'); ?>
            <?php
            echo $form->dropDownList(
                    $model, 'estado_civil', array(
                'solteiro' => 'Solteiro',
                'casado' => 'Casado',
                'amasiado' => 'Amasiado',
                'divorciado' => 'Divorciado',
                'viuvo' => 'Viúvo',
                'outros' => 'Outros',
                    ), array('empty' => 'Escolha o estado civil')
            );
            ?>
            <?php echo $form->error($model, 'estado_civil'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'telefone_fixo'); ?>
            <?php
            $this->widget('CMaskedTextField', array(
                'model' => $model,
                'attribute' => 'telefone_fixo',
                'mask' => '(99)9999-9999',
                'htmlOptions' => array('size' => 13, 'maxlength' => 13, 'class' => 'curto'),
            ));
            ?>
            <?php echo $form->error($model, 'telefone_fixo'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'telefone_celular'); ?>
            <?php echo $form->textField($model, 'telefone_celular', array('size' => 30, 'maxlength' => 30, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'telefone_celular'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'telefone_alternativo'); ?>
            <?php echo $form->textField($model, 'telefone_alternativo', array('size' => 30, 'maxlength' => 30, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'telefone_alternativo'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'whatsapp'); ?>
            <?php echo $form->textField($model, 'whatsapp', array('size' => 30, 'maxlength' => 30, 'class' => 'curto')); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'skype'); ?>
            <?php echo $form->textField($model, 'skype', array('size' => 30, 'maxlength' => 256)); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'cep'); ?>
            <?php
            $this->widget('CMaskedTextField', array(
                'model' => $model,
                'attribute' => 'cep',
                'mask' => '99999-999',
                'htmlOptions' => array('size' => 9, 'maxlength' => 9, 'class' => 'curto'),
            ));
            ?>
            <?php echo $form->error($model, 'cep'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'endereco'); ?>
            <?php echo $form->textField($model, 'endereco', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'endereco'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'numero'); ?>
            <?php echo $form->textField($model, 'numero', array('size' => 30, 'maxlength' => 30, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'numero'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'complemento'); ?>
            <?php echo $form->textField($model, 'complemento', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'complemento'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'cidade'); ?>
            <?php echo $form->textField($model, 'cidade', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'cidade'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'estado'); ?>
            <?php
            echo $form->dropDownList(
                    $model, 'estado', array(
                'AC' => 'AC', 'AL' => 'AL', 'AM' => 'AM', 'AP' => 'AP', 'BA' => 'BA',
                'CE' => 'CE', 'DF' => 'DF', 'ES' => 'ES', 'GO' => 'GO', 'MA' => 'MA',
                'MG' => 'MG', 'MS' => 'MS', 'MT' => 'MT', 'PA' => 'PA', 'PB' => 'PB',
                'PE' => 'PE', 'PI' => 'PI', 'PR' => 'PR', 'RJ' => 'RJ', 'RN' => 'RN',
                'RO' => 'RO', 'RR' => 'RR', 'RS' => 'RS', 'SC' => 'SC', 'SE' => 'SE',
                'SP' => 'SP', 'TO' => 'TO'
                    ), array('empty' => 'Escolha o estado', 'class' => 'curto')
            );
            ?>
            <?php echo $form->error($model, 'estado'); ?>
        </div>

    </fieldset>

    <fieldset><legend>Formação acadêmica</legend>

        <p class="explicacao">Só é possível adicionar novas formações, não é possível remover as já existentes.</p>

        <?php echo $form->error($model, 'formacao'); ?>

        <table id="tabela-formacao">
            <tr>
                <th>Nível</th>
                <th>Curso</th>
                <th>Instituição</th>
                <th>Ano de conclusão</th>
                <th></th>
            </tr>

            <?php foreach ($model->formacoes as $formacao) { ?>
                <tr>
                    <td><?php echo $formacao->nivelPorExtenso; ?></td>
                    <td><?php echo $formacao->curso; ?></td>
                    <td><?php echo $formacao->instituicao; ?></td>
                    <td><?php echo $formacao->ano_conclusao; ?></td>
                    <td></td>
                </tr>
            <?php } ?>

            <tr ng-repeat="formacao in formacoes | orderBy:'conclusao' track by $index">
                <td>{{dePara[formacao.nivel]}}</td>
                <td>{{formacao.curso}}</td>
                <td>{{formacao.instituicao}}</td>
                <td>{{formacao.conclusao}}</td>
                <td><button type="button" ng-click="remover(formacao)">Remover</button></td>
            <input type="hidden" name="Inscricao[formacao][{{$index}}][nivel]" value="{{formacao.nivel}}">
            <input type="hidden" name="Inscricao[formacao][{{$index}}][curso]" value="{{formacao.curso}}">
            <input type="hidden" name="Inscricao[formacao][{{$index}}][instituicao]" value="{{formacao.instituicao}}">
            <input type="hidden" name="Inscricao[formacao][{{$index}}][ano_conclusao]" value="{{formacao.conclusao}}">
            </tr>
        </table>

        <div class="row">
            <?php echo $form->label($modelFormacao, 'nivel'); ?>
            <?php
            echo $form->dropDownList(
                    $modelFormacao, 'nivel', array(
                'graduacao' => 'Graduação',
                'especializacao' => 'Especialização',
                'mestrado' => 'Mestrado',
                'doutorado' => 'Doutorado',
                    ), array(
                'empty' => 'Nível acadêmico da formação',
                'ng-model' => 'formacao.nivel',
                    )
            );
            ?>
            <?php echo $form->error($modelFormacao, 'nivel'); ?>
        </div>

        <div class="row">
            <?php echo $form->label($modelFormacao, 'curso'); ?>
            <?php echo $form->textField($modelFormacao, 'curso', array('size' => 60, 'maxlength' => 256, 'placeholder' => "P. ex. Licenciatura em Pedagogia", 'ng-model' => "formacao.curso")); ?>
            <?php echo $form->error($modelFormacao, 'curso'); ?>
        </div>

        <div class="row">
            <?php echo $form->label($modelFormacao, 'instituicao'); ?>
            <?php echo $form->textField($modelFormacao, 'instituicao', array('size' => 30, 'maxlength' => 256, 'ng-model' => 'formacao.instituicao')); ?>
            <?php echo $form->error($modelFormacao, 'instituicao'); ?>
        </div>

        <div class="row">
            <?php echo $form->label($modelFormacao, 'ano_conclusao'); ?>
            <?php echo $form->textField($modelFormacao, 'ano_conclusao', array('size' => 4, 'maxlength' => 4, 'class' => 'curto', 'ng-model' => 'formacao.conclusao', 'ng-pattern' => '/^[0-9]{1,4}$/')); ?>
            <?php echo $form->error($modelFormacao, 'ano_conclusao'); ?>
        </div>

        <div class="row">
            <label></label>
            <input type="button" ng-click="adicionar()" value="Adicionar formação">
        </div>

    </fieldset>

    <fieldset><legend>Dados profissionais</legend>

        <div class="row">
            <?php echo $form->labelEx($model, 'cargo_atual'); ?>
            <?php echo $form->textField($model, 'cargo_atual', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'cargo_atual'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'empresa'); ?>
            <?php echo $form->textField($model, 'empresa', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'empresa'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'telefone_comercial'); ?>
            <?php echo $form->textField($model, 'telefone_comercial', array('size' => 30, 'maxlength' => 30, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'telefone_comercial'); ?>
        </div>

    </fieldset>

    <fieldset><legend>Tipo de curso</legend></fieldset>
    <h3><?php echo $model->tipoDeCursoPorExtenso(); ?></h3>

    <?php if ($model->ehAlunoDeEspecializacao()) { ?>
        <fieldset><legend>Habilitações</legend>
            <?php
            $i = 1;
            foreach ($model->recuperarHabilitacoes() as $habilitacao) {
                echo "<h3>Habilitação {$i}: {$habilitacao->nome}</h3>\n";
                $i++;
            }
            ?>
        </fieldset>
    <?php } ?>

    <fieldset><legend>Modalidade do curso</legend>
        <h3><b><?php echo $model->modalidadePorExtenso; ?></b></h3>
    </fieldset>

    <fieldset><legend>É candidato à bolsa de estudos?</legend>
        <h3><?php echo $model->candidato_a_bolsa === 'sim' ? 'Sim' : 'Não'; ?></h3>
    </fieldset>

    <div class="row buttons">
        <?php
        echo CHtml::submitButton('Salvar alterações', array(
            'onClick' => 'js:return validar();',
        ));
        ?>
    </div>

    <?php $this->endWidget(); ?>
