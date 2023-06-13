<?php
/* @var $this CourseController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = array(
    'Cursos',
);

$this->menu = array(
    array('label' => 'Criar novo curso', 'url' => array('create')),
    array('label' => 'Gerenciar cursos', 'url' => array('admin')),
);
?>

<h1>Cursos</h1>

<?php
$this->widget('zii.widgets.CListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => '_view',
));
?>

<?php echo CHtml::link('Criar novo curso', array('create'), array('class' => 'button')); ?>
