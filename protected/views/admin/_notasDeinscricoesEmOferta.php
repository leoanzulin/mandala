<?php
/* 
 * @var $oferta Oferta 
 * @var $inscricaoOfertas InscricaoOferta[] Inscrições que foram feitas nesta oferta
 * 
 * Chamado em gerenciarNotas
 */
?>

<div>
    <p><b><?php echo $oferta->componenteCurricular->nome; ?></b></p><br>
    <table>
        <!-- TODO: MEXER NO CSS porque adicionou a coluna 'parcial' aqui -->
        <thead><tr><th>CPF</th><th>Nome</th><th>Parcial</th><th>Média</th><th>Status</th></tr></thead>
        <tbody>
            <?php foreach ($inscricoesOfertas as $inscricaoOferta) { ?>
            <tr>
                <td><?php echo $inscricaoOferta->inscricao->cpf; ?></td>
                <td><?php echo $inscricaoOferta->inscricao->nome; ?></td>
                <td><input type="text"
                            id="parciais_<?php echo $inscricaoOferta->oferta->id ?>_<?php echo $inscricaoOferta->inscricao->id ?>"
                            name="parciais[<?php echo $inscricaoOferta->oferta->id ?>][<?php echo $inscricaoOferta->inscricao->id ?>]"
                            value="<?php echo $inscricaoOferta->recuperarParcialParaView(); ?>"
                            maxlength="6">
                </td>
                <td><input type="text"
                            id="notas_<?php echo $inscricaoOferta->oferta->id ?>_<?php echo $inscricaoOferta->inscricao->id ?>"
                            name="notas[<?php echo $inscricaoOferta->oferta->id ?>][<?php echo $inscricaoOferta->inscricao->id ?>]"
                            value="<?php echo $inscricaoOferta->recuperarMediaParaView(); ?>"
                            maxlength="4">
                </td>
                <td></td>
            <?php } ?>
        </tbody>
    </table>
</div>
