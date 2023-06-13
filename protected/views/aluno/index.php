<?php
/* @var $this AlunoController */
/* @var $nome string */
/* @var $haInscricoesAConfirmar */
?>

<?php if ($haInscricoesAConfirmar) { ?>
<div style="padding: 15px; background-color: lightsalmon; border: 1px solid black">
    <p style="font-size: 1.5em; color: black; margin: 0">Por favor, confirme suas inscrições para o mês de <?php echo CalendarioHelper::nomeDoProximoMes(); ?> no link 
    <a href="<?php echo Yii::app()->createUrl('aluno/confirmarInscricoesEmOfertas'); ?>">
        <?php echo Yii::app()->createAbsoluteUrl('aluno/confirmarInscricoesEmOfertas'); ?>
    </a>.
</div>
<?php } ?>

<h2>Olá, <?php echo $nome; ?></h2>

<p>Seja bem vindo(a) ao sistema acadêmico do EduTec!</p>
<p>
    Aqui você terá acesso à sua trilha pedagógica (componentes curriculares escolhidos), ao seu histórico e poderá fazer 
    modificações no meu perfil.
</p>
