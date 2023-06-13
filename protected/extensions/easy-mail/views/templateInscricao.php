<p>Caro(a) <?php echo $params['content']['nome']; ?>,</p><br>

<p>Seu formulário de inscrição no curso de Especialização em Educação e Tecnologias foi recebido com sucesso.</p>

<p>Você se inscreveu na(s) seguinte(s) habilitação(ões):</p>

<ul>
	<li><b><?php echo $params['content']['habilitacoes'][0]; ?></b></li>
	<?php if (count($params['content']['habilitacoes']) > 1) { ?>
	<li><b><?php echo $params['content']['habilitacoes'][1]; ?></b></li>
	<?php } ?>
</ul>

<p>A forma de pagamento escolhida foi:</p>

<p><b><?php echo $params['content']['formaPagamento']; ?></b></p>

<p>Futuramente você receberá mais informações sobre sua inscrição.</p>

<p>No caso de dúvidas, favor entrar em contato com a secretaria do curso pelo e-mail:</p>

<p>edutec@ead.ufscar.br</p>

<p>Obrigado!</p><br>

<p>Grupo Horizonte</p>

<p>Secretaria do Curso Educação e Tecnologias</p>
