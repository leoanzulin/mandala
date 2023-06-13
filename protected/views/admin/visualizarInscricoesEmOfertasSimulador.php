<?php ?>

<h1>Visualizar número de inscrições em ofertas (simulador)</h1>

<table class="numero-inscricoes-ofertas-resumo">
    <tr><th>Compponente curricular</th><th>Nº de inscrições</th></tr>
    <?php
    $ofertas = Oferta::model()->findAll(array('order' => 'ano, mes'));
    foreach ($ofertas as $oferta) {
        $inscricoesNestaOferta = InscricaoSimulador::model()->findAllByAttributes(array(
            'oferta_id' => $oferta->id
        ));
        $numeroInscricoes = count($inscricoesNestaOferta);
        echo "<tr><td>{$oferta->recuperarNome()}</td><td>{$numeroInscricoes}</td></tr>\n";
    }
    ?>
</table>
