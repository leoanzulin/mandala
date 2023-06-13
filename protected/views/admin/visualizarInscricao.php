<?php
/* @var $this AdminController */
/* @var $model Inscricao */

$this->breadcrumbs = array(
    'Gerenciar inscrições' => array('gerenciarInscricoes'),
    $model->cpf,
);
?>

<h1>Inscrição de <?php echo $model->nomeCompleto; ?></h1>

<?php $this->renderPartial('/comuns/_informacoesDaInscricao', array('inscricao' => $model, 'ehAdmin' => true)); ?>
