<?php /* @var $ofertasPorPeriodo */ ?>

<h1>Gerenciar notas</h1>
<br>

<?php
$this->beginWidget('CActiveForm', array('id' => 'gerenciar-notas-form'));
echo CHtml::submitButton('Sincronizar notas com o Moodle', array(
    'class' => 'btn btn-success btn-lg',
    'name' => 'sincronizar'
));
$this->endWidget();

foreach ($ofertasPorPeriodos as $periodo) {
    echo "<h2>Ofertas de {$periodo['mes']}/{$periodo['ano']}</h2>\n";

    $ofertasDestePeriodo = Oferta::model()->findAllByAttributes(array(
        'ano' => $periodo['ano'],
        'mes' => $periodo['mes'],
    ), array(
        'order' => 'componente_curricular_id',
    ));

    echo "<ul>";
    foreach ($periodo['ofertas'] as $oferta) {
        echo "<li>" . CHtml::link($oferta['nome'], array('/admin/gerenciarNotasDaOferta', 'id' => $oferta['id'])) . "</li>";
    }
    echo "</ul>";
}
?>
