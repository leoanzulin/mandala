<?php
$titulo = $model instanceof Docente ? 'ministradas' : 'em que sou tutor';
if ($model instanceof Docente) {
    $modelTutor = Tutor::model()->findByPk($model->cpf);
}
?>

<h2>Ofertas <?php echo $titulo; ?></h2>

<ul>
<?php
foreach ($model->ofertas as $oferta) {
    if ($oferta->estaNoPassado()) {
?>
    <li><?php echo CHtml::link($oferta->recuperarNome(), ['verOferta', 'id' => $oferta->id]); ?></li>
<?php }} ?>
</ul>

<h2>Ofertas futuras</h2>

<ul>
<?php
foreach ($model->ofertas as $oferta) {
    if (!$oferta->estaNoPassado()) {
?>
    <li><?php echo CHtml::link($oferta->recuperarNome(), ['verOferta', 'id' => $oferta->id]); ?></li>
<?php }} ?>
</ul>

<?php if (!empty($modelTutor)) { ?>

<hr>

<h2>Ofertas em que sou tutor</h2>

<ul>
<?php
foreach ($modelTutor->ofertas as $oferta) {
    if ($oferta->estaNoPassado()) {
?>
    <li><?php echo CHtml::link($oferta->recuperarNome(), ['verOferta', 'id' => $oferta->id]); ?></li>
<?php }} ?>
</ul>

<h2>Ofertas futuras</h2>

<ul>
<?php
foreach ($modelTutor->ofertas as $oferta) {
    if (!$oferta->estaNoPassado()) {
?>
    <li><?php echo CHtml::link($oferta->recuperarNome(), ['verOferta', 'id' => $oferta->id]); ?></li>
<?php }} ?>
</ul>

<?php } ?>