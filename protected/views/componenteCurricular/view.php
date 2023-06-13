<?php
/* @var $this ComponenteCurricularController */
/* @var $model ComponenteCurricular */

$this->breadcrumbs=array(
	'Componente Curriculars'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ComponenteCurricular', 'url'=>array('index')),
	array('label'=>'Create ComponenteCurricular', 'url'=>array('create')),
	array('label'=>'Update ComponenteCurricular', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ComponenteCurricular', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ComponenteCurricular', 'url'=>array('admin')),
);
?>

<h1>View ComponenteCurricular #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'nome',
		'carga_horaria',
	),
)); ?>
