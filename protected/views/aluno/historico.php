<?php
/* @var $this Controller */
/* @var $inscricao */
/* @var $ofertasPorPeriodo */

function recuperarPrioridadeDaOfertaParaHabilitacao($oferta, $habilitacao) {
    foreach ($oferta['componente']['prioridades'] as $prioridadeHabilitacao) {
        if ($prioridadeHabilitacao['id'] == $habilitacao->id) {
            return $prioridadeHabilitacao['letra'];
        }
    }
}
?>

<h1>Histórico de componentes cursadas de <?php echo $inscricao->nomeCompleto; ?></h1>

<?php
    if ($inscricao->ehAlunoDeEspecializacao()) {
        $this->renderPartial('/comuns/_habilitacoesEscolhidas', array('inscricao' => $inscricao));
        $this->renderPartial('/comuns/_legendasPrioridades');
    }
?>

<?php
foreach ($ofertasPorPeriodo as $periodo) {
?>
    <br><h2>Ofertas de <?php echo "{$periodo['mes']}/{$periodo['ano']}"; ?></h2>

    <table class="historico">
        <thead>
            <tr>
                <th>Componente curricular</th>
<?php foreach ($inscricao->habilitacoes as $i => $habilitacao) {
    echo '<th>H' . ($i + 1) . '</th>';
} ?>
                <th>Nota virtual</th>
                <th>Nota presencial</th>
                <th>Média</th>
                <th>Frequência (%)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($periodo['ofertas'] as $oferta) { ?>
                <tr>
                    <td><?php echo $oferta['componente']['nome']; ?></td>
<?php foreach ($inscricao->habilitacoes as $habilitacao) {
    echo '<td>' . recuperarPrioridadeDaOfertaParaHabilitacao($oferta, $habilitacao) . '</td>';
} ?>
                    <td><?php echo $oferta['nota_virtual']; ?></td>
                    <td><?php echo $oferta['nota_presencial']; ?></td>
                    <td><?php echo $oferta['media']; ?></td>
                    <td><?php echo $oferta['frequencia']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>

<br><br>

<?php
$this->beginWidget('CActiveForm', array('id' => 'historico-form'));
echo CHtml::submitButton('Gerar PDF', array(
    'class' => 'btn btn-success btn-lg',
    'name' => 'gerar_pdf',
    'formtarget' => '_blank',
));
?>
<br><br>
<?php
echo CHtml::submitButton('Gerar PDF - histórico limpo', array(
    'class' => 'btn btn-success btn-lg',
    'name' => 'gerar_pdf_limpo',
    'formtarget' => '_blank',
));
$this->endWidget();
