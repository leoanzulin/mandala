<?php
/* @var $this InscricaoController */
/* @var $model Inscricao */

$this->breadcrumbs=array(
	'Inscricaos'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Inscricao', 'url'=>array('index')),
	array('label'=>'Create Inscricao', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#inscricao-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Inscricaos</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'inscricao-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'cpf',
		'nome',
		'sobrenome',
		'email',
		'data_nascimento',
		/*
		'nome_mae',
		'nome_pai',
		'estado_civil',
		'telefone_fixo',
		'telefone_celular',
		'telefone_alternativo',
		'cep',
		'endereco',
		'numero',
		'complemento',
		'cidade',
		'estado',
		'cargo_atual',
		'empresa',
		'telefone_comercial',
		'forma_pagamento',
		'candidato_bolsa',
		'status',
		'curso_id',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
