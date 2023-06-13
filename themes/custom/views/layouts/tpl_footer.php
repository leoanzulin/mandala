<section id="bottom">
    <div class="container bottom"> 
        <div class="row-fluid ">
		<div class="span3">
                	<h5>Realização</h5>
                    <p><a href="http://www.grupohorizonte.ufscar.br/"><img src="<?php echo $baseURL; ?>/images/logo_horizontes.png" style="width: 80%"></a></p>
                    <p><a href="http://edutec.ead.ufscar.br/"><img src="<?php echo $baseURL; ?>/images/logo_edutec_menor.png" style="width: 80%"></a></p>
                </div>
            	<div class="span3">
                	<h5>Apoio</h5>
                	<p><a href="http://www.sead.ufscar.br/"><img src="<?php echo $baseURL; ?>/images/branco_h.png" style="width: 50%;"></a></p>
                	<br>
                	<p><a href="http://www.ufscar.br/"><img src="<?php echo $baseURL; ?>/images/ufscar.png" style="width: 50%;"></a></p>
            	</div>
            <?php
            $acesso[] = array('label' => 'Acessar Sistema', 'url' => array('/site/login'), 'visible' => Yii::app()->user->isGuest, 'linkOptions');
            $acesso[] = array('label' => 'Sair', 'url' => array('/site/logout'), 'visible' => !Yii::app()->user->isGuest, 'linkOptions');
            ?>

            <div class="span4">
                <h5>Acesso</h5>
                <p><?php
                    $this->widget('zii.widgets.CMenu', array(
                        'htmlOptions' => array('class' => 'sitemap'),
                        'itemCssClass' => 'item-test',
                        'encodeLabel' => false,
                        'items' => $acesso));
                    ?></p>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="footer">
        <div class="container">
            &copy; <?php echo date('Y'); ?> Educação e Tecnologias <span style="font-size: 0.5em">- v<?php echo Constantes::VERSAO; ?></span>
            <!--        	Copyright &copy; 2012. Designed by webapplicationthemes.com - High quality HTML Theme-->
        </div>
    </div>
</footer>

</body>
</html>
