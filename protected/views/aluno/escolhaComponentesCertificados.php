<?php
$this->breadcrumbs = array(
    'Escolha de componentes para certificados',
);
?>

<?php if (!empty($mensagensDeErro)) { ?>
<div class="mensagem-de-erro">
    <ul>
        <?php
            foreach ($mensagensDeErro as $mensagemDeErro) {
                echo "<li>{$mensagemDeErro}</li>";
            }
        ?>
    </ul>
</div>
<?php } ?>

<h1>Escolha de componentes para certificados</h1>
<br>

<p>
Nesta tela você pode fazer a seleção de quais componentes gostaria que constassem em cada um de seus
certificados de conclusão de curso.
</p>

<p>
As seleções feitas aqui <b>NÃO</b> mudam as escolhas feitas em sua trilha pedagógica.
</p>

<?php $this->renderPartial('/comuns/_habilitacoesEscolhidas', array('inscricao' => $inscricao)); ?>
<?php $this->renderPartial('/comuns/_legendasPrioridades'); ?>
<br>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'escolha-componentes-certificado-form',
    'enableAjaxValidation' => false,
));
?>

<div
    ng-app="recuperarInscricoesEmOfertas"
    ng-controller="controlador"
    selecao-para-certificados="true"
>

    <div style="display: flex">
        <div style="width: 65%; min-width: 500px">
            <tabela-de-inscricao-em-ofertas periodos="periodos"
                                            habilitacoes="habilitacoes"
                                            nivel-de-edicao="admin"
                                            mes-atual="{{mesAtual}}"
                                            ano-atual="{{anoAtual}}"
                                            mostrar-ofertas-passadas="true"
                                            constantes="constantes"
                                            tipo-de-curso="{{tipoDeCurso}}"
                                            selecao-para-certificados="true">
            </tabela-de-inscricao-em-ofertas>
        </div>

        <div style="width: 35%; min-width: 200px">
            <contagem-de-componentes periodos="periodos"
                                     habilitacoes="habilitacoes">
            </contagem-de-componentes>
        </div>
    </div>

    <input class="btn btn-success btn-lg" name="Salvar" type="submit" value="Salvar" ng-click="validarNumeroDeInscricoes($event)">
</div>

<?php $this->endWidget(); ?>
