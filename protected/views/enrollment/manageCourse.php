<?php
/* @var $this EnrollmentController */
/* @var $dataProvider CActiveDataProvider */
/* @var $model Course */

$this->breadcrumbs = array(
    'Gerenciar inscrições em cursos' => array('enrollment/manage'),
    $model->course_name
);

function gerarLink($data)
{
    return CHtml::link(
            CHtml::encode($data->enr_firstname . ' ' . $data->enr_lastname),
            array('view',
                'courseId' => $data->course_id,
                'enrollmentId' => $data->enrollment_id)
    );
}

function gerarLinkAlterarInscricao($courseId, $enrollmentId, $estado)
{
    return Yii::app()->createUrl('enrollment/approveRefuseEnrollment', array(
        'state' => $estado,
        'enrollmentId' => $enrollmentId,
        'courseId' => $courseId
    ));
}

function gerarStatus($data)
{
    switch($data->enr_status) {
        case Enrollment::STATUS_NORMAL:
            return '';
        case Enrollment::STATUS_APPROVED:
            return 'Aprovada';
        case Enrollment::STATUS_REFUSED:
            return 'Recusada';
    }
}

function rowClass($row, $data)
{
    $class = $row % 2 == 0 ? 'even' : 'odd';
    
    if ($data->enr_status == Enrollment::STATUS_REFUSED) {
        $class = 'refused-' . $class;
    }
    return $class;
}
?>
<h1>Gerenciar inscrições do curso '<?php echo $model->course_name; ?>'</h1>

<ul>
    <li>Número de inscrições aprovadas: <?php echo $model->numberOfAcceptedEnrollments; ?></li>
</ul>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'dataProvider' => $dataProvider,
    'columns' => array(
        array(
            'name' => 'nome',
            'value' => 'gerarLink($data)',
            'type' => 'raw',
        ),
        'enr_formation',
        'enr_formation_area',
        array(
            'name' => 'Inscrição',
            'value' => 'gerarStatus($data)',
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{validar} {recusar}',
            'buttons' => array(
                'validar' => array(
                    'label' => 'Aprovar inscrição',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/aprovar.png',
                    'url' => 'gerarLinkAlterarInscricao($data->course_id, $data->enrollment_id, 1)',
//                    'click' => 'function(){validar(jQuery(this), true); return false;}',
                ),
                'recusar' => array(
                    'label' => 'Recusar inscrição',
                    'imageUrl' => Yii::app()->request->baseUrl . '/images/recusar.png',
                    'url' => 'gerarLinkAlterarInscricao($data->course_id, $data->enrollment_id, 0)',
//                    'click' => 'function(){validar(jQuery(this), false); return false;}',
                )
            ),
//            'visible' => $model->consolidado == true ? false : true,
        ),
    ),
    'rowCssClassExpression' => 'rowClass($row, $data)',
//    'rowHtmlOptionsExpression' => 'array("id" => "registro" . $data->id)',
));

?>
