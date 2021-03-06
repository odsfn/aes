<?php 
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
$this->beginContent('//layouts/column1');

Yii::app()->bootstrap->registerAssetCss('bootstrap-box.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/layouts/core.css');

?>

<div class="row-fluid">
    <div class="span10 offset1">
	<div class="bootstrap-widget" id="title">
	    <div class="bootstrap-widget-header smooth">
                <?php if(!empty($this->clips['titleIcon'])) 
                    echo $this->clips['titleIcon']; 
                ?>
                <h3>
                    <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                            'homeLink' => false,
                            'links'=> is_array($this->breadcrumbs) ? $this->breadcrumbs : $this->breadcrumbs->breadcrumbs 
                    )); ?>
                </h3>
                <?php if(!empty($this->clips['titleAfterBredcrumbsContent']))
                    echo $this->clips['titleAfterBredcrumbsContent'];
                ?>
	    </div>
	</div>
	
	<div class="row-fluid">
	    
	    <div class="span12" id="column-main">
		<?php echo $content; ?>
	    </div><!-- #column-right -->
	    
	</div>
    </div>
</div>
<?php $this->endContent(); ?>