<?php
/* @var $this AlunoController */
/* @var $ofertasInscritasDesteMes */
/* @var $ano */
/* @var $mes */
/* @var $bloqueado */

$this->breadcrumbs = array(
    'Confirmar inscrições',
);
?>

<h1>Confirmar inscrições nas ofertas de <?php echo "{$mes}/{$ano}"; ?></h1>
<br>

<?php if ($bloqueado) { ?>
<p style="font-size: 1.3em">Estamos fora do período de confirmação e por isso as confirmações estão bloqueadas</p>
<?php } else { ?>
<p style="color: red; font-weight: bold; font-size: 1.3em">Atenção: Você só será inscrito nas ofertas em que confirmar sua inscrição</p>
<?php } ?>
<br>

<?php if (!empty($ofertasInscritasDesteMes)) { ?>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'confirmacao-inscricoes-form',
    'enableAjaxValidation' => false,
));
?>

<table class="table tabela-inscricao">
    <tbody>
    <tr><th>Componente curricular</th><th></th></tr>
        <?php foreach ($ofertasInscritasDesteMes as $oferta) { ?>
        <tr data-inscricao>
            <td>
                <b><?php echo $oferta['nome']; ?></b>
                <br>
                Docentes: <?php echo $oferta['nomesDocentes']; ?>
            </td>
            <td>
                <input
                    type="checkbox"
                    name="ofertas[]"
                    value="<?php echo $oferta['id']; ?>"
                    <?php echo $bloqueado ? 'disabled' : null; ?>
                    <?php echo $oferta['confirmada'] ? 'checked' : null; ?>
                >
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php
echo CHtml::submitButton('Confirmar inscrições', array(
    'class' => 'btn btn-success btn-lg',
    'name' => 'Salvar',
    'onClick' => 'js:return validar();',
));

$this->endWidget();

} else {
?>
<h2>Não há inscrições em ofertas para confirmar neste mês</h2>
<?php } ?>
