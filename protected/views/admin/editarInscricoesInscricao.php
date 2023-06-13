<?php
$this->breadcrumbs = array(
    'Inscrição em ofertas',
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

<h1>Inscrição em ofertas</h1>
<br>

<h2>Aluno: <?php echo "{$inscricao->nomeCompleto} (CPF: {$inscricao->cpf})"; ?></h2>

<?php
    if ($inscricao->ehAlunoDeEspecializacao()) {
        $this->renderPartial('/comuns/_habilitacoesEscolhidas', array('inscricao' => $inscricao));
        $this->renderPartial('/comuns/_legendasPrioridades');
    }
?>
<br>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'inscricao-ofertas-form',
    'enableAjaxValidation' => false,
));
?>

<div ng-app="recuperarInscricoesEmOfertas"
     ng-controller="controlador"
     ng-init="tipoDeCurso = <?php echo $inscricao->tipo_curso; ?>"
     data-id-vem-da-url="true"
>

    <input class="btn btn-danger btn-lg" name="limpar_historico" type="submit" value="Limpar histórico deste aluno" ng-click="limparHistorico($event)">

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
                                            houve-mudanca="houveMudanca()"
            >
            </tabela-de-inscricao-em-ofertas>
        </div>

        <div style="width: 35%; min-width: 200px">
            <contagem-de-componentes-simples ng-if="tipoDeCurso != 2 && habilitacoes.length == 0"
                                            periodos="periodos">
            </contagem-de-componentes-simples>
            <contagem-de-componentes ng-if="habilitacoes.length > 0"
                                    periodos="periodos"
                                    habilitacoes="habilitacoes">
            </contagem-de-componentes>
        </div>
    </div>

    <input class="btn btn-success btn-lg" name="Salvar" type="submit" value="Salvar inscrição" ng-click="validarNumeroDeInscricoes($event)">
    <input formtarget="_blank" class="btn btn-lg" type="submit" name="gerar_pdf" value="Gerar PDF" ng-click="gerarPdf($event)">
</div>

<?php $this->endWidget(); ?>
