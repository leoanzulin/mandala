<?php
/* @var $this PagesController */
/* @var $model Pages */
?>

<h1>Editar páginas</h1>

<ul>
<?php
    $paginas = Pages::model()->findAll(array('order'=>'page_id'));
    foreach ($paginas as $pagina) {
        echo '    <li>' . CHtml::link($pagina->page_name, array('pages/update', 'id' => $pagina->page_id)) . '</li>' . "\n";
    }
?>

</ul>