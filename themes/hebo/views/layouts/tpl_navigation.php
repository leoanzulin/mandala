<?php
// Recupera as páginas personalizadas

$criteria = new CDbCriteria(array('order' => 'page_position asc'));
$menuPaginas = array();
foreach (Pages::model()->findAll($criteria) as $pagina) {
    $menuPaginas[] = array('label' => $pagina->page_name, 'url' => array('/pages/view&id=' . $pagina->page_id), 'visible' => Yii::app()->user->isGuest);
}
$menuPaginas[] = array('label' => 'Fazer inscrição', 'url' => array('/inscricao'), 'visible' => Yii::app()->user->isGuest);
$menuPaginas[] = array('label' => 'Gerenciar inscrições em cursos', 'url' => array('/enrollment/manage'), 'visible' => Yii::app()->user->checkAccess('Coordenador'));
$menuPaginas[] = array('label' => 'Cursos', 'url' => array('/course'), 'visible' => Yii::app()->user->checkAccess('Coordenador'));
$menuPaginas[] = array('label' => 'Usuários', 'url' => array('/users'), 'visible' => Yii::app()->user->checkAccess('Coordenador'));
$menuPaginas[] = array('label' => 'Rights', 'url' => array('/rights'), 'visible' => Yii::app()->user->isSuperuser === true);
$menuPaginas[] = array('label' => 'Login', 'url' => array('/site/login'), 'visible' => Yii::app()->user->isGuest);  // , 'linkOptions' => array("data-description" => "member area")
$menuPaginas[] = array('label' => 'Logout (' . Yii::app()->user->name . ')', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest, 'linkOptions' => array("data-description" => "member area"));
?>
<section id="navigation-main">  
    <div class="navbar">
        <div class="navbar-inner">
            <div class="container">
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
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</section><!-- /#navigation-main -->
