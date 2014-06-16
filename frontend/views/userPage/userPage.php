	
		<div id="user-info" class="row-fluid">
		    <div class="span12">
		    
			<h5 data-toggle="#personal-info"><?= Yii::t('userPage', 'Personal info'); ?>
                            <?php if($this->self): ?>
                            &nbsp;<small><a href="<?= Yii::app()->createUrl('/userAccount/profile/edit', array('#'=>'personal-info')); ?>"><?= Yii::t('userPage', 'Change'); ?></a></small>
                            <?php endif; ?>
                        </h5>

			<?php $this->widget('bootstrap.widgets.TbDetailView', array(
			    'htmlOptions' => array(
				'id' => 'personal-info'
			    ),
			    'type'  => 'condensed',
			    'data'  => $profile,
			    'attributes'=>array(
				'first_name', 'last_name', 'birth_place', 'birthDayFormated',
				'displayGender'
			    )
			)); ?>

			<h5 data-toggle="#contacts"><?= Yii::t('userPage', 'Contacts'); ?>
                            <?php if($this->self): ?>
                            &nbsp;<small><a href="<?= Yii::app()->createUrl('/userAccount/profile/edit', array('#'=>'contacts')); ?>"><?= Yii::t('userPage', 'Change'); ?></a></small>
                            <?php endif; ?>
                        </h5>

			<?php $this->widget('bootstrap.widgets.TbDetailView', array(
			    'htmlOptions' => array(
				'id' => 'contacts'
			    ),
			    'type'  => 'condensed',
			    'data'=>$profile,
			    'attributes'=>array(
				'email', 'mobile_phone'
			    ),
			)); ?>

			<h5 data-toggle="#personIdentifier"><?= Yii::t('userPage', 'Person identifier'); ?></h5>

			<?php $this->widget('bootstrap.widgets.TbDetailView', array(
			    'htmlOptions' => array(
				'id' => 'person-identifier'
			    ),
			    'type'  => 'condensed',
			    'data'=>$profile->personIdentifier,
			    'attributes'=>  array_merge(
                                array(
                                    array(
                                        'name' => 'status',
                                        'value'=> $profile->personIdentifier->statusLabel
                                    ), 
                                    array(
                                        'name' => 'type',
                                        'value'=> $profile->personIdentifier->typeLabel
                                    ), 
                                    array(
                                        'name' => 'image',
                                        'type' => 'raw',
                                        'value'=> "<a href=\"{$profile->personIdentifier->imageUrl}\" target=\"_blank\">Open ( in new tab )</a>"
                                    ),
                                ),
                                $profile->personIdentifier->getTypeAttributeNames()
                            ),
			)); ?>                        
                        
		    </div>
		</div>
		

                <?php
                    $this->createWidget('PostsMarionetteWidget', array(
                        'jsConstructorOptions' => array(
                            'userPageId'    => $profile->user_id,
                        ),        
                        'checkForRoles' => array(
                            'postsAdmin' => function($params) {
                                return (Yii::app()->user->id && Yii::app()->user->id == $params['widget']->jsConstructorOptions['userPageId']); 
                            }
                        )
                    ))->register();
                ?>

<script type="text/javascript">
    var UserPagePosts = PostsWidget.Posts.extend({
        targetType: 'Profile',
        userPageId: null,
        url: UrlManager.createUrlCallback('api/userPagePost'),
        getFilters: function() {
            return _.extend(
                PostsWidget.Posts.prototype.getFilters.apply(this, arguments),
                { userPageId: this.userPageId }
            );
        }
    });
    
    var PostsTitleView = PostsWidget.PostsTitleView.extend({
        template: '#local-posts-title-tpl',
        
        ui: {
            authorSwitcher: 'small.author-switcher a'
        },

        events: {
            'click small.author-switcher a': 'switchAuthor'
        },
        
        switchAuthor: function() {
            this.model.set('allUsers', !this.model.get('allUsers'));

            if(!this.model.get('allUsers')) {
                this.postsCol.setFilter('usersRecordsOnly', this.postsCol.userPageId);
            }else{
                this.postsCol.setFilter('usersRecordsOnly', false);
            }
        },

        onBeforeRender: function() {
            if(this.model.get('allUsers')) {
                this.model.set('switcherText', 'Show users\' records only');
            }else{
                this.model.set('switcherText', 'Show all records');
            }
        },        
        
        initialize: function() {
            PostsWidget.PostsTitleView.prototype.initialize.apply(this, arguments);
            
            this.model.set('allUsers', true);
            this.model.set('switcherText', '');

            this.listenTo(this.model, 'change:allUsers', this.render);
        }
    });
</script>           

<?php 

$limit = (defined('TEST_APP_INSTANCE') && TEST_APP_INSTANCE) ? 3 : 20;

Yii::app()->clientScript->registerScript('initPostsWidget', "
var userPagePosts = new UserPagePosts();
userPagePosts.userPageId = {$profile->user_id};
userPagePosts.limit = $limit;

var postsTitleView = new PostsTitleView({
    postsCol: userPagePosts
});

var userPagePostsWidget = PostsWidget.create({

    targetId: {$profile->target_id},    
    targetType: 'Profile',

    postsCol: userPagePosts,
    
    limit: $limit,
        
    postsTitleView: postsTitleView
});

$('#posts').html(userPagePostsWidget.render().el);
userPagePostsWidget.triggerMethod('show');
", CClientScript::POS_READY);

?>

<script type="text/template" id="local-posts-title-tpl">
    <div class="bootstrap-widget" id="title">
        <div class="bootstrap-widget-header smooth">
            <h3 id="posts-counter-cont"><span class="posts-count"><%= count %></span> records</h3>
            <h3 class="pull-right"><small class="author-switcher"><a href="#"><%= t(switcherText) %></a></small></h3>
        </div>
    </div>
</script>

<div id="posts" class="row-fluid"></div>