<h1>Gerenciar habilitações</h1>

<?php echo CHtml::link('Cadastrar nova habililtação', array('cadastrar')); ?>
<br><br>

<ul>
<?php foreach ($habilitacoes as $habilitacao) {
    echo "<li>" . CHtml::link($habilitacao['nome'], array('editar', 'id' => $habilitacao['id'])) . "</li>";
} ?>
</ul>
