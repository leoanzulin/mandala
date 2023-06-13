<?php
$this->breadcrumbs = array(
    'Simulador',
);
?>

<h2>Simulador de grade curricular</h2>

<p class="atencao">As informações contidas no simulador não são oficiais, as informações para cadastro no ambiente virtual são coletadas da aba "Fazer inscrição em componentes"</p>

<?php $this->renderPartial('/comuns/_habilitacoesEscolhidas', array('inscricao' => $model)); ?>

<div class="simulador-container">

    <h2>Grade curricular</h2>

    <?php $this->renderPartial('/comuns/_legendas'); ?>

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'preinscricao-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <div ng-app="recuperarInscricoesEmOfertas" ng-controller="controlador">
        <tabela-do-simulador periodos="periodos"></tabela-do-simulador>
        <contagem-de-componentes periodos="periodos"
                                 habilitacoes="habilitacoes"
                                 tipo-de-ofertas="simulador">
        </contagem-de-componentes>
    </div>

    <?php
    echo CHtml::submitButton('Salvar simulação', array(
        'class' => 'btn btn-success btn-lg',
        'name' => 'Salvar',
    ));
    ?>

    <input formtarget="_blank" class="btn btn-lg" type="submit" name="gerar_pdf" value="Gerar PDF" />
    <?php $this->endWidget(); ?>

</div>
