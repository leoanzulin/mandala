<?php
/* @var $this FormacaoController */
/* @var $model Formacao */

$this->breadcrumbs=array(
	'Formacaos'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Formacao', 'url'=>array('index')),
	array('label'=>'Create Formacao', 'url'=>array('create')),
	array('label'=>'Update Formacao', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Formacao', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Formacao', 'url'=>array('admin')),
);
?>

<h1>View Formacao #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'nivel',
		'curso',
		'ano_conclusao',
		'instituicao',
		'inscricao_id',
	),
)); ?>
