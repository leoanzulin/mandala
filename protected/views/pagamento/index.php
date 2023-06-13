<?php
/* @var $this PagamentoController */

$this->breadcrumbs = array(
    'Pagamentos',
);

?>

<h1>Pagamentos</h1>

<ul>
    <li><?php echo CHtml::link('Gerenciamento de pagamento de bolsas para docnetes e tutores', array('bolsas')); ?></li>
    <li><?php echo CHtml::link('Consulta ao saldo de bolsas', array('saldoBolsas')); ?></li>
    <li><?php echo CHtml::link('Controle de pagamentos de mensalidades dos alunos', array('alunos')); ?></li>
</ul>
