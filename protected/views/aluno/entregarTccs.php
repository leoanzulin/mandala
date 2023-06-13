<?php
/* @var $this AlunoController */
/* @var $aluno Inscricao */
/* @var $habilitacoes Habilitacao[] */

$this->breadcrumbs = ['TCC', 'Entrega'];

function gerarLinkTcc($tcc)
{
    return CHtml::link("{$tcc->titulo}", array("aluno/entregarTcc&id={$tcc->id}"));
}
?>

<h1>Entrega de TCC</h1>

<p>Qual TCC deseja entregar?</p>

<ul>
<?php
foreach ($aluno->tccs as $tcc) {
    $linkTcc = gerarLinkTcc($tcc);
    echo "<li>{$linkTcc}</li>";
}
?>
</ul>
