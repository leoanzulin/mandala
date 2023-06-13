<?php
/* @var Inscricao $inscricao */
/* @var boolean $ehAdmin */
?>

<br>
<p><b>Status da inscrição:</b> <?php echo $inscricao->statusPorExtenso; ?></p>
<br>

<?php
$atributos = array(
    'cpf',
    'tipo_identidade',
    'identidade',
    'orgao_expedidor',
    'ra',
    'numero_ufscar',
    'nome',
    'sobrenome',
    'email',
    'data_nascimento',
    'naturalidade',
    'nome_mae',
    'nome_pai',
    'estado_civil',
    'telefone_fixo',
    'telefone_celular',
    'telefone_alternativo',
    'whatsapp',
    'skype',
    'cep',
    'endereco',
    'numero',
    'complemento',
    'cidade',
    'estado',
    'cargo_atual',
    'empresa',
    'telefone_comercial',
    'modalidade',
    'candidato_a_bolsa',
    array(
        'label' => $inscricao->getAttributeLabel('tipo_curso'),
        'value' => $inscricao->tipoDeCursoPorExtenso(),
    ),
);

if ($ehAdmin) {
    $atributos = array_merge($atributos, array(
        'recebe_bolsa',
        'observacoes',
    ));
}

$this->widget('zii.widgets.CDetailView', array(
    'data' => $inscricao,
    'attributes' => $atributos,
));
?>
<br>

<h2>Formação</h2>

<ul>
    <?php
    foreach ($inscricao->formacoes as $formacao) {
        echo "<li>{$formacao->nivelPorExtenso} - {$formacao->curso} - {$formacao->instituicao} ({$formacao->ano_conclusao})</li>\n";
    }
    ?>
</ul>
<br>

<h2>Habilitações escolhidas</h2>
<?php
$i = 1;
foreach ($inscricao->recuperarHabilitacoes() as $habilitacao) {
    echo "<h3>Habilitação {$i}: {$habilitacao->nome}</h3>\n";
    $i++;
}
?>

<h2>Documentos</h2>

<?php if (empty($inscricao->documento_cpf)) { ?>
    <p>Ainda não enviou os documentos</p>
<?php } else { ?>

    <ul>
        <li><a href="<?php echo $inscricao->recuperarArquivoDocumento('cpf', true); ?>">CPF</a></li>
        <li><a href="<?php echo $inscricao->recuperarArquivoDocumento('rg', true); ?>">RG</a></li>
        <li><a href="<?php echo $inscricao->recuperarArquivoDocumento('diploma', true); ?>">Diploma</a></li>
        <li><a href="<?php echo $inscricao->recuperarArquivoDocumento('comprovante_residencia', true); ?>">Comprovante de residência</a></li>
        <li><a href="<?php echo $inscricao->recuperarArquivoDocumento('curriculo', true); ?>">Currículo</a></li>
        <?php if ($inscricao->documento_justificativa) { ?>
            <li><a href="<?php echo $inscricao->recuperarArquivoDocumento('justificativa', true); ?>">Justificativa de próprio punho</a></li>
        <?php } ?>
    </ul>

<?php } ?>
