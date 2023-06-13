<?php
/* @var $this CourseController */
/* @var $model Course */

$this->breadcrumbs = array(
    'Cursos' => array('index'),
    $model->course_name => array('view', 'id' => $model->course_id),
    'Editar',
);
?>

<h1>Editar curso '<?php echo $model->course_name; ?>'</h1>

<?php echo $this->renderPartial('_form', array('model' => $model)); ?>