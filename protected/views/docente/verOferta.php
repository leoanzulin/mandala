<?php
$this->breadcrumbs = [
    'Ofertas' => ['verOfertas'],
    $oferta->recuperarNome(),
];

function linkAtestado($model, $oferta)
{
    if ($model instanceof Docente && !$oferta->ehTutorNestaOferta($model->cpf)) {
        return '<li>' . CHtml::link('Atestado de docência', ['gerarAtestadoDocencia', 'ofertaId' => $oferta->id]) . '</li>';
    } else {
        return '<li>' . CHtml::link('Atestado de tutoria', ['gerarAtestadoTutoria', 'ofertaId' => $oferta->id]) . '</li>';
    }
}
?>

<h2><?php echo $oferta->recuperarNome(); ?></h2>

<?php echo linkAtestado($model, $oferta); ?>

<h3>Docentes</h3>
<ul>
<?php foreach ($oferta->docentes as $docente) { ?>
    <li><?php echo $docente->nomeCompleto; ?></li>
<?php } ?>
</ul>

<h3>Tutores</h3>
<ul>
<?php foreach ($oferta->tutores as $tutor) { ?>
    <li><?php echo $tutor->nomeCompleto; ?></li>
<?php } ?>
</ul>

<h3>Inscrições</h3>

<p>Nũmero de inscrições: <?php echo count($oferta->inscricoes); ?></p>
<ul>
<?php foreach ($oferta->inscricoes as $inscricao) { ?>
    <li><?php echo $inscricao->nomeCompleto; ?></li>
<?php } ?>
</ul>
