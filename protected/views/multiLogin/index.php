<h2>Escolha qual inscrição deseja acessar</h2>
<br>

<p>Vimos que você tem várias inscrições no EduTec. Por favor, escolha qual deseja acessar:</p>

<ul>
<?php
foreach ($inscricoes as $inscricao) {
    echo '<li>' . CHtml::link($inscricao->gerarStringDescritiva(), ['acessar', 'id' => $inscricao->id]) . '</li>'; 
}
?>
</ul>
