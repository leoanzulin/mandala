<?php
/* @var $this ComponenteCurricularController */
/* @var $model ComponenteCurricular */

$this->breadcrumbs=array(
	'Componente Curriculars'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ComponenteCurricular', 'url'=>array('index')),
	array('label'=>'Create ComponenteCurricular', 'url'=>array('create')),
	array('label'=>'View ComponenteCurricular', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ComponenteCurricular', 'url'=>array('admin')),
);
?>

<h1>Update ComponenteCurricular <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>