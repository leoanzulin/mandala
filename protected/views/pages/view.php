<?php
/* @var $this PagesController */
/* @var $model Pages */

//$this->breadcrumbs=array(
//	'Pages'=>array('index'),
//	$model->page_id,
//);

?>

<div class="view">
    <h1><?php echo $model->page_name; ?></h1>
    <?php echo $model->page_description; ?>
</div>
<br>