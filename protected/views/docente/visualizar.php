<?php

function gerarCertificado($docenteCpf, $ofertaId) {
    return CHtml::link('Certificado', [
        'admin/gerarAtestadoDocencia',
        'docenteCpf' => $docenteCpf,
        'ofertaId' => $ofertaId,
    ]);
}

function gerarCertificadoOrientacao($docenteCpf, $tccId) {
    return CHtml::link('Certificado', [
        'admin/gerarAtestadoOrientador',
        'docenteCpf' => $docenteCpf,
        'tccId' => $tccId,
    ]);
}

function gerarCertificadoBanca($docenteCpf, $tccId) {
    return CHtml::link('Certificado', [
        'admin/gerarAtestadoBanca',
        'docenteCpf' => $docenteCpf,
        'tccId' => $tccId,
    ]);
}

?>

<h1>Visualizando docente <?php echo "{$model->nome} {$model->sobrenome}"; ?> (<?php echo $model->cpf; ?>)</h1>

<p>E-mail: <?php echo $model->email; ?></p>

<h2>Ofertas ministradas</h2>

<ul>
<?php foreach ($model->ofertas as $oferta) { ?>
    <li>
        <?php echo "{$oferta->componenteCurricular->nome} ({$oferta->mes}/{$oferta->ano})"; ?> - 
        <?php echo gerarCertificado($model->cpf, $oferta->id); ?>
    </li>
<?php } ?>
</ul>

<h2>TCCs orientados</h2>

<ul>
<?php
    foreach ($tccsOrientados as $tcc) {
        if ($tcc->ehOrientador($model->cpf)) {
?>
    <li>
        <?php echo "{$tcc->titulo} - {$tcc->inscricao->nomeCompleto}"; ?> - 
        <?php echo gerarCertificadoOrientacao($model->cpf, $tcc->id); ?>
    </li>
<?php }} ?>
</ul>

<h2>TCCs em que é membro de banca</h2>

<ul>
<?php
    foreach ($tccsOrientados as $tcc) {
        if ($tcc->ehMembroDaBanca($model->cpf)) {
?>
    <li>
        <?php echo "{$tcc->titulo} - {$tcc->inscricao->nomeCompleto}"; ?> - 
        <?php echo gerarCertificadoBanca($model->cpf, $tcc->id); ?>
    </li>
<?php }} ?>
</ul>