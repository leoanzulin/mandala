<?php

function temPapel($papel)
{
    return !ehSuperUsuario() && Yii::app()->user->checkAccess($papel);
}

function ehSuperUsuario()
{
    return Yii::app()->user->isSuperuser === true;
}

function ehVisitante()
{
    return Yii::app()->user->isGuest;
}

// Recupera as páginas personalizadas
$criteria = new CDbCriteria(array('order' => 'page_position asc'));
$menuPaginas = array();

//$menuPaginas[] = array('label' => 'Fazer pré-inscrição', 'url' => array('/inscricao'), 'visible' => ehVisitante());
//foreach (Pages::model()->findAll($criteria) as $pagina) {
    //$menuPaginas[] = array('label' => $pagina->page_shortname, 'url' => array('/pages/view&id=' . $pagina->page_id), 'visible' => ehVisitante());
//}

// Funcionalidades do administrador
//$menuPaginas[] = array('label' => 'Páginas', 'url' => array('/pages/administrar'), 'items' => array(
//    array('label' => 'Criar nova página', 'url'=>array('/pages/create')),
//    array('label' => 'Editar páginas', 'url'=>array('/pages/administrar')),
//), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Inscrições', 'url' => array('/admin/gerenciarPreInscricoes'), 'items' => array(
    array('label' => 'Gerenciar Inscrições', 'url'=>array('/admin/gerenciarPreInscricoes')),
    array('label' => 'Visualizar pré-inscrições em componentes (simulador)', 'url'=>array('/admin/visualizarPreInscricoesEmComponentes')),
    array('label' => 'Visualizar inscrições em ofertas', 'url'=>array('/admin/visualizarInscricoesEmOfertas')),
    array('label' => 'Visualizar alunos inscritos', 'url'=>array('/admin/visualizarAlunosInscritos')),
    array('label' => 'Visualizar alunos matriculados', 'url'=>array('/admin/visualizarAlunosMatriculados')),
), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Componentes', 'url' => array('/admin/gerenciarOfertas'), 'items' => array(
    array('label' => 'Gerenciar ofertas', 'url'=>array('/admin/gerenciarOfertas')),
    array('label' => 'Visualizar ofertas', 'url'=>array('/admin/visualizarOfertas')),
    array('label' => 'Gerenciar componentes', 'url'=>array('/admin/gerenciarComponentes')),
), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Gerenciar notas', 'url' => array('/admin/gerenciarNotas'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Docentes', 'url' => array('/admin/gerenciarDocentes'), 'items' => array(
    array('label' => 'Cadastrar novo docente', 'url'=>array('/admin/cadastrarDocente')),
    array('label' => 'Gerenciar docentes', 'url'=>array('/admin/gerenciarDocentes')),
), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Tutores', 'url' => array('/admin/gerenciarTutores'), 'items' => array(
    array('label' => 'Cadastrar novo tutor', 'url'=>array('/admin/cadastrarTutor')),
    array('label' => 'Gerenciar tutores', 'url'=>array('/admin/gerenciarTutores')),
), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Pagamentos', 'url' => array('/pagamento'), 'items' => array(
    array('label' => 'Pagamentos de bolsas', 'url'=>array('/pagamento/bolsas')),
    array('label' => 'Saldo das bolsas', 'url'=>array('/pagamento/saldoBolsas')),
    array('label' => 'Pagamentos de alunos', 'url'=>array('/pagamento/alunos')),
), 'submenuOptions' => array('class' => 'nav-sub'), 'visible' => ehSuperUsuario());

$menuPaginas[] = array('label' => 'Relatórios', 'url' => array('/admin/relatorios'), 'visible' => ehSuperUsuario());
$menuPaginas[] = array('label' => 'Configurações', 'url' => array('/admin/configuracoes'), 'visible' => ehSuperUsuario());
//$menuPaginas[] = array('label' => 'Rights', 'url' => array('/rights'),                                     'visible' => ehSuperUsuario());

// Funcionalidades dos alunos
$menuPaginas[] = array('label' => 'Fazer inscrição em componentes', 'url' => array('/aluno/inscricao'), 'visible' => temPapel('Aluno'));
$menuPaginas[] = array('label' => 'Simulador',                      'url' => array('/aluno/simulador'), 'visible' => temPapel('Aluno'));
$menuPaginas[] = array('label' => 'Meu perfil',                     'url' => array('/aluno/perfil'),    'visible' => temPapel('Aluno') || temPapel('Inscrito'));

//$menuPaginas[] = array('label' => 'Login', 'url' => array('/site/login'), 'visible' => ehVisitante());  // , 'linkOptions' => array("data-description" => "member area")
//$menuPaginas[] = array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/site/logout'), 'visible' => !ehVisitante(), 'linkOptions' => array("data-description" => "member area"));
?>
<section id="navigation-main">
    <div class="navbar" style="margin-bottom: 0">
        <div class="navbar-inner" style="padding: 0">
            <div class="container" style="padding: 0">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <div class="nav-collapse">
                    <?php
                    $this->widget('zii.widgets.CMenu', array(
                        'htmlOptions' => array('class' => 'nav'),
                        'submenuHtmlOptions' => array('class' => 'dropdown-menu'),
                        'itemCssClass' => 'item-test',
                        'encodeLabel' => false,
                        'items' => $menuPaginas,
//                        'firstItemCssClass' => 'menu-inscricao',
                    ));
//                    $this->widget('application.extensions.mbmenu.MbMenu', array(
//                        'htmlOptions' => array('class' => 'nav'),
//                        'items' => array(
//                            array('label' => 'Home', 'url' => array('uou'),
//                                'items' => array(
//                                    array('label' => 'sub1'),
//                                    array('label' => 'sub2'),
//                                ),
//                            ),
//                        ),
//                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</section><!-- /#navigation-main -->
