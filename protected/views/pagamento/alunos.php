<?php
/* @var $this PagamentoController */

$this->breadcrumbs = array(
    'Pagamentos' => array('/pagamento'),
    'Controle de pagamentos de alunuos'
);

?>

<h1>Controle de pagamentos de alunos</h1>

<form id="pagamentos-form" ng-app="pagamentosApp" ng-controller="controlador" action="<?php echo Yii::app()->request->requestUri; ?>" method="post">

<p>Mostrar alunos com status </p>
<select name="filtroStatus" ng-options="filtro for filtro in filtrosStatus" ng-model="filtroSelecionado" ng-change="voltarParaPrimeiraPagina()"></select>

    <table class="pagamentos">
        <thead>
            <tr>
                <td colspan="36">
                    Mostrando {{paginacao.mostrandoInicio}}-{{paginacao.mostrandoFim}} de {{paginacao.numeroTotalDeAlunos}}
                    <button type="button" ng-disabled="(paginacao.paginaAtual + 1) == 1" ng-click="paginacao.paginaAtual = 0; paginacao.selecaoDePagina = paginacao.paginaAtual + 1">&lt;&lt;</button>
                    <button type="button" ng-disabled="(paginacao.paginaAtual + 1) == 1" ng-click="paginacao.paginaAtual = paginacao.paginaAtual - 1; paginacao.selecaoDePagina = paginacao.paginaAtual + 1">&lt;</button>
                    <input type="text" ng-model="paginacao.selecaoDePagina" ng-blur="atualizarPagina()" style="width: 30px;"> / {{paginacao.paginaMaxima}}
                    <button type="button" ng-disabled="(paginacao.paginaAtual + 1) >= paginacao.paginaMaxima" ng-click="paginacao.paginaAtual = paginacao.paginaAtual + 1; paginacao.selecaoDePagina = paginacao.paginaAtual + 1">&gt;</button>
                    <button type="button" ng-disabled="(paginacao.paginaAtual + 1) >= paginacao.paginaMaxima" ng-click="paginacao.paginaAtual = paginacao.paginaMaxima - 1; paginacao.selecaoDePagina = paginacao.paginaAtual + 1">&gt;&gt;</button>
                </td>
            </tr>
            <tr>
                <th rowspan="2">Nome</th>
                <th rowspan="2">Status</th>
                <th rowspan="2">Bolsa</th>
                <th rowspan="2">Inscrição</th>
                <th rowspan="2">Matrícula</th>
                <th colspan="27">Parcelas</th>
                <th rowspan="2">Pagou à vista</th>
                <th rowspan="2">Total previsto</th>
                <th rowspan="2">Total pago</th>
                <th rowspan="2">Total faltante (em relação ao previsto)</th>
            </tr>
            <tr><th ng-repeat="n in [] | range: 27">{{n + 1}}</th></tr>
        </thead>
        <tbody>
            <tr ng-repeat="aluno in alunos |
                        filtrarStatusAluno: filtroSelecionado:this |
                        orderBy: ['nome', 'sobrenome'] |
                        pagination: paginacao.paginaAtual * paginacao.tamanhoDaPagina |
                        limitTo: paginacao.tamanhoDaPagina">
                <td>{{aluno.nome}} {{aluno.sobrenome}}</td>
                <td>{{aluno.status_aluno}}</td>
                <td>{{aluno.observacoes_bolsa}}</td>
                <td ng-class="{'item-de-pagamento-selecionado': aluno.pagamento.inscricao.valor != '0,00'}">
                    <item-de-pagamento pagamento="aluno.pagamento.inscricao"></item-de-pagamento>
                </td>
                <td ng-class="{'item-de-pagamento-selecionado': aluno.pagamento.matricula.valor != '0,00'}">
                    <item-de-pagamento pagamento="aluno.pagamento.matricula"></item-de-pagamento>
                </td>
                <td ng-repeat="n in [] | range: 27" ng-class="{'item-de-pagamento-selecionado': aluno.pagamento.parcelas[(n + 1)].valor != '0,00'}">
                    <item-de-pagamento pagamento="aluno.pagamento.parcelas[(n + 1)]"></item-de-pagamento>
                </td>
                <td ng-class="{'item-de-pagamento-selecionado': aluno.pagamento.pagouAVista.valor != '0,00'}">
                    <item-de-pagamento pagamento="aluno.pagamento.pagouAVista"></item-de-pagamento>
                </td>
                <td ng-class="{'item-de-pagamento-selecionado': aluno.pagamento.totalPrevisto.valor != '0,00'}">
                    <item-de-pagamento pagamento="aluno.pagamento.totalPrevisto"></item-de-pagamento>
                </td>
                <td>R$ {{totalPago(aluno)}}</td>
                <td>R$ {{totalFaltante(aluno)}}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="36">
                    Mostrando {{paginacao.mostrandoInicio}}-{{paginacao.mostrandoFim}} de {{paginacao.numeroTotalDeAlunos}}
                    <button type="button" ng-disabled="(paginacao.paginaAtual + 1) == 1" ng-click="paginacao.paginaAtual = 0; paginacao.selecaoDePagina = paginacao.paginaAtual + 1">&lt;&lt;</button>
                    <button type="button" ng-disabled="(paginacao.paginaAtual + 1) == 1" ng-click="paginacao.paginaAtual = paginacao.paginaAtual - 1; paginacao.selecaoDePagina = paginacao.paginaAtual + 1">&lt;</button>
                    <input type="text" ng-model="paginacao.selecaoDePagina" ng-blur="atualizarPagina()" style="width: 30px;"> / {{paginacao.paginaMaxima}}
                    <button type="button" ng-disabled="(paginacao.paginaAtual + 1) >= paginacao.paginaMaxima" ng-click="paginacao.paginaAtual = paginacao.paginaAtual + 1; paginacao.selecaoDePagina = paginacao.paginaAtual + 1">&gt;</button>
                    <button type="button" ng-disabled="(paginacao.paginaAtual + 1) >= paginacao.paginaMaxima" ng-click="paginacao.paginaAtual = paginacao.paginaMaxima - 1; paginacao.selecaoDePagina = paginacao.paginaAtual + 1">&gt;&gt;</button>
                </td>
            </tr>
        </tfoot>
    </table>

    <div ng-repeat="aluno in alunos">
        <div ng-repeat="tipo in ['inscricao', 'matricula', 'pagouAVista', 'totalPrevisto']">
            <input id="item_{{aluno.id}}_{{tipo}}_valor" type="hidden" name="item[{{aluno.id}}][{{tipo}}][valor]" value="{{aluno.pagamento[tipo].valor}}">
            <input id="item_{{aluno.id}}_{{tipo}}_data" type="hidden" name="item[{{aluno.id}}][{{tipo}}][data_pagamento]" value="{{aluno.pagamento[tipo].data_pagamento}}">
        </div>
        <div ng-repeat="n in [] | range: 27">
            <input id="item_{{aluno.id}}_parcela{{n}}_valor" type="hidden" name="item[{{aluno.id}}][parcelas][{{n + 1}}][valor]" value="{{aluno.pagamento.parcelas[n + 1].valor}}">
            <input id="item_{{aluno.id}}_parcela{{n}}_data" type="hidden" name="item[{{aluno.id}}][parcelas][{{n + 1}}][data_pagamento]" value="{{aluno.pagamento.parcelas[n + 1].data_pagamento}}">
        </div>
    </div>

    <?php
    echo CHtml::submitButton('Salvar', array(
        'class' => 'btn btn-success btn-lg',
        'name' => 'Salvar',
    ));
    ?>

</form>
