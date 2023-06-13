<?php

$NUMBER_OF_COLUMNS = 6;

?>

<h1>Gerenciar ofertas de componentes</h1>
<br>

<div ng-app="gerenciarOfertasApp" ng-controller="controlador">

    <div class="simulador-container">

        <div class="legenda">
            <?php $this->renderPartial('/comuns/_legendasHabilitacoes'); ?>

            <h3>Botões</h3>
            <ul>
                <li><i class="fa fa-user"></i> - Associar docentes e tutores à oferta</li>
                <li><i class="fa fa-link"></i> - Adicionar informações relativas ao Moodle à oferta</li>
                <li><i class="fa fa-file-text-o"></i> - Gerar o arquivo de cadastro dos alunos dessa oferta na sala no Moodle</li>
                <li><i class="fa fa-calendar-o"></i> - Alterar o período da oferta</li>
                <li><i class="fa fa-times"></i> - Excluir oferta (se esse botão não aparece, isso significa que pelo menos um aluno já se inscreveu nessa oferta)</li>
            </ul>
        </div>

        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'ofertas-form',
            'enableAjaxValidation' => false,
        ));
        ?>

        <input ng-repeat="id in idsASeremDeletados" name="OfertasADeletar[]" value="{{id}}" type="hidden">

        <!-- Constrói um form escondido para que todas as ofertas possam ser salvas de uma vez -->
        <div ng-repeat="periodo in periodos" ng-show="false">
            <input ng-repeat="oferta in periodo.ofertas" type="hidden" name="Ofertas[]" value="{{serializar(oferta)}}">
        </div>

        <h2 ng-if="periodos.length == 0">Ainda não há nenhuma oferta. Comece adiionando um novo período para ofertar componentes.</h2>

        <button ng-click="gerarListaMoodleTodasOfertas()" type="button" class="btn btn-default"><i class="fa fa-file-text-o"></i>Gerar lista de alunos de todas as ofertas</button>
        <br><br>

        <button type="button" style="font-size: 20px" ng-click="ativarOfertasPassadas()" ng-if="!mostrarOfertasPassadas">Mostrar ofertas passadas</button>
        <button type="button" style="font-size: 20px" ng-click="ativarOfertasPassadas()" ng-if="mostrarOfertasPassadas">Esconder ofertas passadas</button>
        <br><br>

        <table
            class="simulador"
            ng-repeat="periodo in periodos | orderBy: ['ano', 'mes']"
            ng-show="deveMostrarPeriodo(periodo)"
        >
            <thead>
                <tr>
                    <td colspan="<?php echo ($NUMBER_OF_COLUMNS + count($habilitacoes)); ?>">
                        <h2>Ofertas de {{periodo.mes}}/{{periodo.ano}}</h2>
                        <ul>
                            <li>Carga horária: {{cargaHorariaDoPeriodo(periodo)}} horas</li>
                            <li>{{periodo.ofertas.length}} componentes</li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th>Componente curricular</th>
                    <?php foreach ($habilitacoes as $habilitacao) echo "<th>{$habilitacao->letra}</th>"; ?>
                    <!-- <th style="width: 30px">Carga horária</th> -->
                    <th style="width: 15px"></th>
                    <th style="width: 15px"></th>
                    <th style="width: 15px"></th>
                    <th style="width: 15px"></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr ng-if="periodo.ofertas.length === 0"><td><i>Não há nenhum componente neste período</i></td><td colspan="<?php echo ($NUMBER_OF_COLUMNS + count($habilitacoes) - 1); ?>"></td></tr>
                <tr ng-repeat-start="oferta in periodo.ofertas | orderBy: ['-ehNecessaria', 'nome']" class="simulador-linha">
                    <td>{{oferta.componente.nome}}</td>
                    <td
                        ng-repeat="prioridade in oferta.componente.prioridades track by $index"
                        ng-class="prioridade.classeCss"
                        ng-style="{ 'text-align': 'center', 'background-color': prioridade.classeCss !== '' ? prioridade.cor : '' }"
                    >
                        {{prioridade.letra}}
                    </td>
                    <!-- <td>{{oferta.componente.cargaHoraria}}</td> -->
                    <td><button ng-click="associarDocentesETutores(oferta)" type="button" class="btn btn-default" ng-if="!oferta.projetoIntegrador"><i class="fa fa-user"></i></button></td>
                    <td><button ng-click="adicionarInfoMoodle(oferta)" type="button" class="btn btn-default" ng-if="!oferta.projetoIntegrador"><i class="fa fa-pencil"></i></button></td>
                    <td><button ng-click="gerarListaMoodle(oferta)" type="button" class="btn btn-default" ng-if="!oferta.podeSerDeletada"><i class="fa fa-file-text-o"></i></button></td>
                    <td><button ng-click="alterarPeriodoDa(oferta)" type="button" class="btn btn-default" ng-if="!oferta.projetoIntegrador"><i class="fa fa-calendar-o"></i></button></td>
                    <td><button ng-click="removerOferta(oferta, periodo)" type="button" class="btn btn-default" ng-if="oferta.podeSerDeletada"><i class="fa fa-times"></i></button></td>
                </tr>
                <tr style="background-color: #F9F9F9; font-size: 0.9em">
                    <td colspan="<?php echo ($NUMBER_OF_COLUMNS + count($habilitacoes)); ?>">
                        <p>Número de inscrições: {{oferta.numeroDeInscricoesAtivas}}</p>
                        <p>Data de início da oferta: {{oferta.dataInicio | date: 'dd/MM/yyyy'}}</p>
                        <p>Link da sala da oferta no Moodle: <a href="{{oferta.linkMoodle}}">{{oferta.linkMoodle}}</a></p>
                        <p>Código da sala da oferta no Moodle: {{oferta.codigoMoodle}}</p>
                    </td>
                </tr>
                <tr ng-repeat-end ng-if="oferta.docentes.length != 0 || oferta.tutores.length != 0" style="background: #F9F9F9; font-size: 0.9em">
                    <td colspan="<?php echo ($NUMBER_OF_COLUMNS + count($habilitacoes)); ?>">
                        <p style="font-weight: bold">Docentes:</p>
                        <ul>
                            <li ng-repeat="docente in oferta.docentes| orderBy: 'nome'">
                                {{docente.nomeCompleto}}
                                <button type="button" ng-click="desassociarDocente(docente, oferta)">X</button>
                            </li>
                        </ul>

                        <p style="font-weight: bold">Tutores:</p>
                        <ul>
                            <li ng-repeat="tutor in oferta.tutores| orderBy: 'nome'">
                                {{tutor.nomeCompleto}}
                                <button type="button" ng-click="desassociarTutor(tutor, oferta)">X</button>
                            </li>
                        </ul>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="<?php echo ($NUMBER_OF_COLUMNS + count($habilitacoes)); ?>">
                        <button type="button" class="btn btn-default" ng-click="adicionarNovasOfertas(periodo)">+ Adicionar novas ofertas</button>
                        <button type="button" class="btn btn-default" ng-click="removerPeriodo(periodo)" ng-if="periodo.podeSerDeletado">Remover período</button>
                    </td>
                </tr>
            </tfoot>
        </table>

        <button ng-click="adicionarNovoPeriodo()" type="button" class="btn btn-default">Adicionar novo período</button>

        <?php
        echo CHtml::submitButton('Salvar', array(
            'class' => 'btn btn-success btn-lg',
        ));
        ?>

        <?php $this->endWidget(); ?>

    </div>

    <!-- Diálogo para adicionar novos componentes para ofertar -->
    <script type="text/ng-template" id="adicionarNovasOfertas.html">
        <div class="modal-header">
        <h3 class="modal-title">Ofertar componentes curriculares no período de {{mes}}/{{ano}}</h3>
        </div>
        <div class="modal-body">

        <table class="simulador">
        <tr><td colspan="<?php echo (2 + count($habilitacoes)); ?>"><h3>Componentes curriculares gerais</h3></td></tr>
        <tr>
            <th>Componente</th>
            <?php foreach ($habilitacoes as $habilitacao) echo "<th>{$habilitacao->letra}</th>"; ?>
            <!-- <th>Carga horária</th> -->
            <th></th>
        </tr>
        <tr ng-repeat="componente in componentesDisponiveis | orderBy: 'nome'" ng-class="{'selecionado': componente.selecionado}" ng-click="selecionar(componente)" ng-if="componente.ehNecessaria">
            <td>{{componente.nome}}</td>
            <td
                ng-repeat="prioridade in componente.prioridades track by $index"
                ng-style="{ 'background-color': prioridade.cor }"
            >
                {{prioridade.letra}}
            </td>
            <!-- <td style="text-align: center">{{componente.cargaHoraria}}</td> -->
            <td><input type="checkbox" ng-model="componente.selecionado" ng-click="$event.stopPropagation()"></td>
        </tr>
        </table>
        <br>

        <table class="simulador">
        <tr><td colspan="<?php echo (2 + count($habilitacoes)); ?>"><h3>Componentes curriculares específicas</h3></td></tr>
        <tr>
            <th>Componente</th>
            <?php foreach ($habilitacoes as $habilitacao) echo "<th>{$habilitacao->letra}</th>"; ?>
            <!-- <th>Carga horária</th> -->
            <th></th>
        </tr>
        <tr ng-repeat="componente in componentesDisponiveis | orderBy: 'nome'" ng-class="{'selecionado': componente.selecionado}" ng-click="selecionar(componente)" ng-if="!componente.ehNecessaria">
            <td>{{componente.nome}}</td>
            <td
                ng-repeat="prioridade in componente.prioridades track by $index"
                ng-style="{ 'background-color': prioridade.cor }"
            >
                {{prioridade.letra}}
            </td>
            <!-- <td style="text-align: center">{{componente.cargaHoraria}}</td> -->
            <td><input type="checkbox" ng-model="componente.selecionado" ng-click="$event.stopPropagation()"></td>
        </tr>
        </table>

        </div>
        <div class="modal-footer">
        <button class="btn btn-success" type="button" ng-click="ok()">Adicionar componentes ao período</button>
        <button class="btn btn-default" type="button" ng-click="cancel()">Cancelar</button>
        </div>
    </script>

    <!-- Diálogo para adicionar um novo período de ofertas -->
    <script type="text/ng-template" id="adicionarNovoPeriodo.html">
        <div class="modal-header">
        <h3 class="modal-title">Informe o ano e o mês referentes a esse bloco de ofertas</h3>
        </div>
        <div class="modal-body">
        <label for="ano">Ano</label><input id="ano" type="number" ng-model="ano"><br>
        <label for="mes">Mês</label><input id="mes" type="number" ng-model="mes"><br>
        </div>
        <div class="modal-footer">
        <button class="btn btn-success" type="button" ng-click="ok()">Adicionar bloco</button>
        <button class="btn btn-default" type="button" ng-click="cancel()">Cancelar</button>
        </div>
    </script>

    <!-- Diálogo para associar docentes e tutores à oferta -->
    <script type="text/ng-template" id="associarDocentesETutores.html">
        <div class="modal-header">
        <h3 class="modal-title">Associar docentes e tutores à oferta</h3>
        </div>
        <div class="modal-body">
        <h3>Associar docentes e tutores à oferta do componente "{{componenteSelecionada.nome}}" ({{mes}}/{{ano}})</h3>
        <table class="simulador">
        <tr><td colspan="2"><h3>Docentes desta oferta</h3></td></tr>
        <tr><th>Docente</th><th width="20px"></th></tr>
        <tr ng-repeat="docente in docentesDisponiveis | orderBy: 'nome'" ng-class="{'selecionado': docente.selecionado}" ng-click="selecionar(docente)">
        <td>{{docente.nome}} {{docente.sobrenome}}</td>
        <td><input type="checkbox" ng-model="docente.selecionado" ng-click="$event.stopPropagation()"></td>
        </tr>
        </table>
        <br>
        <table class="simulador">
        <tr><td colspan="2"><h3>Tutores desta oferta</h3></td></tr>
        <tr><th>Tutor</th><th width="20px"></th></tr>
        <tr ng-repeat="tutor in tutoresDisponiveis | orderBy: 'nome'" ng-class="{'selecionado': tutor.selecionado}" ng-click="selecionar(tutor)">
        <td>{{tutor.nome}} {{tutor.sobrenome}}</td>
        <td><input type="checkbox" ng-model="tutor.selecionado" ng-click="$event.stopPropagation()"></td>
        </tr>
        </table>
        <br>
        </div>
        <div class="modal-footer">
        <button class="btn btn-success" type="button" ng-click="ok()">Associar docentes e tutores</button>
        <button class="btn btn-default" type="button" ng-click="cancel()">Cancelar</button>
        </div>
    </script>

    <!-- Diálogo para editar informações adicionais da oferta -->
    <script type="text/ng-template" id="adicionarInfoMoodle.html">
        <div class="modal-header">
        <h3 class="modal-title">Adicionar informações à oferta</h3>
        </div>
        <div class="modal-body">
        <h3>Adicionar informações à oferta do componente "{{componenteSelecionada.nome}}" ({{mes}}/{{ano}})</h3>
        <br>
        <label for="dataInicio">Data de início</label>
        <input id="dataInicio" type="date" ng-model="dataInicio" ><br>
        <label for="linkMoodle">Link da sala da oferta no moodle</label>
        <input id="linkMoodle" type="text" ng-model="linkMoodle" ><br>
        <label for="codigoMoodle" style="clear: both">Código da sala da oferta no moodle</label>
        <input id="codigoMoodle" type="text" ng-model="codigoMoodle"><br>
        </div>
        <div class="modal-footer">
        <button class="btn btn-success" type="button" ng-click="ok()">OK</button>
        <button class="btn btn-default" type="button" ng-click="cancel()">Cancelar</button>
        </div>
    </script>

    <!-- Diálogo para alterar o período de uma oferta -->
    <script type="text/ng-template" id="alterarPeriodoDaOferta.html">
        <div class="modal-header">
        <h3 class="modal-title">Informe o novo período da oferta {{oferta.nome}}</h3>
        </div>
        <div class="modal-body">
        <label for="ano">Ano</label><input id="ano" type="number" ng-model="ano"><br>
        <label for="mes">Mês</label><input id="mes" type="number" ng-model="mes"><br>
        <span style="color: red">ATENÇÃO: esta alteração é salva imediatamente e as demais alterações não salvas em ofertas serão perdidas</span>
        </div>
        <div class="modal-footer">
        <button class="btn btn-success" type="button" ng-click="ok()">Atualizar</button>
        <button class="btn btn-default" type="button" ng-click="cancel()">Cancelar</button>
        </div>
    </script>

</div>
