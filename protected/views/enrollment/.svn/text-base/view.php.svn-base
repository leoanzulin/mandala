<?php
/* @var $this EnrollmentController */
/* @var $model Enrollment */

$this->breadcrumbs = array(
    'Gerenciar inscrições em cursos' => array('enrollment/manage'),
    $model->course->course_name => array('enrollment/manageCourse', 'id' => $model->course_id),
    'Inscrição de ' . $model->enr_firstname . ' ' . $model->enr_lastname,
);
?>

<h1>Visualizando inscrição de <?php echo $model->fullName; ?></h1>

<div class="form">

<?php $this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'enr_document',
        'enr_firstname',
        'enr_lastname',
        'enr_email',
        'enr_phone',
        'enr_mobile',
        'enr_zipcode',
        'enr_address',
        'enr_complement',
        'enr_city',
        'enr_state',
        'enr_formation',
        'enr_formation_area',
        array(
            'name' => $model->getAttributeLabel('enr_public_server'),
            'value' => $model->enr_public_server ? 'Sim' : 'Não'
        ),
        'enr_siape',
    ),
)); ?>

</div>