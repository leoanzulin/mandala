<?php
/* @var $this DisciplineController */
/* @var $data Discipline */
?>

<div class="view"><p>
    <b>Disciplina:</b>
    <?php
        echo CHtml::link(
            CHtml::encode($data->discipline_name), array('view', 'id' => $data->discipline_id)
        );
    ?><br />
    <b>Curso:</b> <?php echo CHtml::encode($data->course->course_name); ?><br />
    <b>Ementa:</b> <?php echo CHtml::encode($data->menu); ?><br />
    <b>Carga horária:</b> <?php echo CHtml::encode($data->workload); ?>
</p></div>