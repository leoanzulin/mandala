<?php ?>

<h1>Exportar arquivos de notas para o ProExWeb</h1>

<ul>
    <li><?php echo CHtml::link('Notas de todas as ofertas', ['exportador/notasProex']); ?></li>
</ul>

<ul>
<?php
    $ultimoMes = -1;
    foreach ($ofertas as $oferta) {
        if ($ultimoMes != $oferta['mes']) {
            echo "<br><li><b>{$oferta['mes']}/{$oferta['ano']}</b></li>";
            $ultimoMes = $oferta['mes'];
        }
?>
    <li><?php echo CHtml::link("{$oferta['nome']}", ['exportador/notasProexOferta', 'ofertaId' => $oferta['id']]); ?></li>
<?php
    }
?>
</ul>
