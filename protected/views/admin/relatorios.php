<?php ?>

<h1>Relatórios</h1>

<ul>
    <li><?php echo CHtml::link('Inscrições com documentos sendo analisados', array('exportador/relatorioDeInscricoesComStatus', 'status' => Inscricao::STATUS_DOCUMENTOS_SENDO_ANALISADOS, 'formato' => 'xls')) ?></li>
    <li><?php echo CHtml::link('Inscrições com documentos verificados', array('exportador/relatorioDeInscricoesComStatus', 'status' => Inscricao::STATUS_DOCUMENTOS_VERIFICADOS, 'formato' => 'xls')) ?></li>
    <li><?php echo CHtml::link('Alunos matriculados', array('exportador/relatorioDeInscricoesComStatus', 'status' => Inscricao::STATUS_MATRICULADO, 'formato' => 'xls')) ?></li>
    <li><?php echo CHtml::link('Componentes curriculares', ['exportador/relatorioDeComponentes']) ?></li>
    <li><?php echo CHtml::link('* Alunos com ofertas realizadas', ['exportador/relatorioDeAlunosComOfertas']) ?></li>
    <li><?php echo CHtml::link('Encontros presenciais', ['exportador/encontrosPresenciais']) ?></li>
</ul>

<br>
<?php echo CHtml::link('Exportar arquivos de notas para ProExWeb', ['relatoriosNotasProEx']) ?>
<br>
<br>

<ul>
    <li><?php echo CHtml::link('TCCs', ['exportador/tccs']) ?></li>
    <li><?php echo CHtml::link('Lista de estudantes concluintes', ['exportador/estudantesConcluintes']) ?></li>
    <li><?php echo CHtml::link('Lista de docentes', ['exportador/listaDocentes']) ?></li> 
    <li><?php echo CHtml::link('Lista de tutores', ['exportador/listaTutores']) ?></li> 
    <li><?php echo CHtml::link('Lista de colaboradores', ['exportador/listaColaboradores']) ?></li> 
    <li><?php echo CHtml::link('Lista de componentes por habilitação', ['exportador/componentesPorHabilitacao']) ?></li>
    <li><?php echo CHtml::link('Lista de informações de certificados (apenas alunos de especialização)', ['exportador/listaCertificados']) ?></li>
    <li><?php echo CHtml::link('Lista de todos os alunos', ['exportador/listaTodosAlunos']) ?></li>
    <li><?php echo CHtml::link('Lista de todos os certificados de conclusão em formato TXT', ['exportador/certificadosConclusaoTxt']) ?></li>
</ul>

<p>* devido à grande quantidade de dados, esses relatórios só podem ser exportados como CSV</p>

<h2>Relatório personalizado de alunos</h2>

<div ng-app="relatorioPersonalizadoApp" ng-controller="controlador">

<table>
    <thead><tr><th colspan="2" style="padding: 5px">Campos</th></tr></thead>
    <tbody>
<!--        <tr><td>Status da inscrição</td><td>
            <select ng-model="statusInscricao" ng-options="status for status in statusInscricao"></select>
        </td></tr>-->
<!--        <tr><td>Status do aluno</td><td>
            <select ng-model="statusAluno" ng-options="status for status in statusAluno"></select>
        </td></tr>-->
        <tr><td>Conjunto de campos</td>
            <Td><select ng-model="conjuntoDeCamposSelecionado"
                        ng-options="conjunto for conjunto in conjuntosDeCampos"
                        ng-change="atualizarCampos()"></select></td>
        </tr>
        <tr
            ng-repeat="(campo, detalhes) in campos"
            ng-style="{'background-color': $even ? '#F0F0F0' : '#FFF'}"
            ng-click="selecionar(campo)">
            <td style="padding: 5px">{{detalhes.label}}</td>
            <td style="text-align: center">
                <input id="Relatorio_{{campo}}_selecionado"
                       name="Relatorio[{{campo}}][selecionado]"
                       type="checkbox"
                       ng-model="detalhes.selecionado"
                       ng-click="$event.stopPropagation()">
            </td>
        </tr>
    </tbody>
</table>
<br>
<button ng-click="gerar()" type="button">Gerar relatório</button>

</div>
