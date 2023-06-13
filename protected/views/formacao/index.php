<?php
/* @var $this FormacaoController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Formacaos',
);

$this->menu=array(
	array('label'=>'Create Formacao', 'url'=>array('create')),
	array('label'=>'Manage Formacao', 'url'=>array('admin')),
);
?>

<h1>Formacaos</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
