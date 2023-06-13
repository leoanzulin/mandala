<?php

?>

<h1>Cadastrar novo encontro presencial</h1>

<?php $this->renderPartial('_formulario', array(
    'model' => $model,
    'colaboradores' => $colaboradores,
    'inscricoes' => $inscricoes,
)); ?>
