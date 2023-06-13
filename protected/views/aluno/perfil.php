<?php
/* @var $this AlunoController */
/* @var $dataProvider CActiveDataProvider */

Yii::app()->clientScript->registerScript('scripts_locais', "
    // Remove os storages utilizados na inscrição
    sessionStorage.clear();
");

$this->breadcrumbs = array(
    'Meu perfil',
);

function linkAtestadoMatricula($inscricao)
{
    if ($inscricao->estaAtivo() && !$inscricao->finalizouCurso()) {
        return '<li>' . CHtml::link('Atestado de matrícula', ['aluno/gerarAtestadoMatricula']) . '</li>';
    }
}

function linkAtestadoCurso($inscricao)
{
    if ($inscricao->finalizouCurso()) {
        $tipo = $inscricao->tipoDeCursoPorExtenso();
        if ($inscricao->ehAlunoDeEspecializacao()) {
            $retorno = '';
            foreach ($inscricao->habilitacoes as $habilitacao) {
                $retorno .= '<li>' . CHtml::link("Atestado de conclusao de curso - {$habilitacao->nome}", ['aluno/gerarAtestadoEspecializacao', 'habilitacaoId' => $habilitacao->id]) . '</li>';
            }
            return $retorno;
        } else {
            $tipo = $inscricao->ehAlunoDeAperfeicoamento()
                ? 'Aperfeicoamento'
                : 'Extensao';
            return  '<li>' . CHtml::link('Atestado de conclusao de curso', ["aluno/gerarAtestado{$tipo}"]) . '</li>';
        }
    }
}

?>

<h1>Meu perfil</h1>

<ul>
    <li><?php echo CHtml::link('Editar perfil', array('aluno/editarPerfil')); ?></li>
    <li><?php echo CHtml::link('Trocar senha', array('aluno/trocarSenha')); ?></li>
    <li><?php echo CHtml::link('Histórico', array('aluno/historico')); ?></li>
    <?php /*
    <?php if ($model->ehAlunoDeEspecializacao() && count($model->habilitacoes) > 1) { ?>
    <li><?php echo CHtml::link('Escolha de componentes para certificados', array('aluno/escolhaComponentesCertificados')); ?></li>
    <?php } ?>
    */ ?>
    <?php echo linkAtestadoMatricula($model); ?>
    <?php echo linkAtestadoCurso($model); ?>
</ul>

<?php $this->renderPartial('/comuns/_informacoesDaInscricao', array('inscricao' => $model, 'ehAdmin' => false)); ?>
