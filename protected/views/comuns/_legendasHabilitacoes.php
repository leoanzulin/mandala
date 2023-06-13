<h3>Legenda das habilitações</h3>
<ul>
    <?php
    $habilitacoes = Habilitacao::findAllValid();
    foreach ($habilitacoes as $habilitacao) {
        echo "<li><b>{$habilitacao->letra}</b>: {$habilitacao->nome}</li>\n";
    }
    ?>
</ul>
