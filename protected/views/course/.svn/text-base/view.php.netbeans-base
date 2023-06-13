<?php
/* @var $this CourseController */
/* @var $model Course */
/* @var $dataProviderDisciplinas CActiveDataProvider */

$this->breadcrumbs = array(
    'Cursos' => array('index'),
    $model->course_name,
);

$this->menu = array(
    array('label' => 'Editar curso', 'url' => array('update', 'id' => $model->course_id)),
    array('label' => 'Remover curso', 'url' => '#',
        'linkOptions' => array('submit' => array(
            'delete',
            'id' => $model->course_id),
            'confirm' => 'Tem certeza que deseja remover este curso?'
        )
    ),
);
?>

<h1>Curso '<?php echo $model->course_name; ?>' (ID <?php echo $model->course_id; ?>)</h1>

[<?php echo CHtml::link('editar curso', array('update', 'id' => $model->course_id)) ?>]
[<?php echo CHtml::link('remover curso', array('delete', 'id' => $model->course_id), array('confirm' => 'Tem certeza que deseja remover este curso?')) ?>]

<?php
    if (count($model->disciplines) > 0) {
?>

<h2>Disciplinas do curso</h2>

<?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $dataProviderDisciplinas,
        'itemView' => '_viewDiscipline',
    ));

    } else {
?>

<h3>Nâo há disciplinas neste curso.</h3>

<?php } ?>

<br />

<?php
    echo CHtml::link('Criar nova disciplina', array(
        'discipline/createDisciplineForCourse', 'courseId' => $model->course_id
    ), array('class' => 'button'));
?>
