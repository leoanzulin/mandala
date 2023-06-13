<?php
$this->breadcrumbs = array();
?>

<h1>Inscrições feitas em ofertas</h1>
<br>
<h2>Aluno(a): <?php echo "{$inscricao->nomeCompleto}"; ?></h2>

<?php
    if ($inscricao->ehAlunoDeEspecializacao()) {
        $this->renderPartial('/comuns/_habilitacoesEscolhidas', array('inscricao' => $inscricao));
        $this->renderPartial('/comuns/_legendasPrioridades');
    }
?>
<br>

<!-- http://qiita.com/yorkxin/items/c5899314d63214fb5409 -->
<div ng-app="recuperarInscricoesEmOfertas"
     ng-controller="controlador"
     ng-init="tipoDeCurso = <?php echo $inscricao->tipo_curso; ?>"
     data-id-vem-da-url="true">

     <div style="display: flex">
        <div style="width: 65%; min-width: 500px">
            <tabela-de-inscricao-em-ofertas periodos="periodos"
                                            habilitacoes="habilitacoes"
                                            nivel-de-edicao="visualizacao"
                                            mes-atual="{{mesAtual}}"
                                            ano-atual="{{anoAtual}}"
                                            mostrar-ofertas-passadas="true">
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
</div>
