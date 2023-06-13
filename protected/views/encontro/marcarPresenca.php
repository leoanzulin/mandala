<?php
$this->breadcrumbs = array(
    'Encontros presenciais' => array('/encontro/gerenciar'),
    "{$model->local} {$model->data}" => array('/encontro/editar', 'id' => $model->id),
    'Marcar presença',
);
?>

<h1>Marcar presença de alunos</h1>

<?php echo "<p>Encontro {$model->tipo} em {$model->local} no dia {$model->data}</p>"; ?>

<div class="form">

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'docente-form',
    'enableAjaxValidation' => false,
));
?>

    <table>
        <thead><tr><th>Aluno</th><th>Presente?</th></tr></thead>
        <tbody>
<?php foreach ($model->alunos as $aluno) { ?>
            <tr>
                <td><?php echo "<label for=\"presenca_{$aluno->id}\" style=\"text-align: left; width: 100%\">{$aluno->nomeCompleto}</label>"; ?></td>
                <td><input id="presenca_<?php echo $aluno->id; ?>" type="checkbox" name="Presenca[<?php echo $aluno->id; ?>]"
                <?php if (in_array($aluno->id, $idsAlunosPresentes)) { echo 'checked'; } ?>
                ></td>
            </tr>
<?php } ?>
        </tbody>
    </table>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Salvar'); ?>
    </div>

<?php $this->endWidget(); ?>

</div>
