<?php /* @var Inscricao $inscricao */ ?>
<?php /* @var  */ ?>
<h3>Habilitações escolhidas:</h3>
<ul>
    <?php
    $i = 1;
    foreach ($inscricao->recuperarHabilitacoes() as $habilitacao) {
        echo "<li><b>H{$i}</b>: {$habilitacao}</li>\n";
        $i++;
    }
    ?>
</ul>
