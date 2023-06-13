<?php
// TODO: Refatorar esta página
?>

<h1>Gerenciar ofertas de componentes</h1>
<br>

<div ng-app="administrarOfertasApp" ng-controller="controlador">

<div class="simulador-container">

    <div class="legenda">
        <h3>Legenda das habilitações</h3>
        <ul>
            <ul>
                <li><b>G</b>: Gestão da Educação a Distância</li>
                <li><b>D</b>: Docência Virtual</li>
                <li><b>M</b>: Mídias na Educação</li>
                <li><b>T</b>: Produção e Uso de Tecnologias para Educação</li>
                <li><b>P</b>: Design Instrucional (Projeto e Desenho Pedagógico)</li>
            </ul>
        </ul>
        
        <p>Clique no botão <i class="fa fa-user"></i> para associar docentes e tutores a cada oferta</p>
        <p>Clique no botão <i class="fa fa-link"></i> para adicionar informações relativas ao Moodle de cada oferta</p>
        
        <p>Se uma oferta não pode ser excluída, isso significa que pelo menos um aluno já se inscreveu nela.</p>
    </div>

    <h3 ng-if="periodos.length === 0"><b>Não há nenhum período</b></h3>

    <?php $form=$this->beginWidget('CActiveForm', array(
        'id'=>'ofertas-form',
        'enableAjaxValidation'=>false,
    )); ?>

    <table class="simulador" ng-repeat="periodo in periodos | orderBy: ['ano', 'mes']">
        <thead>
            <tr><td colspan="9">
                <h3>Ofertas de {{periodo.mes}}/{{periodo.ano}}</h3>
                <ul>
<!--                    <li>Carga horária: {{cargaHorariaDoPeriodo(periodo)}} horas</li>-->
                    <li>{{periodo.componentes.length}} componentes</li>
                </ul>
            </td></tr>
            <tr>
                <th>Componente curricular</th>
                <th>G</th>
                <th>D</th>
                <th>M</th>
                <th>T</th>
                <th>P</th>
                <th style="width: 30px">Carga horária</th>
                <th style="width: 15px"></th>
                <th style="width: 15px"></th>
                <th style="width: 15px"></th>
            </tr>
        </thead>
        <tbody>
            <tr ng-if="periodo.componentes.length === 0"><td><i>Não há nenhuma componente neste período</i></td><td colspan="8"></td></tr>
            <tr ng-repeat-start="componente in periodo.componentes | orderBy: ['-ehNecessaria', 'nome']" class="simulador-linha">
                <td style="width: auto; text-align: left">{{componente.nome}}</td>
                <td ng-class="componente.classesCss[3]">{{componente.prioridadesLetras[3]}}</td>
                <td ng-class="componente.classesCss[4]">{{componente.prioridadesLetras[4]}}</td>
                <td ng-class="componente.classesCss[1]">{{componente.prioridadesLetras[1]}}</td>
                <td ng-class="componente.classesCss[2]">{{componente.prioridadesLetras[2]}}</td>
                <td ng-class="componente.classesCss[5]">{{componente.prioridadesLetras[5]}}</td>
                <td style="text-align: center">{{componente.cargaHoraria}}</td>
                <td><button ng-click="associarDocentes(componente, periodo)" type="button" class="btn btn-default"><i class="fa fa-user"></i></button></td>
                <td><button ng-click="adicionarInfoMoodle(componente, periodo)" type="button" class="btn btn-default"><i class="fa fa-link"></i></button></td>
                <td><button ng-click="remover(componente, periodo)" type="button" class="btn btn-default" ng-if="componente.podeSerDeletada">X</button></td>

                <input
                    id="Oferta_{{periodo.ano + '0' + periodo.mes}}_{{componente.id}}_componente_id"
                    type="hidden"
                    name="Oferta[ofertas][{{componente.id}}_{{periodo.ano}}_{{periodo.mes}}][componente_id]"
                    value="{{componente.id}}">
                <input
                    id="Oferta_{{periodo.ano + '0' + periodo.mes}}_{{componente.id}}_link_moodle"
                    type="hidden"
                    name="Oferta[ofertas][{{componente.id}}_{{periodo.ano}}_{{periodo.mes}}][link_moodle]"
                    value="{{componente.linkMoodle}}">
                <input
                    id="Oferta_{{periodo.ano + '0' + periodo.mes}}_{{componente.id}}_codigo_moodle"
                    type="hidden"
                    name="Oferta[ofertas][{{componente.id}}_{{periodo.ano}}_{{periodo.mes}}][codigo_moodle]"
                    value="{{componente.codigoMoodle}}">
            </tr>
            <tr style="background-color: #F9F9F9; font-size: 0.9em">
                <td colspan="10">
                    <p>Link da sala da oferta no Moodle: <a href="{{componente.linkMoodle}}">{{componente.linkMoodle}}</a></p>
                    <p>Código da sala da oferta no Moodle: {{componente.codigoMoodle}}</p>
                </td>
            </tr>
            <tr ng-repeat-end ng-if="componente.docentes.length != 0 || componente.tutores.length != 0" style="background: #F9F9F9; font-size: 0.9em">
                <td colspan="10">
                    <p style="font-weight: bold">Docentes:</p>
                    <ul>
                        <li ng-repeat="docente in componente.docentes | orderBy: 'nome'">
                            {{docente.nome}} {{docente.sobrenome}}
                            <button type="button" ng-click="desassociarDocente(docente, componente)">X</button>
                            <input id="Oferta_{{periodo.ano + '0' + periodo.mes}}_{{componente.id}}_docente_{{docente.cpf}}" type="hidden" name="Oferta[ofertas][{{componente.id}}_{{periodo.ano}}_{{periodo.mes}}][docentes][]" value="{{docente.cpf}}">
                        </li>
                    </ul>

                    <p style="font-weight: bold">Tutores:</p>
                    <ul>
                        <li ng-repeat="tutor in componente.tutores | orderBy: 'nome'">
                            {{tutor.nome}} {{tutor.sobrenome}}
                            <button type="button" ng-click="desassociarTutor(tutor, componente)">X</button>
                            <input id="Oferta_{{periodo.ano + '0' + periodo.mes}}_{{componente.id}}_docente_{{docente.cpf}}" type="hidden" name="Oferta[ofertas][{{componente.id}}_{{periodo.ano}}_{{periodo.mes}}][tutores][]" value="{{tutor.cpf}}">
                        </li>
                    </ul>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr><td colspan="9">
                <button type="button" class="btn btn-default" ng-click="adicionarComponentes(periodo)">+ Adicionar componente curricular</button>
                <button type="button" class="btn btn-default" ng-click="removerPeriodo(periodo)" ng-if="periodo.podeSerDeletado">Remover período</button>
            </td></tr>
        </tfoot>
    </table>

    <!--<button ng-click="adicionarNovoPeriodo()" type="button" class="btn btn-default">Adicionar novo período</button>-->

    <?php echo CHtml::submitButton('Salvar ofertas de componentes', array(
        'class' => 'btn btn-success btn-lg',
//        'confirm' => 'Essas ofertas não poderão ser alteradas, tem certeza que deseja continuar?',
    )); ?>

    <?php $this->endWidget(); ?>

</div>

<!-- Diálogo para selecionar disciplinas -->

<script type="text/ng-template" id="myModalContent.html">
    <div class="modal-header">
        <h3 class="modal-title">Ofertar componentes curriculares no período de {{mes}}/{{ano}}</h3>
    </div>
    <div class="modal-body">

        <table class="simulador">
            <tr><td colspan="8"><h3>Componentes curriculares gerais</h3></td></tr>
            <tr><th>Componente</th><th>G</th><th>D</th><th>M</th><th>T</th><th>P</th><th>Carga horária</th><th></th></tr>
            <tr ng-repeat="componente in componentesDisponiveis | orderBy: 'nome'" ng-class="{'selecionado': componente.selecionado}" ng-click="selecionar(componente)" ng-if="componente.ehNecessaria">
                <td>{{componente.nome}}</td>
                <td ng-class="componente.classesCss[3]">{{componente.prioridadesLetras[3]}}</td>
                <td ng-class="componente.classesCss[4]">{{componente.prioridadesLetras[4]}}</td>
                <td ng-class="componente.classesCss[1]">{{componente.prioridadesLetras[1]}}</td>
                <td ng-class="componente.classesCss[2]">{{componente.prioridadesLetras[2]}}</td>
                <td ng-class="componente.classesCss[5]">{{componente.prioridadesLetras[5]}}</td>
                <td style="text-align: center">{{componente.cargaHoraria}}</td>
                <td><input type="checkbox" ng-model="componente.selecionado" ng-click="$event.stopPropagation()"></td>
            </tr>
        </table>
        <br>

        <table class="simulador">
            <tr><td colspan="8"><h3>Componentes curriculares específicas</h3></td></tr>
            <tr><th>Componente</th><th>G</th><th>D</th><th>M</th><th>T</th><th>P</th><th>Carga horária</th><th></th></tr>
            <tr ng-repeat="componente in componentesDisponiveis | orderBy: 'nome'" ng-class="{'selecionado': componente.selecionado}" ng-click="selecionar(componente)" ng-if="!componente.ehNecessaria">
                <td>{{componente.nome}}</td>
                <td ng-class="componente.classesCss[3]">{{componente.prioridadesLetras[3]}}</td>
                <td ng-class="componente.classesCss[4]">{{componente.prioridadesLetras[4]}}</td>
                <td ng-class="componente.classesCss[1]">{{componente.prioridadesLetras[1]}}</td>
                <td ng-class="componente.classesCss[2]">{{componente.prioridadesLetras[2]}}</td>
                <td ng-class="componente.classesCss[5]">{{componente.prioridadesLetras[5]}}</td>
                <td style="text-align: center">{{componente.cargaHoraria}}</td>
                <td><input type="checkbox" ng-model="componente.selecionado" ng-click="$event.stopPropagation()"></td>
            </tr>
        </table>

    </div>
    <div class="modal-footer">
        <button class="btn btn-success" type="button" ng-click="ok()">Adicionar componentes ao período</button>
        <button class="btn btn-default" type="button" ng-click="cancel()">Cancelar</button>
    </div>
</script>

<!--<script type="text/ng-template" id="anomesperiodo.html">
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
</script>-->

<script type="text/ng-template" id="associarDocentes.html">
    <div class="modal-header">
        <h3 class="modal-title">Associar docentes à oferta</h3>
    </div>
    <div class="modal-body">
        <h3>Associar docentes e tutores à oferta da componente "{{componenteSelecionada.nome}}" ({{mes}}/{{ano}})</h3>
        <table class="simulador">
            <tr><td colspan="3"><h3>Docentes desta oferta</h3></td></tr>
            <tr><th>Docente</th><th width="20px"></th></tr>
            <tr ng-repeat="docente in docentesDisponiveis | orderBy: 'nome'" ng-class="{'selecionado': docente.selecionado}" ng-click="selecionar(docente)">
                <td>{{docente.nome}} {{docente.sobrenome}}</td>
                <td><input type="checkbox" ng-model="docente.selecionado" ng-click="$event.stopPropagation()"></td>
            </tr>
        </table>
        <br>
        <table class="simulador">
            <tr><td colspan="3"><h3>Tutores desta oferta</h3></td></tr>
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

<script type="text/ng-template" id="adicionarInfoMoodle.html">
    <div class="modal-header">
        <h3 class="modal-title">Adicionar informações do Moodle à oferta</h3>
    </div>
    <div class="modal-body">
        <h3>Adicionar informações do Moodle à oferta da componente "{{componenteSelecionada.nome}}" ({{mes}}/{{ano}})</h3>
        <br>
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

</div>
