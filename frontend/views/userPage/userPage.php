<?php
$this->layout = '//layouts/column1';
Yii::app()->bootstrap->registerAssetCss('bootstrap-box.css');
Yii::app()->clientScript->registerCssFile(
	Yii::app()->assetManager->publish(Yii::getPathOfAlias('application.views.userPage.assets') . '/styles.css')
);
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
?>

<div class="row-fluid">
    <div class="span10 offset1">
	
	<div class="bootstrap-widget" id="title">
	    <div class="bootstrap-widget-header smooth">
		<i class="icon-user"></i><h3>Jhon Lenon</h3>
		<h3 class="pull-right">Online</h3>
	    </div>
	</div>
	
	<div class="row-fluid">
	    
	    <div class="span3" id="column-left">
		
		<div id="photo">
		    <img src="http://placehold.it/237x300" />
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
		
		<div id="user-info" class="row-fluid">
		    <div class="span12">
		    
			<h5 data-toggle="#personal-info"><?= Yii::t('userPage', 'Personal info'); ?>&nbsp;<small><a href="#"><?= Yii::t('userPage', 'Change'); ?></a></small></h5>

			<?php $this->widget('bootstrap.widgets.TbDetailView', array(
			    'htmlOptions' => array(
				'id' => 'personal-info'
			    ),
			    'data'=>array('id'=>1, 'firstName'=>'Mark', 'lastName'=>'Otto', 'language'=>'CSS'),
			    'attributes'=>array(
				    array('name'=>'firstName', 'label'=>'First name'),
				    array('name'=>'lastName', 'label'=>'Last name'),
				    array('name'=>'language', 'label'=>'Language'),
			    ),
			)); ?>

			<h5 data-toggle="#contacts"><?= Yii::t('userPage', 'Contacts'); ?>&nbsp;<small><a href="#"><?= Yii::t('userPage', 'Change'); ?></a></small></h5>

			<?php $this->widget('bootstrap.widgets.TbDetailView', array(
			    'htmlOptions' => array(
				'id' => 'contacts'
			    ),
			    'data'=>array('id'=>1, 'firstName'=>'Mark', 'lastName'=>'Otto', 'language'=>'CSS'),
			    'attributes'=>array(
				    array('name'=>'firstName', 'label'=>'First name'),
				    array('name'=>'lastName', 'label'=>'Last name'),
				    array('name'=>'language', 'label'=>'Language'),
			    ),
			)); ?>
			
		    </div>
		</div>
		
		<div id="posts row-fluid">
		    <div class="span12">
			
			<div class="bootstrap-widget" id="title">
			    <div class="bootstrap-widget-header smooth">
				<h3><?= Yii::t('userPage', '{count} records', array('{count}'=>150)); ?></h3>
				<h3 class="pull-right"><small><a href="#"><?= Yii::t('userPage', 'Show users\' records only');?></a></small></h3>
			    </div>
			</div>		    

			<div class="new-post row-fluid">
			    <div class="well span12">
				<input type="text" name="new-post" placeholder="<?= Yii::t('userPage', 'What\'s new?');?>" value="" class="span12">
				<div class="controls">
				    <button class="btn btn-primary pull-right"><?= Yii::t('userPage', 'Post'); ?></button>
				</div>
			    </div>
			</div>

			<div class="records row-fluid">

			    <div class="media post">
				<a class="pull-left" href="#">
				    <img class="media-object" src="http://placehold.it/64x64">
				</a>
				<div class="media-body">
				    
				    <h5 class="media-heading">
					<span class="user">Jhon Lenon</span> 
					<small><a href="#">8 August, 2013</a></small> 
					<span class="controls pull-right">
					    <i class="icon-pencil"></i>&nbsp;
					    <i class="icon-remove"></i>
					</span>
				    </h5>

				    <div class="post-content">
					Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
				    </div>
				    
				    <div class="post-after">
					<div class="post-rate pull-right">
					    <span class="icon-thumbs-up">162</span>
					    <span class="icon-thumbs-down">38</span>
					</div>
				    </div>
				    
				    <div class="comments"></div>
				</div>
			    </div>

			    <div class="media post">
				<a class="pull-left" href="#">
				    <img class="media-object" src="http://placehold.it/64x64">
				</a>
				<div class="media-body">
				    
				    <h5 class="media-heading">
					<span class="user">Jhon Lenon</span> 
					<small><a href="#">5 August, 2013</a></small> 
					<span class="controls pull-right">
					    <i class="icon-pencil"></i>&nbsp;
					    <i class="icon-remove"></i>
					</span>
				    </h5>

				    <div class="post-content">
					Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
				    </div>
				    
				    <div class="post-after">
					<div class="post-rate pull-right">
					    <span class="icon-thumbs-up">2</span>
					    <span class="icon-thumbs-down">13</span>
					</div>
				    </div>
				    
				    <div class="comments">
					
					<div class="media post">
					    <a class="pull-left" href="#">
						<img class="media-object" src="http://placehold.it/64x64">
					    </a>
					    <div class="media-body">

						<h5 class="media-heading">
						    <span class="user">Jhon Lenon</span> 
						    <small><a href="#">8 August, 2013</a></small> 
						    <span class="controls pull-right">
							<i class="icon-pencil"></i>&nbsp;
							<i class="icon-remove"></i>
						    </span>
						</h5>

						<div class="post-content">
						    Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin commodo. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
						</div>

						<div class="post-after">
						    <div class="post-rate pull-right">
							<span class="icon-thumbs-up">162</span>
							<span class="icon-thumbs-down">38</span>
						    </div>
						</div>
						
						<div class="comments"></div>
					    </div>
					</div>
				    </div>
				</div>
			    </div>

			    <div class="row-fluid get-more">
				<div class="span12"><a href="#">More</a></div>
			    </div>
			</div><!-- .records -->

		    </div>
		</div>
	    </div><!-- #column-right -->
	    
	</div>
    </div>
</div>