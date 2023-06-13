<?php
/* @var $this DisciplineController */
/* @var $model Discipline */

$this->breadcrumbs = array(
    'Cursos' => array('course/index'),
    $model->course->course_name => array('course/view', 'id' => $model->course_id),
    $model->discipline_name => array('discipline/view', 'id' => $model->discipline_id),
    'Editar',
);
?>

<h1>Editar disciplina '<?php echo $model->discipline_name; ?>' (ID <?php echo $model->discipline_id; ?>)</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
