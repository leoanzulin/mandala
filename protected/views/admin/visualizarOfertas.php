<?php
// TODO: Refatorar esta página
?>

<h1>Visualizar ofertas de componentes</h1>
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
    </div>

    <h3 ng-if="periodos.length === 0"><b>Não há nenhum período</b></h3>

    <table class="simulador" ng-repeat="periodo in periodos | orderBy: ['ano', 'mes']">
        <thead>
            <tr><td colspan="9">
                <h3>Ofertas de {{periodo.mes}}/{{periodo.ano}}</h3>
                <ul>
                    <li>{{periodo.componentes.length}} componentes</li>
                </ul>
            </td></tr>
            <tr><th>Componente curricular</th><th>G</th><th>D</th><th>M</th><th>T</th><th>P</th><th style="width: 30px">Carga horária</th><th style="width: 15px"></th></tr>
        </thead>
        <tbody>
            <tr ng-if="periodo.componentes.length === 0"><td><i>Não há nenhuma componente neste período</i></td><td colspan="8"></td></tr>
            <tr ng-repeat-start="componente in periodo.componentes | orderBy: ['-ehNecessaria', 'nome']" class="simulador-linha">
                <td style="width: auto; text-align: left">{{componente.nome}} - {{componente.cargaHoraria}} horas</td>
                <td ng-class="componente.classesCss[3]">{{componente.prioridadesLetras[3]}}</td>
                <td ng-class="componente.classesCss[4]">{{componente.prioridadesLetras[4]}}</td>
                <td ng-class="componente.classesCss[1]">{{componente.prioridadesLetras[1]}}</td>
                <td ng-class="componente.classesCss[2]">{{componente.prioridadesLetras[2]}}</td>
                <td ng-class="componente.classesCss[5]">{{componente.prioridadesLetras[5]}}</td>
                <td style="text-align: center">{{componente.cargaHoraria}}</td>
            </tr>
            <tr ng-repeat-end ng-if="componente.docentes.length != 0 || componente.tutores.length != 0 || componente.inscricoes.length != 0" style="background: #F9F9F9; font-size: 0.9em">
                <td colspan="9">
                    <p style="font-weight: bold">Docentes:</p>
                    <ul>
                        <li ng-repeat="docente in componente.docentes | orderBy: 'nome'">
                            {{docente.nome}} {{docente.sobrenome}}
                        </li>
                    </ul>
                    <p style="font-weight: bold">Tutores:</p>
                    <ul>
                        <li ng-repeat="tutor in componente.tutores | orderBy: 'nome'">
                            {{tutor.nome}} {{tutor.sobrenome}}
                        </li>
                    </ul>
                    <p style="font-weight: bold">Alunos inscritos:</p>
                    <ul>
                        <li ng-repeat="inscricao in componente.inscricoes | orderBy: 'toString()'">
                            {{inscricao}}
                        </li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

</div>
