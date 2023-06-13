<?php
/* @var $this AlunoController */
/* @var $aluno Inscricao */
/* @var $habilitacoes Habilitacao[] */

$this->breadcrumbs = ['TCC', 'Cadastro'];

function gerarLinkTcc($aluno, $habilitacao)
{
    $tcc = Tcc::model()->findByAttributes([
        'inscricao_id' => $aluno->id,
        'habilitacao_id' => $habilitacao->id,
    ]);
    return $tcc
        ? CHtml::link("{$tcc->titulo}", array("aluno/editarTcc&id={$tcc->id}"))
        : CHtml::link('Cadastrar TCC', array("aluno/cadastrarTcc&habilitacaoId={$habilitacao->id}"));
}
?>

<h1>Cadastro de TCC</h1>

<ul>
<?php foreach ($habilitacoes as $i => $habilitacao) { ?>
    <h3>Habilitação <?php echo ($i + 1) . ': ' . $habilitacao->nome; ?></h3>
    <?php echo gerarLinkTcc($aluno, $habilitacao); ?>
<?php } ?>
</ul>
