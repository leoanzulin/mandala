<?php
/* @var $this UsersController */
/* @var $model Users */

$this->breadcrumbs = array(
    'Usuários' => array('index'),
    $model->user_id,
);

$this->menu = array(
    array('label' => 'Editar usuário', 'url' => array('update', 'id' => $model->user_id)),
    array('label' => 'Remover usuário', 'url' => '#', 'linkOptions' => array('submit' => array('delete', 'id' => $model->user_id), 'confirm' => 'Tem certeza que deseja remover este usuário?')),
);

function canEditUser($model)
{
    return !in_array('Admin', explode(', ', $model->roles));
}
?>

<h1>Visualizar usuário</h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'user_id',
        'user_firstname',
        'user_lastname',
        'user_email',
        'roles',
    ),
));
?>

<?php if (canEditUser($model)) { ?>

<?php echo CHtml::link('editar usuário', array('update', 'id' => $model->user_id),
            array('class' => 'button')); ?>

<?php echo CHtml::link('remover usuário', array('delete', 'id' => $model->user_id),
        array('confirm' => 'Tem certeza que deseja remover esta usuário?',
            'class' => 'button')); ?>

<?php } ?>