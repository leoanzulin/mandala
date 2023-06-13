<?php
/* @var $this MatriculaController */
/* @var $model Matricula */

$this->breadcrumbs=array(
	'Matriculas'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Matricula', 'url'=>array('index')),
	array('label'=>'Manage Matricula', 'url'=>array('admin')),
);
?>

<h1>Create Matricula</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>