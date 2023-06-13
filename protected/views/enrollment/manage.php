<?php
/* @var $this EnrollmentController */
/* @var $dataProviderCursos CActiveDataProvider */

$this->breadcrumbs=array(
	'Gerenciar inscrições em cursos',
);

function gerarLink($data)
{
    return CHtml::link($data->course_name, array('manageCourse', 'id' => $data->course_id));
}
?>
<h1>Gerenciar inscrições em cursos</h1>

<ul>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider' => $dataProviderCursos,
    'columns' => array(
        array(
            'header' => 'Curso',
            'name' => 'course_name',
            'value' => 'gerarLink($data)',
            'type' => 'raw',
        ),
        array(
            'header' => 'Número de inscritos',
            'value' => '$data->numberOfEnrollments',
        ),
    ),
));
?>
</ul>