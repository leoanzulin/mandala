<?php
/* @var $this ColaboradorController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs = [ 'Meu perfil' ];
?>

<h1>Meu perfil</h1>

<ul>
    <li><?php echo CHtml::link('Editar perfil', ['colaborador/editarPerfil']); ?></li>
    <li><?php echo CHtml::link('Trocar senha', ['colaborador/trocarSenha']); ?></li>
</ul>

<?php
$atributos = [
    'cpf',
    'nome',
    'sobrenome',
    'email',
    'telefone',
    'endereco',
    'numero',
    'bairro',
    'complemento',
    'cep',
    'mestrando_ou_doutorando_ufscar',
    'titulo',
];

$this->widget('zii.widgets.CDetailView', [
    'data' => $colaborador,
    'attributes' => $atributos,
]);
?>
