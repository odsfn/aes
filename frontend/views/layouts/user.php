<?php 
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
$this->beginContent('//layouts/column1');
Yii::app()->bootstrap->registerAssetCss('bootstrap-box.css');
Yii::app()->clientScript->registerCssFile(
	Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.views.userPage.assets') . '/styles.css')
);
?>

<div class="row-fluid">
    <div class="span10 offset1">
	
	<div class="bootstrap-widget" id="title">
	    <div class="bootstrap-widget-header smooth">
		<i class="icon-user"></i><h3><?= Yii::app()->user->username; ?></h3>
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
			$this->widget('bootstrap.widgets.TbTabs', array(
				'type'=>'pills',
				'stacked'=>true,
				'tabs'=>array(
					array('label'=> Yii::t('userPage', 'My page'), 'active'=>true),
					array('label'=> Yii::t('userPage', 'My messages'), 'url' => '#'),
					array('label'=> Yii::t('userPage', 'My votes')),
					array('label'=> Yii::t('userPage', 'My nominations')),
					array('label'=> Yii::t('userPage', 'My mandates')),
					array('label'=> Yii::t('userPage', 'My photos')),
					array('label'=> Yii::t('userPage', 'My videos')),
				),
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