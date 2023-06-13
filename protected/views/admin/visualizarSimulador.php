<?php
$this->breadcrumbs = array(
    'Visualizar simulador do(a) aluno(a) \'' . $inscricao->nomeCompleto . '\'',
);
?>

<h2>Simulador do(a) aluno(a) <?php echo $inscricao->nomeCompleto; ?> (CPF <?php echo $inscricao->cpf ?>)</h2>

<?php $this->renderPartial('/comuns/_habilitacoesEscolhidas', array('inscricao' => $inscricao)); ?>

<div class="simulador-container">
    <h2>Grade curricular</h2>

    <div ng-app="recuperarInscricoesEmOfertas"
         ng-controller="controlador"
         data-id-vem-da-url="true">
        <tabela-do-simulador modo="visualizar" periodos="periodos"></tabela-do-simulador>
        <contagem-de-componentes periodos="periodos"
                                 habilitacoes="habilitacoes"
                                 tipo-de-ofertas="simulador">
        </contagem-de-componentes>
    </div>

</div>
