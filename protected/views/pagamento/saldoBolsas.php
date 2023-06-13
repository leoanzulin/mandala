<?php
/* @var $this PagamentoController */
/* @var $docentes Docente[] */
/* @var $tutores Tutor[] */

$this->breadcrumbs = array(
    'Pagamentos' => array('/pagamento'),
    'Saldo de pagamentos de bolsas',
);
?>

<h1>Saldo do pagamentos de bolsas para docentes e tutores</h1>

<div ng-app="saldoBolsasApp" ng-controller="controlador">

    <br>
    <p class="note">No campo de saldo da FAI, use ponto ao invés de vírgula como separador decimal</p>
    <br>

    <p>Período:</p>
    <span>De
        <select style="width: 50px" ng-options="mes.id for mes in meses track by mes.id" ng-model="periodo_inicio_mes"></select>
        /
        <select style="width: 70px" ng-options="ano.id for ano in anos track by ano.id" ng-model="periodo_inicio_ano"></select>
        até
        <select style="width: 50px" ng-options="mes.id for mes in meses track by mes.id" ng-model="periodo_fim_mes"></select>
        /
        <select style="width: 70px" ng-options="ano.id for ano in anos track by ano.id" ng-model="periodo_fim_ano"></select>
    </span>

    <p>Mostrar bolsas pagas para
        <select style="width: 170px" ng-options="filtro.name for filtro in bolsasPagasPara track by filtro.id" ng-model="filtro"></select>
    </p>

    <p>Ordenar por
        <select style="width: 120px" ng-options="ordenacao.name for ordenacao in ordenacoes track by ordenacao.id" ng-model="ordem"></select>
    </p>
    
    <table class="saldo-bolsas">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Docente</th>
                <th>Tutor</th>
                <th>Data</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="bolsa in bolsas |
                        filter: filtrarPeriodo |
                        filter: filtrarQuem |
                        orderBy: [ordem.id, 'data_pagamento_ano_na_frente']">
                <td>{{bolsa.descricao}}</td>
                <td>{{bolsa.docente}}</td>
                <td>{{bolsa.tutor}}</td>
                <td>{{bolsa.data_pagamento}}</td>
                <td>{{bolsa.valor | currency: 'R$'}}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr><td colspan="4">Total</td><td>{{bolsas | filter: filtrarPeriodo | filtroSoma | currency: 'R$'}}</td></tr>
            <tr style="background: #FFF"><td colspan="4">Saldo na conta da FAI</td><td>
            <input type="number" placeholder="0.00" ng-pattern="/^[0-9]+((\.|,)[0-9]{1,2})?$/" step="100" ng-model="saldoFai" style="width: 80px"></td></tr>
            <tr><td colspan="4">Saldo remanescente</td><td>{{bolsas | filter: filtrarPeriodo | filtroSoma | filtroTirarSaldoFai:this | currency: 'R$'}}</td></tr>
        </tfoot>
    </table>

</div>