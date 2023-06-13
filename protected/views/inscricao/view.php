<?php
/* @var $this InscricaoController */
/* @var $model Inscricao */
/* @var $formacoes Formacao[] */
/* @var $habilitacoes Habilitacao[] */

function recuperarExtensao($nomeArquivo)
{
    $partes = pathinfo($nomeArquivo);
    return $partes['extension'];    
}
?>

<h1>Visualizando inscrição de <?php echo $model->nome . ' ' . $model->sobrenome; ?></h1>

<br>
<p><b>Status da inscrição:</b> <?php
    if ($model->status == Inscricao::STATUS_PENDENTE) echo "Pré-inscrição realizada";
    else if ($model->status == Inscricao::STATUS_DOCUMENTOS_SENDO_ANALISADOS) echo "Documentos sendo analisados";
    else if ($model->status == Inscricao::STATUS_DOCUMENTOS_VERIFICADOS) echo "Documentos validados";
?></p>
<br>

<?php $this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
        'id',
        'cpf',
        'ra',
        'numero_ufscar',
        'nome',
        'sobrenome',
        'email',
        'data_nascimento',
        'nome_mae',
        'nome_pai',
        'estado_civil',
        'telefone_fixo',
        'telefone_celular',
        'telefone_alternativo',
        'cep',
        'endereco',
        'numero',
        'complemento',
        'cidade',
        'estado',
        'cargo_atual',
        'empresa',
        'telefone_comercial',
    ),
)); ?>
<br>

<h2>Formação</h2>

<ul>
<?php
foreach ($formacoes as $formacao) {
    $tabelaNivel = array('graduacao' => 'Graduação', 'especializacao' => 'Especialização', 'mestrado' => 'Mestrado', 'doutorado' => 'Doutorado');
    echo "<li>{$tabelaNivel[$formacao->nivel]} - {$formacao->curso} - {$formacao->instituicao} ({$formacao->ano_conclusao})</li>\n";
}
?>
</ul>
<br>

<h2>Habilitações escolhidas</h2>

<ul>
<?php
foreach ($habilitacoes as $habilitacao) {
    echo "<li>$habilitacao->nome</li>\n";
}
?>
</ul>

<h2>Documentos</h2>

<?php if (empty($model->documento_cpf)) { ?>
<p>Ainda não enviou os documentos</p>
<?php } else { ?>

<ul>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('cpf', true); ?>">CPF</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('rg', true); ?>">RG</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('diploma', true); ?>">Diploma</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('comprovante_residencia', true); ?>">Comprovante de residência</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('curriculo', true); ?>">Currículo</a></li>
    <li><a href="<?php echo $model->recuperarArquivoDocumento('justificativa', true); ?>">Justificativa de próprio punho</a></li>
</ul>

<?php } ?>