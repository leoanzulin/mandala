<?php
/* @var $this AlunoController */
/* @var $aluno Inscricao */
/* @var $habilitacoes Habilitacao[] */

$this->breadcrumbs = ['TCC'];

?>

<h1>TCC</h1>

<p>Você pode
<?php echo CHtml::link("cadastrar seus TCC", ["aluno/criarTcc"]); ?>
 ou
<?php echo CHtml::link("entregar seus TCC", ["aluno/entregarTccs"]); ?>
</p>
