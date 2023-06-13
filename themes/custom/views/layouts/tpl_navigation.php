<?php

function temPapel($papel)
{
    return !ehSuperUsuario() && Yii::app()->user->checkAccess($papel);
}

function ehSuperUsuario()
{
    return Yii::app()->user->isSuperuser === true || Yii::app()->user->checkAccess('Secretaria');
}

function ehVisitante()
{
    return Yii::app()->user->isGuest;
}

function ehAlunoDeEspecializacao()
{
    $inscricao = Inscricao::model()->findByPk(Yii::app()->session['inscricao_id']);
    if (empty($inscricao)) return false;
    return temPapel('Aluno') && $inscricao->ehAlunoDeEspecializacao();
}

// Recupera as páginas personalizadas
$criteria = new CDbCriteria(array('order' => 'page_position asc'));
$menuPaginas = array();

$menuPaginas[] = array('label' => 'Estudantes', 'url' => array('/admin/gerenciarInscricoes'), 'items' => array(
    array('label' => 'Gerenciar Inscrições', 'url'=>array('/admin/gerenciarInscricoes')),
    //array('label' => 'Visualizar alunos inscritos', 'url'=>array('/admin/visualizarAlunosInscritos')),
    array('label' => 'Visualizar alunos matriculados', 'url'=>array('/admin/visualizarAlunosMatriculados')),
    array('label' => 'Gerenciar TCC', 'url'=>array('/admin/gerenciarTccs')),
    array('label' => 'Emitir certificados', 'url'=>array('/admin/emitirCertificados')),
), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Componentes', 'url' => array('/oferta/gerenciar'), 'items' => array(
    array('label' => 'Gerenciar ofertas', 'url'=>array('/oferta/gerenciar')),
    array('label' => 'Gerenciar componentes', 'url'=>array('/componenteCurricular/gerenciar')),
    array('label' => 'Gerenciar habilitações', 'url'=>array('/habilitacao/gerenciar')),
    array('label' => 'Visualizar número de inscrições em ofertas', 'url'=>array('/admin/visualizarInscricoesEmOfertas')),
    array('label' => 'Notas', 'url' => array('/admin/gerenciarNotas')),
), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Gestão de pessoas', 'url' => array('/docente/gerenciar'), 'items' => array(
    array('label' => 'Docentes', 'url'=>array('/docente/gerenciar')),
    array('label' => 'Tutores', 'url'=>array('/tutor/gerenciar')),
    array('label' => 'Colaboradores', 'url'=>array('/colaborador/gerenciar')),
), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Encontros', 'url' => array('/encontro/gerenciar'), 'visible' => ehSuperUsuario());

$menuPaginas[] = ['label' => 'Financeiro', 'url' => ['/financeiro/pagamentosDeAlunos'], 'items' => [
    ['label' => 'Pagamentos de alunos', 'url'=> ['/financeiro/pagamentosDeAlunos']],
    ['label' => 'Pagamentos de colaboradores', 'url'=> ['/financeiro/pagamentosDeColaboradores', 'mes' => date('n'), 'ano' => date('Y')]],
    ['label' => 'Viagens', 'url'=> ['/financeiro/viagens']],
    ['label' => 'Compras', 'url'=> ['/financeiro/compras']],
], 'submenuOptions' => ['class' => 'nav-sub'], 'visible' => ehSuperUsuario()];

$menuPaginas[] = array('label' => 'Relatórios', 'url' => array('/admin/relatorios'), 'visible' => ehSuperUsuario());
$menuPaginas[] = array('label' => 'Configurações', 'url' => array('/configuracao'), 'visible' => ehSuperUsuario());

// Funcionalidades dos alunos
$menuPaginas[] = array('label' => 'Monte sua trilha pedagógica / inscrição em componentes', 'url' => array('/aluno/inscricao'), 'visible' => temPapel('Aluno'));
// $menuPaginas[] = array('label' => 'Confirmar inscrições em ofertas', 'url' => array('/aluno/confirmarInscricoesEmOfertas'), 'visible' => temPapel('Aluno'));
$menuPaginas[] = array('label' => 'Histórico', 'url' => array('/aluno/historico'), 'visible' => temPapel('Aluno'));
$menuPaginas[] = ['label' => 'TCC', 'url' => ['/aluno/tcc'], 'items' => [
    ['label' => 'Cadastro', 'url' => ['/aluno/criarTcc']],
    ['label' => 'Entrega', 'url' => ['/aluno/entregarTccs']],
], 'submenuOptions' => ['class' => 'nav-sub'], 'visible' => ehAlunoDeEspecializacao()];
$menuPaginas[] = array('label' => 'Meu perfil', 'url' => array('/aluno/perfil'),    'visible' => temPapel('Aluno') || temPapel('Inscrito'));

// Funcionalidades dos professores e tutores
$menuPaginas[] = ['label' => 'Ver ofertas', 'url' => ['/docente/verOfertas'], 'visible' => temPapel(Constantes::PAPEL_PROFESSOR) || temPapel(Constantes::PAPEL_TUTOR)];

// Funcionalidades dos orientadores
$menuPaginas[] = ['label' => 'Avaliar TCC', 'url' => ['/orientador'], 'items' => [
    ['label' => 'Pendentes', 'url' => ['/orientador/pendentes']],
    ['label' => 'Avaliados', 'url' => ['/orientador/avaliados']],
], 'submenuOptions' => ['class' => 'nav-sub'], 'visible' => temPapel('Orientador')];

$menuPaginas[] = ['label' => 'Meu perfil', 'url' => ['/colaborador/perfil'], 'visible' => temPapel(Constantes::PAPEL_COLABORADOR)];

?>
<section id="navigation-main">
    <div class="navbar" style="margin-bottom: 0">
        <div class="navbar-inner" style="padding: 0">
            <div class="container" style="padding: 0">
                <div class="nav">
                    <?php
                    $this->widget('zii.widgets.CMenu', array(
                        'htmlOptions' => array('class' => 'nav'),
                        'submenuHtmlOptions' => array('class' => 'dropdown-menu'),
                        'itemCssClass' => 'item-test',
                        'encodeLabel' => false,
                        'items' => $menuPaginas,
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</section><!-- /#navigation-main -->
