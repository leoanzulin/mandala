<?php

function gerarCertificado($tutorCpf, $ofertaId) {
    return CHtml::link('Certificado', [
        'admin/gerarAtestadoTutoria',
        'tutorCpf' => $tutorCpf,
        'ofertaId' => $ofertaId,
    ]);
}

?>

<h1>Visualizando tutor <?php echo "{$model->nome} {$model->sobrenome}"; ?> (<?php echo $model->cpf; ?>)</h1>

<p>E-mail: <?php echo $model->email; ?></p>

<h2>Ofertas em que atua</h2>

<ul>
<?php foreach ($model->ofertas as $oferta) { ?>
    <li>
        <?php echo "{$oferta->componenteCurricular->nome} ({$oferta->mes}/{$oferta->ano})"; ?> - 
        <?php echo gerarCertificado($model->cpf, $oferta->id); ?>
    </li>
<?php } ?>
</ul>
