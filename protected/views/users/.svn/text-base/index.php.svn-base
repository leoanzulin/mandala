<?php
/* @var $this UsersController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
    'Users',
);

$this->menu = array(
    array('label' => 'Adicionar novo usuário', 'url' => array('create')),
    array('label' => 'Gerenciar usuários', 'url' => array('admin')),
);
?>

<h1>Usuários</h1>

<?php
$this->widget('zii.widgets.CListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => '_view',
));

echo CHtml::link('Adicionar novo usuário',
        array('users/create'),
        array('class' => 'button'));
?>