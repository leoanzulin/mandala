<?php
?>

<h1>Visualizar ofertas de componentes</h1>
<br>

<div ng-app="visualizarOfertasApp" ng-controller="controlador">

<label for="turma">Turma</label>
<select id="turma" ng-model="turmaAtual" ng-options="turma for turma in turmasDisponiveis"></select>

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

    <h2 ng-if="turmas[turmaAtual - 1].periodos.length == 0">Ainda não há nenhuma oferta nesta turma.</h2>

    <table class="simulador" ng-repeat="periodo in turmas[turmaAtual - 1].periodos | orderBy: ['ano', 'mes']">
        <thead>
            <tr><td colspan="9">
                <h3>Ofertas de {{periodo.mes}}/{{periodo.ano}}</h3>
                <ul>
                    <li>{{periodo.ofertas.length}} componentes</li>
                </ul>
            </td></tr>
            <tr><th>Componente curricular</th><th>G</th><th>D</th><th>M</th><th>T</th><th>P</th><th style="width: 30px">Carga horária</th><th style="width: 15px"></th></tr>
        </thead>
        <tbody>
            <tr ng-if="periodo.ofertas.length === 0"><td><i>Não há nenhuma componente neste período</i></td><td colspan="8"></td></tr>
            <tr ng-repeat-start="oferta in periodo.ofertas | orderBy: ['-ehNecessaria', 'nome']" class="simulador-linha">
                <td style="width: auto; text-align: left">{{oferta.componente.nome}}</td>
                <td ng-class="oferta.componente.classesCss[3]">{{oferta.componente.prioridadesLetras[3]}}</td>
                <td ng-class="oferta.componente.classesCss[4]">{{oferta.componente.prioridadesLetras[4]}}</td>
                <td ng-class="oferta.componente.classesCss[1]">{{oferta.componente.prioridadesLetras[1]}}</td>
                <td ng-class="oferta.componente.classesCss[2]">{{oferta.componente.prioridadesLetras[2]}}</td>
                <td ng-class="oferta.componente.classesCss[5]">{{oferta.componente.prioridadesLetras[5]}}</td>
                <td style="text-align: center">{{oferta.componente.cargaHoraria}}</td>
            </tr>
            <tr ng-repeat-end ng-if="oferta.docentes.length != 0 || oferta.tutores.length != 0 || oferta.inscricoes.length != 0" style="background: #F9F9F9; font-size: 0.9em">
                <td colspan="9">
                    <p style="font-weight: bold">Docentes:</p>
                    <ul>
                        <li ng-repeat="docente in oferta.docentes | orderBy: 'nome'">
                            {{docente.nome}} {{docente.sobrenome}}
                        </li>
                    </ul>
                    <p style="font-weight: bold">Tutores:</p>
                    <ul>
                        <li ng-repeat="tutor in oferta.tutores | orderBy: 'nome'">
                            {{tutor.nome}} {{tutor.sobrenome}}
                        </li>
                    </ul>
                    <p style="font-weight: bold">Alunos inscritos:</p>
                    <ul>
                        <li ng-repeat="inscricao in oferta.inscricoes | orderBy: 'toString()'">
                            {{inscricao}}
                        </li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>

</div>
