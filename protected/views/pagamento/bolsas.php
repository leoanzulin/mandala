<?php
/* @var $this PagamentoController */
/* @var $docentes Docente[] */
/* @var $tutores Tutor[] */

$this->breadcrumbs = array(
    'Pagamentos' => array('/pagamento'),
    'Gerenciamento de pagamentos de bolsas',
);
?>

<h1>Pagamentos de bolsas para docentes e tutores</h1>

<form id="bolsas-form" ng-app="bolsasApp" ng-controller="controlador" action="<?php echo Yii::app()->request->requestUri; ?>" method="post">

    <h2>Docentes</h2>

    <table ng-repeat="docente in docentes| orderBy: ['nome', 'sobrenome']" class="bolsas">
        <thead>
            <tr><td colspan="4"><a href="{{urlVisualizar(docente, 'docente')}}">{{docente.nomeCompleto}}</a></td></tr>
            <tr>
                <th>Descrição</th>
                <th style="width: 130px">Valor</th>
                <th style="width: 130px">Data</th>
                <th style="width: 15px"><button class="btn btn-default" ng-click="adicionarNovaBolsa(docente, 'docente')" type="button"><i class="fa fa-usd"></i></button></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-if="docente.bolsas.length == 0"><td colspan="4"><i>Nnehuma bolsa paga</i></td></tr>
            <tr ng-repeat="bolsa in docente.bolsas| orderBy: 'data_pagamento_ano_na_frente'" style="background-color:#F0F0F0">
                <td>{{bolsa.descricao}}</td>
                <td>{{bolsa.valor| currency: 'R$'}}</td>
                <td>{{bolsa.data_pagamento}}</td>
                <td><button class="btn btn-default" ng-click="remover(docente, bolsa)" type="button">X</button></td>
        <input name="Bolsa[]" type="hidden" value="{{serializarBolsa(bolsa)}}">
        </tr>
        </tbody>
    </table>

    <hr>

    <h2>Tutores</h2>

    <table ng-repeat="tutor in tutores| orderBy: ['nome', 'sobrenome']" class="bolsas">
        <thead>
            <tr><td colspan="4"><a href="{{urlVisualizar(tutor, 'tutor')}}">{{tutor.nomeCompleto}}</a></td></tr>
            <tr>
                <th>Descrição</th>
                <th style="width: 130px">Valor</th>
                <th style="width: 130px">Data</th>
                <th style="width: 15px"><button class="btn btn-default" ng-click="adicionarNovaBolsa(tutor, 'tutor')" type="button"><i class="fa fa-usd"></i></button></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-if="tutor.bolsas.length == 0"><td colspan="4"><i>Nnehuma bolsa paga</i></td></tr>
            <tr ng-repeat="bolsa in tutor.bolsas| orderBy: 'data_pagamento_ano_na_frente'" style="background-color:#F0F0F0">
                <td>{{bolsa.descricao}}</td>
                <td>{{bolsa.valor| currency: 'R$'}}</td>
                <td>{{bolsa.data_pagamento}}</td>
                <td><button class="btn btn-default" ng-click="remover(tutor, bolsa)" type="button">X</button></td>
        <input name="Bolsa[]" type="hidden" value="{{serializarBolsa(bolsa)}}">
        </tr>
        </tbody>
    </table>

    <br>

    <?php
    echo CHtml::submitButton('Salvar', array(
        'name' => 'Salvar',
        'class' => 'btn btn-success btn-lg',
    ));
    ?>

    <!-- Diálogo para selecionar bolsas -->

    <!--TODO: COLOCAR VALIDADOR PARA O VALOR-->
    <script type="text/ng-template" id="adicionarBolsa.html">
        <div class="modal-header">
        <h3 class="modal-title">Adicionar bolsa para o(a) {{tipo}} {{nome}}</h3>
        </div>
        <div class="modal-body">
        <label>Data</label>
        <input type="number" ng-model="dia_pagamento" class="muito-curto" ng-change="corrigirData()">
        <input type="number" ng-model="mes_pagamento" class="muito-curto" ng-change="corrigirData()">
        <input type="number" ng-model="ano_pagamento" class="muito-curto" ng-change="corrigirData()"><br>
        <label>Descrição</label><input type="text" ng-model="descricao"><br>
        <label>Valor</label><input type="number" placeholder="0.00" ng-model="valor" ng-pattern="/^[0-9]+((\.|,)[0-9]{1,2})?$/" step="0.01"><br>
        </div>
        <div class="modal-footer">
        <button class="btn btn-success" type="button" ng-click="ok()">Salvar bolsa</button>
        <button class="btn btn-default" type="button" ng-click="cancel()">Cancelar</button>
        </div>
    </script>

    <input ng-repeat="id in idsASeremDeletados" name="BolsasADeletar[]" value="{{id}}" type="hidden">

</form>