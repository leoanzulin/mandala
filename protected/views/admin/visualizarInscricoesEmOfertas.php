<?php
/* @var $ofertasPorPeriodos */

$this->breadcrumbs = array(
    'Inscrição em ofertas',
);
?>

<h1>Visualizar número de inscrições por ofertas</h1>
<br>
<p>São contabilizadas apenas inscriçẽos <b>ATIVAS</b> no momento (alunos já formados ou cancelados não contam)</p>

<?php
foreach ($ofertasPorPeriodos as $periodo) {
?>
    <h2>Ofertas de <?php echo "{$periodo['mes']}/{$periodo['ano']}"; ?></h2>

    <table class="table tabela-inscricao">
        <tr><th>Componente Curricular</th><th>Nº de inscrições</th></tr>

        <?php foreach ($periodo['ofertas'] as $oferta) { ?>
            <tr>
                <td><b><?php echo $oferta['componente']['nome']; ?></b><br>Docentes: <?php echo $oferta['nomesDocentes']; ?></td>
                <td><?php echo $oferta['numeroDeInscricoesAtivas']; ?></td>
            </tr>
        <?php } ?>

    </table>

<?php
}
?>

