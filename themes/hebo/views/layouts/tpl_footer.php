<section id="bottom" class="">
    <div class="container bottom"> 
    	<div class="row-fluid ">
            <div class="span4">
            	<h5>Apoio: </h5>
                <p><img src="<?php echo $baseURL;?>/images/branco_h.png"> <img src="<?php echo $baseURL;?>/images/ufscar.png"></p>
                
                <p></p>
                
            </div><!-- /span3-->
            
            <?php
            foreach (Pages::model()->findAll($criteria) as $pagina)
                    $menuPaginas1[] = array('label' => $pagina->page_name, 'url' => array('/paginas/view&id='.$pagina->page_id), 'visible'=>Yii::app()->user->isGuest,'linkOptions');
                    $acesso[] = array('label'=>'Acessar Sistema', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest,'linkOptions');
                    $acesso[] = array('label'=>'Sair ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest,'linkOptions');
            ?>
            <div class="span3">
            	<h5>Mapa do site</h5>
                <?php $this->widget('zii.widgets.CMenu',array(
                    'htmlOptions'=>array('class'=>'sitemap'),
                    'itemCssClass'=>'item-test',
                    'encodeLabel'=>false,
                            
                    'items'=>$menuPaginas1,
                   
                )); ?>
            	
            </div><!-- /span3-->
            
            <div class="span3">
            	<h5>Contato</h5>
                <p>
                    Secretaria geral de Educação a Distância<br/>
                    Universidade Federal de São Carlos<br/>
                    Telefone: (16)3351-9586<br/>
                    E-mail: sead@ufscar.br<br/>
                
                </p>
                <br>
                <h5>Acesso</h5>
                    <p><?php $this->widget('zii.widgets.CMenu',array(
                    'htmlOptions'=>array('class'=>'sitemap'),
                    'itemCssClass'=>'item-test',
                    'encodeLabel'=>false,
                            
                    'items'=>$acesso)); ?></p>
            </div><!-- /span3-->
        </div><!-- /row-fluid -->
        </div><!-- /container-->
</section><!-- /bottom-->

<footer>
    <div class="footer">
        <div class="container">
        	Copyright &copy; 2012. Designed by webapplicationthemes.com - High quality HTML Theme
        </div>
	</div>
</footer>

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-transition.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-alert.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-modal.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-dropdown.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-scrollspy.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-tab.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-tooltip.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-popover.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-button.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-collapse.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-carousel.js"></script>
    <script src="<?php echo Yii::app()->theme->baseUrl;?>/js/bootstrap-typeahead.js"></script>   
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>


  </body>
</html>