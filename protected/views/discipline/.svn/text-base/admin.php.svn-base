<?php
/* @var $this DisciplineController */
/* @var $model Discipline */

$this->breadcrumbs = array(
    'Disciplinas' => array('index'),
    'Gerenciar',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#discipline-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Gerenciar disciplinas</h1>

<!--<p>
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>-->

    <?php echo CHtml::link('Busca avançada', '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
    <?php
    $this->renderPartial('_search', array(
        'model' => $model,
    ));
    ?>
</div><!-- search-form -->

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'discipline-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'discipline_id',
        'course_id',
        'discipline_name',
        'menu',
        'workload',
        'objective',
        array(
            'class' => 'CButtonColumn',
        ),
    ),
));
?>
