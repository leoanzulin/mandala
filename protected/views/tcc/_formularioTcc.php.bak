<?php

function gerarLinkSintese($i, $sintese)
{
    $label = ($i + 1) . " - {$sintese->componente_curricular->nome}";
    return CHtml::link($label, ['editarSinteseComponente', 'id' => $sintese->id]);
}

function gerarLinkMover($i, $numeroMaximo, $modelo, $direcao, $id)
{
    if ($numeroMaximo == 1) return null;
    if ($i == 0 && $direcao == -1) return null;
    if ($i == $numeroMaximo - 1 && $direcao == 1) return null;

    $textoDirecao = $direcao == -1 ? 'cima' : 'baixo';
    $label = "mover para {$textoDirecao}";
    $legenda = ucfirst($label);
    $label = CHtml::image("images/seta_{$textoDirecao}.png", $legenda, ['title' => $legenda]);
    $acao = "mover{$modelo}A{$textoDirecao}";
    $requestParameters = [
        'data' => [],
        'type' => 'get',
        'success' => 'js:function() { location.reload(); }',
    ];
    return CHtml::ajaxLink($label, [$acao, 'id' => $id], $requestParameters);
}

function gerarLinkProposta($i, $proposta)
{
    $label = ($i + 1) . " - {$proposta->titulo}";
    return CHtml::link($label, ['editarPropostaPedagogica', 'id' => $proposta->id]);
}

?>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'tcc-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => array('enctype' => 'multipart/form-data'),
    ));
    ?>
    <?php echo $form->errorSummary($tcc); ?>

    <?php if ($ehCoordenacao) { ?>
    <div class="row">
        <?php echo $form->label($tcc, 'inscricao_id'); ?>
        <p><?php echo $tcc->inscricao->nomeCompleto ?></p>
        <?php echo $form->error($tcc, 'inscricao_id'); ?>
    </div>
    <?php } ?>

    <div class="row">
        <?php echo $form->label($tcc, 'habilitacao_id'); ?>
        <p><?php echo $habilitacao->nome ?></p>
        <?php echo $form->error($tcc, 'habilitacao_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'titulo'); ?>
        <?php echo $form->textField($tcc, 'titulo', array('size' => 60, 'maxlength' => 256)); ?>
        <?php echo $form->error($tcc, 'titulo'); ?>
    </div>

<?php if (!$tcc->isNewRecord) { ?>

    <fieldset>
        <legend>Caracterização do especialista</legend>
        <?php echo CHtml::link('Editar caracterização do especialista', [
            'editarCaracterizacaoEspecialista', 'tccId' => $tcc->id
        ]); ?>
    </fieldset>

    <fieldset>
        <legend>Sínteses dos componentes mais essenciais realizados no EduTec (5 ou mais)</legend>
        <uil>
        <?php foreach ($tcc->sinteses_componentes as $i => $sintese) {
            echo "<li>"
            . gerarLinkSintese($i, $sintese)
            . gerarLinkMover($i, count($tcc->sinteses_componentes), 'Sintese', -1, $sintese->id)
            . gerarLinkMover($i, count($tcc->sinteses_componentes), 'Sintese', 1, $sintese->id)
            . "</li>";
        } ?>
        </ul>
        <br>
        <?php echo CHtml::link('Cadastrar nova síntese', [
            'cadastrarSinteseComponente', 'tccId' => $tcc->id
        ]); ?>
    </fieldset>

    <fieldset>
        <legend>Ideias e propostas de aplicação pedagógica de tecnologias digitais (3 ou mais)</legend>
        <ul>
        <?php foreach ($tcc->propostas_pedagogicas as $i => $proposta) {
            echo "<li>"
            . gerarLinkProposta($i, $proposta)
            . gerarLinkMover($i, count($tcc->propostas_pedagogicas), 'Proposta', -1, $proposta->id)
            . gerarLinkMover($i, count($tcc->propostas_pedagogicas), 'Proposta', 1, $proposta->id)
            . "</li>";
        } ?>
        </ul>
        <br>
        <?php echo CHtml::link('Cadastrar nova proposta', [
            'cadastrarPropostaPedagogica', 'tccId' => $tcc->id,
        ]); ?>
    </fieldset>

<?php } ?>

<?php if ($ehCoordenacao) { ?>

    <h1 style="margin-top: 150px">Orientadores e considerações</h1>

    <fieldset>
    <legend>Validação</legend>

    <div class="row">
        <label>Versão de validação</label>
        <?php if ($tcc->validacao_arquivo) { ?>
        <a href="<?php echo $tcc->recuperarArquivoValidacao(); ?>" target="_blank">Visualizar</a>
        <?php } else { ?>
        <p>Não entregue</p>
        <?php } ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'validacao_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'validacao_data_entrega', ['class' => 'curto', 'disabled' => true]); ?>
    </div>

    <?php if ($tcc->recuperarStatus() == Tcc::FASE_VERSAO_PARA_VALIDACAO_ENTREGUE) { ?>
    <div class="row">
        <p style="color: red; font-size: 1.3em; margin: 30px 0">&gt; Atribuição de pré-orientador pendente</p>
    </div>
    <?php } ?>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'validacao_orientador_cpf'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'validacao_orientador_cpf',
            $listaDocentes,
            [
                'empty' => 'Selecione o pré-orientador',
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_VERSAO_PARA_VALIDACAO_ENTREGUE,
            ]
        );
        ?>
        <?php echo $form->error($tcc, 'validacao_orientador_cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'validacao_consideracoes'); ?>
        <?php echo $form->textArea($tcc, 'validacao_consideracoes',
            [
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Considerações sobre o trabalho',
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_PRE_ORIENTADOR_ATRIBUIDO,
            ]);
        ?>
        <?php echo $form->error($tcc, 'validacao_consideracoes'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($tcc, 'validacao_tem_pendencias'); ?>
        <?php echo $form->radioButton($tcc, 'validacao_tem_pendencias', ['id' => 'validacao_tem_pendencias_sim', 'value' => true, 'disabled' => $tcc->recuperarStatus() != Tcc::FASE_PRE_ORIENTADOR_ATRIBUIDO, 'uncheckValue' => null]); ?>
        <label for="validacao_tem_pendencias_sim">Sim</label>
        <?php echo $form->radioButton($tcc, 'validacao_tem_pendencias', ['id' => 'validacao_tem_pendencias_nao', 'value' => false, 'disabled' => $tcc->recuperarStatus() != Tcc::FASE_PRE_ORIENTADOR_ATRIBUIDO, 'uncheckValue' => null]); ?>
        <label for="validacao_tem_pendencias_nao">Não</label>
    </div>

    </fieldset>

    <fieldset>
    <legend>Banca</legend>

    <div class="row">
        <label>Versão da banca</label>
        <?php if ($tcc->banca_arquivo) { ?>
        <a href="<?php echo $tcc->recuperarArquivoBanca(); ?>" target="_blank">Visualizar</a>
        <?php } else { ?>
        <p>Não entregue</p>
        <?php } ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'banca_data_entrega', ['class' => 'curto', 'disabled' => true]); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_data_apresentacao'); ?>
        <?php echo $form->textField($tcc, 'banca_data_apresentacao', ['class' => 'curto', 'placeholder' => 'Formato: dd/mm/aaaa']); ?>
        <?php echo $form->error($tcc, 'banca_data_apresentacao'); ?>
    </div>

    <?php if ($tcc->recuperarStatus() == Tcc::FASE_VERSAO_DA_BANCA_ENTREGUE) { ?>
    <div class="row">
        <p style="color: red; font-size: 1.3em; margin: 30px 0">&gt; Atribuição de banca pendente</p>
    </div>
    <?php } ?>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro1_cpf'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'banca_membro1_cpf',
            $listaDocentes,
            [
                'empty' => 'Selecione o primeiro membro da banca',
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_VERSAO_DA_BANCA_ENTREGUE,
            ]
        );
        ?>
        <?php echo $form->error($tcc, 'banca_membro1_cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro2_cpf'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'banca_membro2_cpf',
            $listaDocentes,
            [
                'empty' => 'Selecione o segundo membro da banca',
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_VERSAO_DA_BANCA_ENTREGUE,
            ]
        );
        ?>
        <?php echo $form->error($tcc, 'banca_membro2_cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro3_cpf'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'banca_membro3_cpf',
            $listaDocentes,
            [
                'empty' => 'Selecione o terceiro membro da banca',
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_VERSAO_DA_BANCA_ENTREGUE,
            ]
        );
        ?>
        <?php echo $form->error($tcc, 'banca_membro3_cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro1_consideracoes'); ?>
        <?php echo $form->textArea($tcc, 'banca_membro1_consideracoes',
            [
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_BANCA_ATRIBUIDA,
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Considerações sobre o trabalho',
            ]);
        ?>
        <?php echo $form->error($tcc, 'banca_membro1_consideracoes'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro2_consideracoes'); ?>
        <?php echo $form->textArea($tcc, 'banca_membro2_consideracoes',
            [
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_BANCA_ATRIBUIDA,
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Considerações sobre o trabalho',
            ]);
        ?>
        <?php echo $form->error($tcc, 'banca_membro2_consideracoes'); ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'banca_membro3_consideracoes'); ?>
        <?php echo $form->textArea($tcc, 'banca_membro3_consideracoes',
            [
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_BANCA_ATRIBUIDA,
                'maxlength' => 3000,
                'rows' => 10,
                'cols' => 200,
                'placeholder' => 'Considerações sobre o trabalho',
            ]);
        ?>
        <?php echo $form->error($tcc, 'banca_membro3_consideracoes'); ?>
    </div>

    <div class="row">
        <?php echo $form->label($tcc, 'banca_tem_pendencias'); ?>
        <?php echo $form->radioButton($tcc, 'banca_tem_pendencias', ['id' => 'banca_tem_pendencias_sim', 'value' => true, 'disabled' => $tcc->recuperarStatus() != Tcc::FASE_BANCA_ATRIBUIDA, 'uncheckValue' => null]); ?>
        <label for="banca_tem_pendencias_sim">Sim</label>
        <?php echo $form->radioButton($tcc, 'banca_tem_pendencias', ['id' => 'banca_tem_pendencias_nao', 'value' => false, 'disabled' => $tcc->recuperarStatus() != Tcc::FASE_BANCA_ATRIBUIDA, 'uncheckValue' => null]); ?>
        <label for="banca_tem_pendencias_nao">Não</label>
    </div>

    </fieldset>

    <fieldset>
    <legend>Versão final</legend>

    <div class="row">
        <label>Versão final DOC</label>
        <?php if ($tcc->final_arquivo_doc) { ?>
        <a href="<?php echo $tcc->recuperarArquivoFinalDoc(); ?>" target="_blank">Visualizar</a>
        <?php } else { ?>
        <p>Não entregue</p>
        <?php } ?>
    </div>
    <div class="row">
        <label>Versão final PDF</label>
        <?php if ($tcc->final_arquivo_pdf) { ?>
        <a href="<?php echo $tcc->recuperarArquivoFinalPdf(); ?>" target="_blank">Visualizar</a>
        <?php } else { ?>
        <p>Não entregue</p>
        <?php } ?>
    </div>
    <div class="row">
        <?php echo $form->labelEx($tcc, 'final_data_entrega'); ?>
        <?php echo $form->textField($tcc, 'final_data_entrega', ['class' => 'curto', 'disabled' => true]); ?>
    </div>

    <?php if ($tcc->recuperarStatus() == Tcc::FASE_VERSAO_FINAL_ENTREGUE) { ?>
    <div class="row">
        <p style="color: red; font-size: 1.3em; margin: 30px 0">&gt; Atribuição de banca pendente</p>
    </div>
    <?php } ?>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'final_orientador_cpf'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'final_orientador_cpf',
            $listaDocentes,
            [
                'empty' => 'Selecione o orientador',
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_VERSAO_FINAL_ENTREGUE,
            ]
        );
        ?>
        <?php echo $form->error($tcc, 'final_orientador_cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'final_coorientador_cpf'); ?>
        <?php
        echo $form->dropDownList(
            $tcc,
            'final_coorientador_cpf',
            $listaDocentes,
            [
                'empty' => 'Selecione o coorientador',
                'disabled' => $tcc->recuperarStatus() != Tcc::FASE_VERSAO_FINAL_ENTREGUE,
            ]
        );
        ?>
        <?php echo $form->error($tcc, 'final_coorientador_cpf'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($tcc, 'aprovado'); ?>
        <?php echo $form->checkBox($tcc, 'aprovado', ['disabled' => $tcc->recuperarStatus() != Tcc::FASE_ORIENTADOR_FINAL_ATRIBUIDO]); ?>
    </div>

    </fieldset>

    <hr>

<?php } ?>

    <div class="row buttons" style="margin-top: 30px">
        <label></label>
        <?php echo CHtml::submitButton('Salvar', [
            'class' => 'btn btn-success btn-lg',
            'name' => 'salvar',
        ]); ?>
    </div>
    <?php if (!$tcc->isNewRecord) { ?>
        <div class="row buttons" style="margin-top: 10px">
            <label></label>
            <?php echo CHtml::submitButton('Exportar DOCX', [
                'class' => 'btn btn-success btn-lg',
                'name' => 'exportar',
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