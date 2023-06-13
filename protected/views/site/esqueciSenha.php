<h1>Esqueci minha senha</h1>
<br>

<p>Para criar uma nova senha, por favor forneça as seguintes informações:</p>

<form id="esqueci-senha-form" method="post">
    <div class="row">
        <label for="cpf">CPF</label><input id="cpf" type="text" value="" name="cpf" class="curto" maxlength="11">
    </div>
    <div class="row">
        <label for="data_nascimento">Data de nascimento</label>
        <?php $this->widget('CMaskedTextField', array(
            'name' => 'data_nascimento',
            'mask' => '99/99/9999',
            'htmlOptions' => array('size' => 10, 'maxlength' => 10, 'class' => 'curto'),
        )); ?>
    </div>
    <?php if ($houveErro) { ?>
        <label></label><div class="errorMessage">CPF não cadastrado ou data de nascimento não confere</div>
    <?php } ?>

    <div class="row">
        <label></label><input type="submit" value="Solicitar nova senha" name="solicitar">
    </div>
</form>

<p>Um e-mail com um link para troca de senha será enviado para você.</p>
