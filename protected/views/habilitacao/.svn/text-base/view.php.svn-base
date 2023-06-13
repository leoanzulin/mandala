<?php
/* @var $this HabilitacaoController */
/* @var $model Habilitacao */

$this->breadcrumbs=array(
	'Habilitacaos'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Habilitacao', 'url'=>array('index')),
	array('label'=>'Create Habilitacao', 'url'=>array('create')),
	array('label'=>'Update Habilitacao', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Habilitacao', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Habilitacao', 'url'=>array('admin')),
);
?>

<h1>View Habilitacao #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'nome',
		'curso_id',
	),
)); ?>
