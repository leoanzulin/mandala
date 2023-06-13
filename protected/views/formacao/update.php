<?php
/* @var $this FormacaoController */
/* @var $model Formacao */

$this->breadcrumbs=array(
	'Formacaos'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Formacao', 'url'=>array('index')),
	array('label'=>'Create Formacao', 'url'=>array('create')),
	array('label'=>'View Formacao', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Formacao', 'url'=>array('admin')),
);
?>

<h1>Update Formacao <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>