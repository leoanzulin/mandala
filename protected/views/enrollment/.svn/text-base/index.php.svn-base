<?php
/* @var $this EnrollmentController */
/* @var $dataProviderCursos CActiveDataProvider */

$this->breadcrumbs=array(
	'Fazer inscrição em cursos',
);
?>
<h1>Fazer inscrição em cursos</h1>

<p>Os seguintes cursos estão com inscrições abertas:</p>

<ul>
<?php
    $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $dataProviderCursos,
        'itemView' => '_viewCurso',
    ));
?>
</ul>