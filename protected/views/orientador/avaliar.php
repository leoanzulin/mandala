<?php
/* @var $this AlunoController */
/* @var $aluno Inscricao */
/* @var $habilitacoes Habilitacao[] */

$this->breadcrumbs = ['TCC' => ['orientador/pendentes'], 'Avaliar', $tcc->titulo];

function linkAtestadoOrientacao($colaborador, $tcc)
{
    if ($tcc->ehOrientador($colaborador->cpf)) {
        return CHtml::link('Atestado de orientação', ['gerarAtestadoOrientador', 'tccId' => $tcc->id]) . '<br>';
    }
}

function linkAtestadoBanca($colaborador, $tcc)
{
    if ($tcc->ehMembroDaBanca($colaborador->cpf)) {
        return CHtml::link('Atestado de membro da banca', ['gerarAtestadoBanca', 'tccId' => $tcc->id]);
    }
}
?>

<h1>Avaliar TCC: <?php echo $tcc->titulo; ?></h1>
<br>

<?php echo linkAtestadoOrientacao($colaborador, $tcc); ?>
<?php echo linkAtestadoBanca($colaborador, $tcc); ?>
<br>
<hr>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id' => 'avaliar-tcc-form',
        'enableAjaxValidation' => false,
    ]);
    ?>

    <div class="row">
        <?php echo $form->label($tcc, 'inscricao_id'); ?>
        <?php echo $tcc->inscricao->nomeCompleto; ?>
    </div>
    <div class="row">
        <?php echo $form->label($tcc, 'habilitacao_id'); ?>
        <?php echo $tcc->habilitacao->nome; ?>
    </div>

<h2>Versão de validação</h2>

<?php if ($tcc->recuperarStatus() >= Tcc::FASE_PRE_ORIENTADOR_ATRIBUIDO) { ?>
    <div class="row">
        <label>Arquivo</label>
        <a href="<?php echo $tcc->recuperarArquivoBanca(); ?>" target="_blank">Visualizar</a>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'validacao_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'validacao_data_entrega', ['disabled' => true]); ?>
    </div>
    <div class="row">
        <?php echo $form->label($tcc, 'validacao_orientador_cpf'); ?>
        <?php echo $tcc->orientador_provisorio->nomeCompleto; ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'validacao_consideracoes'); ?>
        <?php echo $form->textArea($tcc, 'validacao_consideracoes',
            [
                'disabled' => $tcc->recuperarStatus() > Tcc::FASE_PRE_ORIENTADOR_ATRIBUIDO || $tcc->validacao_orientador_cpf != Yii::app()->user->id,
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Considerações sobre o trabalho',
            ]);
        ?>
        <?php echo $form->error($tcc, 'validacao_consideracoes'); ?>
    </div>
    <?php if ($tcc->recuperarStatus() == Tcc::FASE_PRE_ORIENTADOR_ATRIBUIDO) { ?>
    <label></label>
    <?php echo CHtml::submitButton('Aprovar', [
        'class' => 'btn btn-success btn-lg',
        'name' => 'aprovar-validacao',
    ]); ?>
    <?php echo CHtml::submitButton('Aprovar com pendências', [
        'class' => 'btn btn-success btn-lg',
        'name' => 'aprovar-validacao-pendencias',
    ]); ?>
    <?php } ?>
    <?php if ($tcc->recuperarStatus() > Tcc::FASE_PRE_ORIENTADOR_ATRIBUIDO) { ?>
    <div class="row">
        <?php echo $form->label($tcc, 'validacao_tem_pendencias'); ?>
        <?php echo $form->radioButton($tcc, 'validacao_tem_pendencias', ['id' => 'validacao_tem_pendencias_sim', 'value' => true, 'disabled' => true]); ?>
        <label for="validacao_tem_pendencias_sim">Sim</label>
        <?php echo $form->radioButton($tcc, 'validacao_tem_pendencias', ['id' => 'validacao_tem_pendencias_nao', 'value' => false, 'disabled' => true]); ?>
        <label for="validacao_tem_pendencias_nao">Não</label>
    </div>
    <?php } ?>
<?php } ?>

<?php if ($tcc->recuperarStatus() >= Tcc::FASE_BANCA_ATRIBUIDA) { ?>

<br>
<h2>Versão para banca</h2>

<?php if (!empty($tcc->banca_arquivo)) { ?>
    <div class="row">
        <label>Arquivo</label>
        <a href="<?php echo $tcc->recuperarArquivoBanca(); ?>" target="_blank">Visualizar</a>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'banca_data_entrega', ['disabled' => true]); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_data_apresentacao'); ?>
        <?php echo $form->textField($tcc, 'banca_data_apresentacao', ['disabled' => true]); ?>
    </div>
    <div class="row">
        <?php echo $form->label($tcc, 'banca_membro1_cpf'); ?>
        <?php echo $tcc->banca_membro1->nomeCompleto; ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro1_consideracoes'); ?>
        <?php echo $form->textArea($tcc, 'banca_membro1_consideracoes',
            [
                'disabled' => $tcc->recuperarStatus() > Tcc::FASE_BANCA_ATRIBUIDA || Yii::app()->user->id != $tcc->banca_membro1_cpf,
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Considerações sobre o trabalho',
            ]);
        ?>
        <?php echo $form->error($tcc, 'banca_membro1_consideracoes'); ?>
    </div>
    <div class="row">
        <?php echo $form->label($tcc, 'banca_membro2_cpf'); ?>
        <?php echo $tcc->banca_membro2->nomeCompleto; ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro2_consideracoes'); ?>
        <?php echo $form->textArea($tcc, 'banca_membro2_consideracoes',
            [
                'disabled' => $tcc->recuperarStatus() > Tcc::FASE_BANCA_ATRIBUIDA || Yii::app()->user->id != $tcc->banca_membro2_cpf,
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Considerações sobre o trabalho',
            ]);
        ?>
        <?php echo $form->error($tcc, 'banca_membro2_consideracoes'); ?>
    </div>

    <?php if (!empty($tcc->banca_membro3_cpf)) { ?>
    <div class="row">
        <?php echo $form->label($tcc, 'banca_membro3_cpf'); ?>
        <?php echo $tcc->banca_membro3->nomeCompleto; ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro3_consideracoes'); ?>
        <?php echo $form->textArea($tcc, 'banca_membro3_consideracoes',
            [
                'disabled' => $tcc->recuperarStatus() > Tcc::FASE_BANCA_ATRIBUIDA || Yii::app()->user->id != $tcc->banca_membro3_cpf,
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Considerações sobre o trabalho',
            ]);
        ?>
        <?php echo $form->error($tcc, 'banca_membro3_consideracoes'); ?>
    </div>
    <?php } ?>
    <?php
        if ($tcc->recuperarStatus() == Tcc::FASE_BANCA_ATRIBUIDA) {
            if (Yii::app()->user->id == $tcc->banca_membro1_cpf) {
    ?>
    <label></label>
    <?php echo CHtml::submitButton('Aprovar', [
        'class' => 'btn btn-success btn-lg',
        'name' => 'aprovar-banca',
    ]); ?>
    <?php echo CHtml::submitButton('Aprovar com pendências', [
        'class' => 'btn btn-success btn-lg',
        'name' => 'aprovar-banca-pendencias',
    ]); ?>
    <?php
            }
            if (in_array(Yii::app()->user->id, [$tcc->banca_membro2_cpf, $tcc->banca_membro3_cpf])) {
    ?>
    <label></label>
    <?php echo CHtml::submitButton('Salvar', [
        'class' => 'btn btn-success btn-lg',
        'name' => 'salvar-banca',
    ]); ?>
    <p style="color: red">Obs: Apenas o primeiro membro da banca pode aprovar o trabalho</p>
    <?php
           }
        }
    ?>
    <?php if ($tcc->recuperarStatus() > Tcc::FASE_BANCA_ATRIBUIDA) { ?>
    <div class="row">
        <?php echo $form->label($tcc, 'banca_tem_pendencias'); ?>
        <?php echo $form->radioButton($tcc, 'banca_tem_pendencias', ['id' => 'banca_tem_pendencias_sim', 'value' => true, 'disabled' => true]); ?>
        <label for="banca_tem_pendencias_sim">Sim</label>
        <?php echo $form->radioButton($tcc, 'banca_tem_pendencias', ['id' => 'banca_tem_pendencias_nao', 'value' => false, 'disabled' => true]); ?>
        <label for="banca_tem_pendencias_nao">Não</label>
    </div>
    <?php } ?>
<?php } ?>

<?php } ?>

<?php if ($tcc->recuperarStatus() >= Tcc::FASE_ORIENTADOR_FINAL_ATRIBUIDO) { ?>

<br>
<h2>Versão final</h2>

<?php if (!empty($tcc->final_arquivo_doc)) { ?>
    <div class="row">
        <label>Arquivo</label>
        <a href="<?php echo $tcc->recuperarArquivoFinalDoc(); ?>" target="_blank">Visualizar</a>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'final_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'final_data_entrega', ['disabled' => true]); ?>
    </div>
<?php } ?>

<?php if (!empty($tcc->final_arquivo_pdf)) { ?>
    <div class="row">
        <label></label>
        <a href="<?php echo $tcc->recuperarArquivoFinalPdf(); ?>" target="_blank">Visualizar</a>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'final_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'final_data_entrega', ['disabled' => true]); ?>
    </div>
<?php } ?>

<div class="row">
    <?php echo $form->label($tcc, 'final_orientador_cpf'); ?>
    <?php echo $tcc->orientador_final->nomeCompleto; ?>
</div>
<?php if (!empty($tcc->final_coorientador_cpf)) { ?>
<div class="row">
    <?php echo $form->label($tcc, 'final_coorientador_cpf'); ?>
    <?php echo $tcc->coorientador_final->nomeCompleto; ?>
</div>
<?php } ?>

<label></label>
<?php echo CHtml::submitButton('Aprovar', [
    'class' => 'btn btn-success btn-lg',
    'name' => 'aprovar-final',
]); ?>

<?php } ?>
<?php if ($tcc->recuperarStatus() > Tcc::FASE_ORIENTADOR_FINAL_ATRIBUIDO) { ?>
    <div class="row">
        <?php echo $form->label($tcc, 'aprovado'); ?>
        <?php echo $form->checkBox($model, 'aprovado', ['disabled' => true]); ?>
    </div>
<?php } ?>

<?php $this->endWidget(); ?>

</div>
