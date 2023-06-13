<?php
/* @var $this InscricaoController */
/* @var $model Inscricao */

$this->breadcrumbs=array(
	'Inscricaos'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Inscricao', 'url'=>array('index')),
	array('label'=>'Manage Inscricao', 'url'=>array('admin')),
);
?>

<h1>Create Inscricao</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>