<?php
/* @var $this DisciplineController */
/* @var $model Discipline */
/* @var $dataProviderOffers CActiveDataProvier */

$this->breadcrumbs = array(
    'Cursos' => array('course/index'),
    $model->course->course_name => array('course/view', 'id' => $model->course_id),
    $model->discipline_name,
);

$this->menu = array(
    array('label' => 'Editar disciplina', 'url' => array('update', 'id' => $model->discipline_id)),
    array('label' => 'Remover disciplina', 'url' => '#',
        'linkOptions' => array(
            'submit' => array('delete', 'id' => $model->discipline_id),
            'confirm' => 'Tem certeza que deseja remover esta disciplina?'
        )
    ),
);
?>

<h1>Disciplina '<?php echo $model->discipline_name; ?>' (ID <?php echo $model->discipline_id; ?>)</h1>

<?php
$this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'discipline_name',
        array(
            'label' => $model->getAttributeLabel('menu'),
            'name' => 'menu',
            'type' => 'ntext',
        ),
        'workload',
        array(
            'label' => $model->getAttributeLabel('objective'),
            'name' => 'objective',
            'type' => 'ntext',
        )
    ),
));
?>
[<?php echo CHtml::link('editar disciplina', array('update', 'id' => $model->discipline_id)) ?>]
[<?php echo CHtml::link('remover disciplina', array('delete', 'id' => $model->discipline_id), array('confirm' => 'Tem certeza que deseja remover esta disciplina?')) ?>]

<h2>Ofertas desta disciplina</h2>

<?php
$this->widget('zii.widgets.CListView', array(
    'dataProvider' => $dataProviderOffers,
    'itemView' => '_viewOffer',
));

echo CHtml::link('Criar nova oferta', array(
        'offer/create', 'disciplineId' => $model->discipline_id
    ), array('class' => 'button')
);

?>


