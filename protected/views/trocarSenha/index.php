<?php

?>

<h1>Troca de senha</h1>

<br>
<p>Caro(a) <?php echo $nome; ?>, informe sua nova senha:</p>
<br>

<form id="trocar-senha-form" method="post" action="<?php echo Yii::app()->request->requestUri; ?>">

    <?php if ($ocorreuErro) { ?>
    <div class="row">
        <span class="errorMessage">Ocorreu um erro, tente novamente</span>
    </div>
    <br>
    <?php } ?>

    <div class="row">
        <label for="senha">Nova senha</label>
        <input type="password" value="" name="senha" id="senha">
    </div>
    <div class="row">
        <label for="senha">Confirme a nova senha</label>
        <input type="password" value="" name="senha_confirmar" id="senha">
    </div>

    <br>
    <div class="row">
        <label></label>
        <input type="submit" value="Salvar" name="salvar">
    </div>

</form>


