<?php
?>

<h1>Visualizando docente <?php echo "{$model->nome} {$model->sobrenome}"; ?> (<?php echo $model->cpf; ?>)</h1>

<p>E-mail: <?php echo $model->email; ?></p>

<h2>Ofertas ministradas</h2>

<ul>
<?php foreach ($model->ofertas as $oferta) { ?>
    <li><?php echo "{$oferta->componenteCurricular->nome} ({$oferta->mes}/{$oferta->ano})"; ?></li>
<?php } ?>
</ul>

<h2>Bolsas recebidas</h2>

<ul>
<?php foreach ($model->bolsas as $bolsa) { ?>
    <li><?php echo $bolsa; ?></li>
<?php } ?>
</ul>
