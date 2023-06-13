<?php
/**
 * @property Inscricao $inscricao
 */
Yii::app()->clientScript->registerScript('scripts_locais', "
    // Remove os storages utilizados na inscrição
    sessionStorage.clear();
");
?>

<h1>Inscrição realizada com sucesso!</h1>
<br>
<p>Caro(a) <b><?php echo $inscricao->nomeCompleto; ?></b>, sua inscrição foi
    realizada com sucesso!</p>
<p>Você receberá um e-mail com instruções de acesso a sua área virtual. Em
    sua área virtual você poderá alterar seu perfil e enviar a documentação
    necessária para finalizar sua inscrição.</p>
<p>Obrigado!</p>
<p>Secretaria do Curso Educação e Tecnologias</p>
