<?php
$this->breadcrumbs = array(
    'Gerenciar notas' => array('/admin/gerenciarNotas'),
    $oferta->recuperarNome(),
);

$ehProjetoIntegrador = $oferta->ehProjetoIntegrador();

function campoMedia($form, $inscricaoOferta, $ehProjetoIntegrador)
{
    if ($ehProjetoIntegrador) {
        $name = "Notas[{$inscricaoOferta->inscricao_id}][media]";
        return '<input id="media_'.$inscricaoOferta->inscricao_id.'" onFocus="habilitar(this.id)" data-changed="false" size="10" maxlength="10" class="muito-curto" name="'.$name.'" type="text" value="'.$inscricaoOferta->media.'">';
    } else {
        return $inscricaoOferta->media;
    }
}

function campoFrequencia($form, $inscricaoOferta, $ehProjetoIntegrador)
{
    if ($ehProjetoIntegrador) {
        $name = "Notas[{$inscricaoOferta->inscricao_id}][frequencia]";
        return '<input id="frequencia_'.$inscricaoOferta->inscricao_id.'" onFocus="habilitar(this.id)" data-changed="false" size="10" maxlength="10" class="muito-curto" name="'.$name.'" type="text" value="'.$inscricaoOferta->frequencia.'">';
    } else {
        return $inscricaoOferta->frequencia;
    }
}

function campoStatus($inscricaoOferta)
{
    $name = "Notas[{$inscricaoOferta->inscricao_id}][status]";

    $statusSelecionado = $inscricaoOferta->status;
    // $statusSelecionado = $inscricaoOferta->ehAprovada()
    //     ? InscricaoOferta::STATUS_APROVADO
    //     : InscricaoOferta::STATUS_REPROVADO;

    $options = implode('', array_map(function($status) use($statusSelecionado) {
        $selecionado = ($status == $statusSelecionado) ? ' selected' : '';
        return "<option value=\"{$status}\"{$selecionado}>{$status}</option>";
    }, InscricaoOferta::STATUS_POSSIVEIS()));

    return "<select onFocus=\"habilitar(this.id)\" data-changed=\"false\" id=\"status_{$inscricaoOferta->inscricao_id}\" name=\"{$name}\">{$options}</select>";
}

?>

<h2>Gerenciar notas e frequências da oferta <?php echo $oferta->recuperarNome(); ?></h2>

<p class="atencao">Apenas o campo <b>status</b> pode ser modificado, as demais informações vêm diretamente do Moodle e devem ser alteradas nele.</p>
<p class="atencao">Apenas alunos com status <b>Ativo</b> aparecem nesta listagem.</p>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'gerenciar-notas-form',
    'enableAjaxValidation' => false,
));
?>

<table>
    <thead>
        <tr>
            <th>Aluno</th>
            <th>Nota virtual</th>
            <th>Nota presencial</th>
            <th>Média</th>
            <th>Frequência</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($inscricoesOferta as $inscricaoOferta) { ?>
            <tr>
                <td><?php echo $inscricaoOferta->inscricao->nomeCompleto; ?></td>
                <td><?php echo $inscricaoOferta->nota_virtual; ?></td>
                <td><?php echo $inscricaoOferta->nota_presencial; ?></td>
                <td><?php echo campoMedia($form, $inscricaoOferta, $ehProjetoIntegrador); ?></td>
                <td><?php echo campoFrequencia($form, $inscricaoOferta, $ehProjetoIntegrador); ?></td>
                <td><?php echo campoStatus($inscricaoOferta); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php
echo CHtml::submitButton('Salvar notas', array(
    'id' => 'salvar_notas',
    'class' => 'btn btn-success btn-lg',
    'onClick' => 'desabilitarCamposQueNaoMudaram()'
));
$this->endWidget();
?>

<script>
    function habilitar(id) {
        var partes = id.split('_');
        var inscricaoId = partes[1];
        document.getElementById('media_' + inscricaoId).setAttribute('data-changed', true);
        document.getElementById('frequencia_' + inscricaoId).setAttribute('data-changed', true);
        document.getElementById('status_' + inscricaoId).setAttribute('data-changed', true);
    };

    function desabilitarCamposQueNaoMudaram() {
        var campos = document.querySelectorAll("input[type=text]");
        for (i = 0; i < campos.length; i++) {
            desabilitarElementoSeNaoTiverMudado(campos[i]);
        }

        var campos = document.querySelectorAll("select");
        for (i = 0; i < campos.length; i++) {
            desabilitarElementoSeNaoTiverMudado(campos[i]);
        }
        return true;
    }

    function desabilitarElementoSeNaoTiverMudado(elemento) {
        if (elemento.getAttribute('data-changed') == false || elemento.getAttribute('data-changed') == 'false') {
            elemento.setAttribute('disabled', true);
        }
    }
</script>