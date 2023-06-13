<?php
/* @var $this MatriculaController */
/* @var $model Matricula */

$this->breadcrumbs=array(
	'Matriculas'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Matricula', 'url'=>array('index')),
	array('label'=>'Create Matricula', 'url'=>array('create')),
	array('label'=>'View Matricula', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Matricula', 'url'=>array('admin')),
);
?>

<h1>Update Matricula <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>