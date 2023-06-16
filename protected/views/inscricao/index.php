<?php
/* @var $this InscricaoController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerScript('scripts_locais', "
    // Acopla a máscara aos campos de telefone
    mascara_celular('#Inscricao_telefone_celular');
    mascara_celular('#Inscricao_telefone_alternativo');
    mascara_celular('#Inscricao_telefone_comercial');
");
?>

<h1>Formulário de pré-inscrição</h1>

<div class="form" ng-app="inscricaoApp" ng-controller="controlador">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'inscricao-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>

    <?php echo $form->errorSummary($model); ?>

    <p class="note">Campos com <span class="required">*</span> são obrigatórios.</p>

    <fieldset><legend>Dados pessoais</legend>

        <div class="row">
            <?php echo $form->labelEx($model, 'cpf'); ?>
            <?php echo $form->textField($model, 'cpf', array('size' => 11, 'maxlength' => 11, 'class' => 'curto', 'ng-model' => 'cpf')); ?>
            <?php echo $form->error($model, 'cpf'); ?>
            <span class="errorMessage" ng-if="!cpfValido(cpf)">CPF inválido</span>
        </div>
        <label></label><p style="color: #AAA; font-style: italic; font-size: 0.9em;">Informe apenas números</p>

        <div class="row">
            <?php echo $form->labelEx($model, 'tipo_identidade'); ?>
            <?php
            echo $form->dropDownList($model, 'tipo_identidade', array(
                'rg' => 'RG',
                'cnh' => 'CNH',
                'passaporte' => 'Passaporte'
            ));
            ?>
            <?php echo $form->error($model, 'tipo_identidade'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'identidade'); ?>
            <?php echo $form->textField($model, 'identidade', array('size' => 20, 'maxlength' => 20, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'identidade'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'orgao_expedidor'); ?>
            <?php echo $form->textField($model, 'orgao_expedidor', array('size' => 50, 'maxlength' => 50, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'orgao_expedidor'); ?>
        </div>

        <br>
        <div class="row">
            <?php echo $form->labelEx($model, 'nome'); ?>
            <?php echo $form->textField($model, 'nome', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'nome'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'sobrenome'); ?>
            <?php echo $form->textField($model, 'sobrenome', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'sobrenome'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'sexo'); ?>
            <?php
            echo $form->radioButtonList($model, 'sexo', array(
                'm' => 'Masculino',
                'f' => 'Feminino',
                    ), array('separator' => ' '));
            ?>
            <?php echo $form->error($model, 'sexo'); ?>
        </div>        

        <div class="row">
            <?php echo $form->labelEx($model, 'email'); ?>
            <?php echo $form->textField($model, 'email', array('size' => 60, 'maxlength' => 256)); ?>
            <?php echo $form->error($model, 'email'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'confirmarEmail'); ?>
            <?php echo $form->textField($model, 'confirmarEmail', array('size' => 60, 'maxlength' => 256, 'autocomplete' => 'off')); ?>
            <?php echo $form->error($model, 'confirmarEmail'); ?>
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
            <?php echo $form->error($model, 'whatsapp'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model, 'skype'); ?>
            <?php echo $form->textField($model, 'skype', array('size' => 30, 'maxlength' => 30, 'class' => 'curto')); ?>
            <?php echo $form->error($model, 'skype'); ?>
        </div>

    </fieldset>

    <fieldset><legend>Endereço</legend>

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

        <?php echo $form->error($model, 'formacao'); ?>

        <table id="tabela-formacao">
            <tr>
                <th>Nível</th>
                <th>Curso</th>
                <th>Instituição</th>
                <th>Ano de conclusão</th>
                <th></th>
            </tr>
            <tr ng-show="formacoes.length === 0">
                <td colspan="4"><i>Nenhuma formação informada</i></td>
            </tr>
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
            <?php echo $form->labelEx($modelFormacao, 'nivel'); ?>
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
            <?php echo $form->labelEx($modelFormacao, 'curso'); ?>
            <?php echo $form->textField($modelFormacao, 'curso', array('size' => 60, 'maxlength' => 256, 'placeholder' => "P. ex. Licenciatura em Pedagogia", 'ng-model' => "formacao.curso")); ?>
            <?php echo $form->error($modelFormacao, 'curso'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($modelFormacao, 'instituicao'); ?>
            <?php echo $form->textField($modelFormacao, 'instituicao', array('size' => 30, 'maxlength' => 256, 'ng-model' => 'formacao.instituicao')); ?>
            <?php echo $form->error($modelFormacao, 'instituicao'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($modelFormacao, 'ano_conclusao'); ?>
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

    <fieldset><legend>Tipo do curso</legend>
        <?php echo $form->error($model, 'tipo_curso'); ?>

        <?php echo $form->radioButton($model, 'tipo_curso', array('id' => 'tipo_curso_extensao', 'value' => Inscricao::TIPO_CURSO_EXTENSAO, 'uncheckValue' => null, 'ng-model' => 'tipoDeCurso', 'ng-click' => 'deselecionarHabilitacoes()')); ?>
        <label for="tipo_curso_extensao">Extensão</label>
        <p class="explicacao">Realização de 3 componentes</p>

        <?php echo $form->radioButton($model, 'tipo_curso', array('id' => 'tipo_curso_aperfeicoamento', 'value' => Inscricao::TIPO_CURSO_APERFEICOAMENTO, 'uncheckValue' => null, 'ng-model' => 'tipoDeCurso', 'ng-click' => 'deselecionarHabilitacoes()')); ?>
        <label for="tipo_curso_aperfeicoamento">Aperfeiçoamento</label>
        <p class="explicacao">Realização de 9 componentes</p>

        <?php echo $form->radioButton($model, 'tipo_curso', array('id' => 'tipo_curso_especializacao', 'value' => Inscricao::TIPO_CURSO_ESPECIALIZACAO, 'uncheckValue' => null, 'ng-model' => 'tipoDeCurso')); ?>
        <label for="tipo_curso_especializacao">Especialização</label>
        <p class="explicacao">&nbsp;</p>

        <p style="margin-left: 30px">Habilitações da especialização:</p>
        <div class="row checkboxes" style="margin-left: 30px">
            <table>
                <tr
                    data-habilitacao="edtec"
                    ng-repeat="habilitacao in habilitacoes"
                    ng-class="{selecionado: habilitacao.selecionada}"
                    ng-click="selecionar(habilitacao)"
                >
                    <td>
                        <input
                            id="habilitacao1_{{habilitacao.letra}}"
                            type="checkbox"
                            ng-click="selecionar(habilitacao); $event.stopPropagation();"
                            ng-checked="habilitacao.selecionada"
                            ng-disabled="tipoDeCurso != 2"
                        >
                    </td>
                    <td>
                        <label
                            for="habilitacao1_{{habilitacao.letra}}"
                            ng-click="$event.stopPropagation();"
                        >
                            {{habilitacao.ordem}} {{habilitacao.nome}}
                        </label>
                    </td>
                    <input type="hidden" name="habilitacoes[{{habilitacao.letra}}]" value="{{habilitacao.ordem}}">
                </tr>
            </table>
        </div>

    </fieldset>

    <fieldset><legend>* Modalidade do curso</legend>
        <?php echo $form->error($model, 'modalidade'); ?>

        <?php echo $form->radioButton($model, 'modalidade', array('id' => 'modalidade_distancia', 'value' => 'distancia', 'uncheckValue' => null)); ?>
        <label for="modalidade_distancia">A distância</label>
        <p class="explicacao">&nbsp;</p>

        <?php echo $form->radioButton($model, 'modalidade', array('id' => 'modalidade_presencial', 'value' => 'presencial', 'uncheckValue' => null)); ?>
        <label for="modalidade_presencial">Presencial</label>
        <p class="explicacao">(aulas na sexta-feira a noite e sábado manhã e tarde)</p>

        <?php echo $form->radioButton($model, 'modalidade', array('id' => 'modalidade_mista', 'value' => 'mista', 'uncheckValue' => null)); ?>
        <label for="modalidade_mista">Mista</label>
        <p class="explicacao">(com alguns componentes pela Educação a Distância e outros presencialmente).</p>

    </fieldset>

    <fieldset><legend>* Você irá solicitar bolsa de 40%? Pré-requisito: atuar como professor(a) da Educação Básica ou estar desempregado(a)</legend>
        <?php echo $form->error($model, 'candidato_a_bolsa'); ?>

        <?php echo $form->radioButton($model, 'candidato_a_bolsa', array('id' => 'candidato_bolsa_nao', 'value' => 'nao', 'uncheckValue' => null)); ?>
        <label for="candidato_bolsa_nao">Não</label>
        <?php echo $form->radioButton($model, 'candidato_a_bolsa', array('id' => 'candidato_bolsa_sim', 'value' => 'sim', 'uncheckValue' => null)); ?>
        <label for="candidato_bolsa_sim">Sim</label>
    </fieldset>

    <fieldset><legend>* Envio de documentos</legend>

        <p>Instruções:</p>
        <ul>
            <li>Apenas <b>cópias simples</b> dos documentos são necessárias</li>
            <li>Formatos aceitos: PDF, JPG, GIF, PNG</li>
            <li>Para o currículo: DOC, DOCX, PDF, JPG, GIF, PNG</li>
            <li>Documentos bolsa: comprovante para professores da educação básica (Holerite, Carteira de trabalho ou Declaração da Instituição, apenas 1); comprovante para desempregados (Carteira de trabalho com o último registro).</li>
            <li>Tamanho máximo dos arquivos: 2MB</li>
        </ul>
        <br>

        <div class="row">
            <?php echo $form->labelEx($model, 'documento_cpf'); ?>
            <?php echo $form->fileField($model, 'documento_cpf'); ?>
            <?php echo $form->error($model, 'documento_cpf'); ?>
        </div>
        <br>

        <div class="row">
            <?php echo $form->labelEx($model, 'documento_rg'); ?>
            <?php echo $form->fileField($model, 'documento_rg'); ?>
            <?php echo $form->error($model, 'documento_rg'); ?>
        </div>
        <br>

        <div class="row">
            <?php echo $form->labelEx($model, 'documento_diploma'); ?>
            <?php echo $form->fileField($model, 'documento_diploma'); ?>
            <?php echo $form->error($model, 'documento_diploma'); ?>
        </div>
        <br>

        <div class="row">
            <?php echo $form->labelEx($model, 'documento_comprovante_residencia'); ?>
            <?php echo $form->fileField($model, 'documento_comprovante_residencia'); ?>
            <?php echo $form->error($model, 'documento_comprovante_residencia'); ?>
        </div>
        <br>

        <div class="row">
            <?php echo $form->labelEx($model, 'documento_curriculo'); ?>
            <?php echo $form->fileField($model, 'documento_curriculo'); ?>
            <?php echo $form->error($model, 'documento_curriculo'); ?>
        </div>
        <br>

        <div class="row">
            <?php echo $form->labelEx($model, 'documento_justificativa'); ?>
            <?php echo $form->fileField($model, 'documento_justificativa'); ?>
            <?php echo $form->error($model, 'documento_justificativa'); ?>
        </div>
        <br>

        <fieldset><legend>Se desejar, deixe comentários e sugestões no campo abaixo</legend>
            <?php
            echo $form->textArea($model, 'comentarios', array(
                'maxlength' => 3000, 'rows' => 6, 'cols' => 100
            ));
            ?>
        </fieldset>

        <div class="row buttons">
            <?php
            echo CHtml::submitButton('Fazer inscrição', array(
                'onClick' => 'js:return validar();',
            ));
            ?>
        </div>

        <?php $this->endWidget(); ?>
