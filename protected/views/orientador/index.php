<?php

?>

<h1>Avaliar TCC</h1>

<p>Veja os trabalhos que estão
<?php echo CHtml::link("pendentes de avaliação", ["orientador/pendentes"]); ?>
 e os que já foram
<?php echo CHtml::link("avaliados", ["orientador/avaliados"]); ?>
</p>