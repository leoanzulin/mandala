<?php

Yii::app()->clientScript->registerScript('scripts_locais', "

$(document).ready(function() {
    /**
    * Mostra ou esconde os detalhes na página de visualização das pré-inscrições
    * em componentes.
    */
    $('#tabela-inscricoes tr[data-inscricao]').click(function() {
        idInscricao = $(this).data('inscricao');
        $('#inscricao' + idInscricao).toggle('fast');

        texto = $(this).text().trim();
        if (texto.indexOf('▶') != -1) {
            texto = '▼' + texto.slice(1);
        }
        else {
            texto = '▶' + texto.slice(1);
        }
        $(this).html('<td>' + texto + '</td>');
    });
});

");

function imprimirLinha($inscricao, $componente)
{
    $classe1 = 'habilitacao ' . $componente->classeCssParaHabilitacao($inscricao->recuperarHabilitacao1());
    $prioridade1 = $componente->prioridadeParaHabilitacao($inscricao->recuperarHabilitacao1());

    $classe2 = 'habilitacao ' . $componente->classeCssParaHabilitacao($inscricao->recuperarHabilitacao2());
    $prioridade2 = $componente->prioridadeParaHabilitacao($inscricao->recuperarHabilitacao2());

    echo "<tr><td>{$componente->nome}</td>";
    echo "<td class=\"{$classe1}\">{$prioridade1}</td>";
    echo "<td class=\"{$classe2}\">{$prioridade2}</td></tr>";
}

?>

<?php /*

<h1>Visualizar pré-inscrições em componentes</h1>
<br>

<table id="tabela-inscricoes" class="inscricoes">
<?php
    foreach ($inscricoes as $inscricao) {
?> 
    <tr data-inscricao="<?php echo $inscricao->id; ?>">
        <td>▶ <?php echo "{$inscricao->nome} {$inscricao->sobrenome} ($inscricao->cpf)"; ?></td>
    </tr>
    <tr id="inscricao<?php echo $inscricao->id; ?>" class="detalhes"><td>
        <ul>
<?php
        echo "<li>Habilitação 1: {$inscricao->habilitacao1PorExtenso}</li>";
        if (!empty($inscricao->habilitacao2)) {
            echo "<li>Habilitação 2: {$inscricao->habilitacao2PorExtenso}</li>";
        }
        echo "</ul>";

        if (!$inscricao->recuperarPreInscricoesPorPeriodo()) {?>
            <h3>Esta inscrição ainda não fez nenhuma pré-inscrição</h3>
<?php   }
        else {
            $periodoNumero = 1;
            foreach ($inscricao->recuperarPreInscricoesPorPeriodo() as $componentesPorPeriodo) {
?>
        <h3>Período <?php echo $periodoNumero++; ?></h3>
        <table class="simulador">
            <tr><th>Componente Curricular</th><th class="habilitacao">H1</th><th class="habilitacao">H2</th></tr>
<?php
                foreach ($componentesPorPeriodo as $componente) {
                    imprimirLinha($inscricao, $componente);
                }
                echo "</table>\n";
            }
        }
        echo "</td></tr>\n";
    }
?>
</table>

<br>

*/ ?>

<h1>Inscrições por componente (simulador)</h1>

<table class="inscricoes-resumo">
    <tr><th>Compponente curricular</th><th>Nº de inscrições</th></tr>
<?php
    $componentes = ComponenteCurricular::model()->findAll();
    foreach ($componentes as $componente) {
        $numeroInscricoes = count($componente->preinscricoes);
        echo "<tr><td>{$componente->nome}</td><td class=\"centralizar\">{$numeroInscricoes}</td></tr>\n";
    }
?>
</table>
