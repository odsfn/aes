<?php 
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
$this->beginContent('//layouts/column1');

Yii::app()->bootstrap->registerAssetCss('bootstrap-box.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/user.css');

$username = Yii::app()->user->username;
$userPageUrl = '/userPage';

$this->breadcrumbs->add($username, $userPageUrl);
?>

<div class="row-fluid">
    <div class="span10 offset1">
	
	<div class="bootstrap-widget" id="title">
	    <div class="bootstrap-widget-header smooth">
		<i class="icon-user"></i>
                <h3>
                    <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                            'homeLink' => false,
                            'links'=> $this->breadcrumbs->breadcrumbs 
                    )); ?>
                </h3>
		<h3 class="pull-right">Online</h3>
	    </div>
	</div>
	
	<div class="row-fluid">
	    
	    <div class="span3" id="column-left">
		
		<div id="photo">
		    <img src="<?php echo $this->profile->getPhoto(237, 300); ?>" />
		</div>
		
		<div id="navigation">
		    <?php
                    $this->widget('bootstrap.widgets.TbMenu', array(
                        'type'=>'pills',
                        'stacked' => 'true',
                        'items' => array(
                            array('label'=> Yii::t('userPage', 'My page'), 'url'=> array('userPage/index')),
                            array('label'=> Yii::t('userPage', 'My messages'), 'url'=> array('userPage/messages')),
                            array('label'=> Yii::t('userPage', 'My votes'), 'url'=> array('userPage/votes')),
                            array('label'=> Yii::t('userPage', 'My nominations'), 'url'=> array('userPage/nominations')),
                            array('label'=> Yii::t('userPage', 'My mandates'), 'url'=> array('userPage/mandates')),
                            array('label'=> Yii::t('userPage', 'My photos'), 'url'=> array('userPage/photos')),
                            array('label'=> Yii::t('userPage', 'My videos'), 'url'=> array('userPage/videos')),
                        )
                    ));
		    ?>
		</div>
		
	    </div><!-- #column-left -->
	    
	    <div class="span9" id="column-right">
		<?php echo $content; ?>
	    </div><!-- #column-right -->
	    
	</div>
    </div>
</div>
<?php $this->endContent(); ?>