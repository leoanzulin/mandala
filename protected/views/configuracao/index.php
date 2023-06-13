<?php
?>

<h1>Configurações do sistema</h1>
<br>

<div class="form">

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'configuracao-form',
        'enableAjaxValidation' => false,
    ));
    ?>

    <fieldset>
        <legend>Administrador</legend>

        <div class="row">
            <?php echo $form->label($model, 'novaSenha'); ?>
            <?php echo $form->passwordField($model, 'novaSenha'); ?>
            <?php echo $form->error($model, 'novaSenha'); ?>
        </div>

        <div class="row">
            <?php echo $form->label($model, 'novaSenhaConfirmacao'); ?>
            <?php echo $form->passwordField($model, 'novaSenhaConfirmacao'); ?>
            <?php echo $form->error($model, 'novaSenhaConfirmacao'); ?>
        </div>
    </fieldset>

    <fieldset>
        <legend>Mensagens automáticas</legend>

        <p style="color: red; margin-bottom: 2em">ATENÇÃO: Não remova os placeholders (palavras entre colchetes) das mensagens pois eles são substituídos pelas informações correspondentes nos e-mails</p>

        <p style="font-size: 1.9em;  margin-top: 0.7em; margin-bottom: 1em">Docentes: Lembrete de ofertas no futuro próximo</p>
        <p>O período contém o número de dias antes da data de início da oferta em que esta mensagem será enviada para os docentes. Se quiser que a mensagem seja enviada em mais de um dia, separe-os por vírgula</p>
        <p>Exemplo: Se o valor "45, 15" for salvo, esta mensagem será enviada para os docentes 45 e 15 dias antes do início da oferta. </p>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteDocentePeriodos'); ?>
            <?php echo $form->textField($model, 'mensagemLembreteDocentePeriodos', ['class' => 'mensagem-automatica']); ?>
            <?php echo $form->error($model, 'mensagemLembreteDocentePeriodos'); ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteDocenteAssunto'); ?>
            <?php echo $form->textField($model, 'mensagemLembreteDocenteAssunto', ['class' => 'mensagem-automatica']); ?>
            <?php echo $form->error($model, 'mensagemLembreteDocenteAssunto'); ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteDocenteCorpo'); ?>
            <?php echo $form->textArea($model, 'mensagemLembreteDocenteCorpo', [
                'maxlength' => 1048576, 'rows' => 6, 'cols' => 100, 'class' => 'mensagem-automatica',
            ]); ?>
            <?php echo $form->error($model, 'mensagemLembreteDocenteCorpo'); ?>
        </div>
        <br>

        <p style="font-size: 1.9em;  margin-top: 0.7em; margin-bottom: 1em">Alunos: Lembrete de ofertas no próximo mês</p>
        <p>O período contém os dias do mês em que esta mensagem será enviada para os alunos. Se quiser que a mensagem seja enviada em mais de um dia, separe-os por vírgula. A mensagem é referente às ofertas do próximo mês.</p>
        <p>Exemplo: Se o valor "15, 20" for salvo, esta mensagem será enviada para os alunos nos dias 15 e 20 de cada mês.</p>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteAlunoProximoMesPeriodos'); ?>
            <?php echo $form->textField($model, 'mensagemLembreteAlunoProximoMesPeriodos', ['class' => 'mensagem-automatica']); ?>
            <?php echo $form->error($model, 'mensagemLembreteAlunoProximoMesPeriodos'); ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteAlunoProximoMesAssunto'); ?>
            <?php echo $form->textField($model, 'mensagemLembreteAlunoProximoMesAssunto', ['class' => 'mensagem-automatica']); ?>
            <?php echo $form->error($model, 'mensagemLembreteAlunoProximoMesAssunto'); ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteAlunoProximoMesCorpo'); ?>
            <?php echo $form->textArea($model, 'mensagemLembreteAlunoProximoMesCorpo', [
                'maxlength' => 1048576, 'rows' => 6, 'cols' => 100, 'class' => 'mensagem-automatica',
            ]); ?>
            <?php echo $form->error($model, 'mensagemLembreteAlunoProximoMesCorpo'); ?>
        </div>
        <br>

        <p style="font-size: 1.9em;  margin-top: 0.7em; margin-bottom: 1em">Alunos: Lembrete de ofertas neste semestre</p>
        <p>O período contém os dia/mês em que esta mensagem será enviada para os alunos. Se quiser que a mensagem seja enviada em mais de um dia, separe-os por vírgula. A mensagem é referente às ofertas do semestre em que a mesnagem está sendo enviada.</p>
        <p>Exemplo: Se o valor "05/01, 05/07" for salvo, esta mensagem será enviada para os alunos nos dias 5 de janeiro e 5 de julho de cada ano.</p>
        <p style="color: red">Importante: O formato DD/MM deve ser seguido corretamente, com dois dígitos para o dia e para o mês, caso contrário a mensagem poderá não ser enviada.</p>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteAlunoProximoSemestrePeriodos'); ?>
            <?php echo $form->textField($model, 'mensagemLembreteAlunoProximoSemestrePeriodos', ['class' => 'mensagem-automatica']); ?>
            <?php echo $form->error($model, 'mensagemLembreteAlunoProximoSemestrePeriodos'); ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteAlunoProximoSemestreAssunto'); ?>
            <?php echo $form->textField($model, 'mensagemLembreteAlunoProximoSemestreAssunto', ['class' => 'mensagem-automatica']); ?>
            <?php echo $form->error($model, 'mensagemLembreteAlunoProximoSemestreAssunto'); ?>
        </div>
        <div class="row">
            <?php echo $form->label($model, 'mensagemLembreteAlunoProximoSemestreCorpo'); ?>
            <?php echo $form->textArea($model, 'mensagemLembreteAlunoProximoSemestreCorpo', [
                'maxlength' => 1048576, 'rows' => 6, 'cols' => 100, 'class' => 'mensagem-automatica',
            ]); ?>
            <?php echo $form->error($model, 'mensagemLembreteAlunoProximoSemestreCorpo'); ?>
        </div>
        <br>

    </fieldset>

    <div class="row buttons">
        <label></label>
        <?php
        echo CHtml::submitButton('Salvar', array(
            'onClick' => 'js:return validar();',
        ));
        ?>
    </div>

    <?php $this->endWidget(); ?>

</div>
