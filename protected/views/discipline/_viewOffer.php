<?php
/* @var $this DisciplineController */
/* @var $data Offer */
?>

<div class="view"><p>
    <b>Docente:</b> <?php echo CHtml::encode($data->teacher->user_firstname . ' ' . $data->teacher->user_lastname); ?><br />
    <b>Data de início:</b> <?php echo CHtml::encode($data->offer_start_date); ?><br />
    <b>Data de término:</b> <?php echo CHtml::encode($data->offer_end_date); ?><br />
    [<?php echo CHtml::link('editar oferta', array(
        'offer/update',
        'disciplineId' => $data->offer_discipline_id,
        'userId' => $data->offer_teacher_id,
        'startDate' => $data->offer_start_date,
        'endDate' => $data->offer_end_date,
    )); ?>]
    [<?php echo CHtml::link('remover oferta', array(
        'offer/delete',
        'disciplineId' => $data->offer_discipline_id,
        'userId' => $data->offer_teacher_id,
        'startDate' => $data->offer_start_date,
        'endDate' => $data->offer_end_date,
    ), array('confirm' => 'Tem certeza que deseja remover esta oferta?')) ?>]
</p></div>