<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs = array(
    'Usuários' => array('index'),
    $model->user_id => array('view', 'id' => $model->user_id),
    'Atualizar',
);
?>

<h1>Atualizar usuário '<?php echo $model->user_id; ?>'</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>