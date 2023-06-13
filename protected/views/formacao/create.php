<?php
/* @var $this FormacaoController */
/* @var $model Formacao */

$this->breadcrumbs=array(
	'Formacaos'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Formacao', 'url'=>array('index')),
	array('label'=>'Manage Formacao', 'url'=>array('admin')),
);
?>

<h1>Create Formacao</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>