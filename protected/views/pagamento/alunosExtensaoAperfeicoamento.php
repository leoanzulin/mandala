<?php
/* @var $this PagamentoController */

$this->breadcrumbs = array(
    'Pagamentos' => array('/pagamento'),
    'Controle de pagamentos de alunuos de extensão e aperfeiçoamento'
);
?>

<h1>Controle de pagamentos de alunos de extensão e aperfeiçoamento</h1>

<form id="pagamentos-form" action="<?php echo Yii::app()->request->requestUri; ?>" method="post">

    <?php
    $alunos = Inscricao::model()->findAll(array(
        'condition' => 'status = :status AND (tipo_curso = :tipo_curso1 OR tipo_curso = :tipo_curso2)',
        'params' => array(
            ':status' => Inscricao::STATUS_MATRICULADO,
            ':tipo_curso1' => Inscricao::TIPO_CURSO_EXTENSAO,
            ':tipo_curso2' => Inscricao::TIPO_CURSO_APERFEICOAMENTO,
        ),
    ));

    foreach ($alunos as $aluno) {

        echo "<b>{$aluno->nomeCompleto} (CPF {$aluno->cpf}) - Tipo: {$aluno->tipoDeCursoPorExtenso()}</b><br>";

        if ($aluno->tipo_curso == Inscricao::TIPO_CURSO_EXTENSAO) {

            $pagamentosDeExtensao = Pagamento::model()->findAllByAttributes(array(
                'inscricao_id' => $aluno->id,
                'tipo' => Pagamento::TIPO_PAGAMENTO_CURSO_EXTENSAO,
            ));

            echo "<ul>";
            foreach ($pagamentosDeExtensao as $pagamentoExtensao) {
                echo "<li>R\${$pagamentoExtensao->valor} pagos em {$pagamentoExtensao->data_pagamento}";
                echo "<input type=\"submit\" name=\"excluir[{$pagamentoExtensao->id}]\" value=\"Excluir\"></li>";
            }
            echo "</ul><br>";
            ?>

            R$ <input type="text" name="PagamentoExtensao[<?php echo $aluno->id; ?>][valor]" placeholder="Use ponto como separador decimal">
            Data <input type="text" name="PagamentoExtensao[<?php echo $aluno->id; ?>][data]" placeholder="dd/mm/aaaa">
            <input type="submit" value="Adicionar" name="adicionarExtensao[<?php echo $aluno->id ?>]">

            <?php
        } else if ($aluno->tipo_curso == Inscricao::TIPO_CURSO_APERFEICOAMENTO) {

            $pagamentosDeAperfeicoamento = Pagamento::model()->findAllByAttributes(array(
                'inscricao_id' => $aluno->id,
                'tipo' => Pagamento::TIPO_PAGAMENTO_CURSO_APERFEICOAMENTO,
            ));

            echo "<ul>";
            foreach ($pagamentosDeAperfeicoamento as $pagamentoAperfeicoamento) {
                echo "<li>R\${$pagamentoAperfeicoamento->valor} pagos em {$pagamentoAperfeicoamento->data_pagamento}";
                echo "<input type=\"submit\" name=\"excluir[{$pagamentoAperfeicoamento->id}]\" value=\"Excluir\"></li>";
            }
            echo "</ul>";
            ?>

            R$ <input type="text" name="PagamentoAperfeicoamento[<?php echo $aluno->id; ?>][valor]" placeholder="Use ponto como separador decimal">
            Data <input type="text" name="PagamentoAperfeicoamento[<?php echo $aluno->id; ?>][data]" placeholder="dd/mm/aaaa">
            <input type="submit" value="Adicionar" name="adicionarAperfeicoamento[<?php echo $aluno->id ?>]">

            <?php
        }
        echo "<br><br><hr>";
    }
    ?>

</form>
