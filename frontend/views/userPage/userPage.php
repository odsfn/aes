<?php 
    $this->layout = '//layouts/user';
?>		
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
			
		    </div>
		</div>
		

                <?php $this->widget('application.widgets.ClientApp', array(
                    'isolated' => true,
                    'appName' => 'posts',
                    'requires' => array(
                        'js' => array(
                            'models/Post.js',
                            'collections/FeedCollection.js',
                            'collections/Posts.js',
                            'collections/Comments.js',
                            'views/PostsTitleView.js',
                            'views/EditBoxView.js',
                            'views/PostsView.js',
                            'views/CommentsView.js',
                            'views/EditableView.js',
                            'views/MoreView.js'
                         )
                    ),
                    'initializers' => array(
                        'this.pageUserId = ' . $this->profile->user_id . ';'
                    )
                )); ?>

		<div id="posts row-fluid">
		    <div class="span12" id="posts-app-container">
                        
                        <div id="feed-title"></div>

			<div id="add-post-top"></div>
                        
			<div class="records row-fluid" id="posts-feed"></div>

                        <div id="posts-load-btn"></div>
		    </div>
		</div>

<script type="text/template" id="posts-title-tpl">
    <div class="bootstrap-widget" id="title">
        <div class="bootstrap-widget-header smooth">
            <h3 id="posts-counter-cont"><span class="posts-count"><%= count %></span> records</h3>
            <h3 class="pull-right"><small class="author-switcher"><a href="#"><%= t(switcherText) %></a></small></h3>
        </div>
    </div>
</script>

<script type="text/template" id="edit-box-tpl">
    <div class="new-post row-fluid">
        <div class="body span12">
            <input type="text" name="new-post" placeholder="<%= t(view.placeholderText) %>" value="" class="span12">
            <textarea class="span12"><%= content %></textarea>
            <div class="controls pull-right">
                <button type="button" class="btn btn-primary post" data-loading-text="<%= t("Saving...") %>"><%= t(view.buttonText) %></button>
                <button type="button" class="btn cancel"><%= t("Cancel") %></button>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="post-tpl">
        <div class="pull-left">
            <div class="img-wrapper-tocenter users-photo users-photo-1">
                <span></span>
                <img alt="<%= authorDisplayName %>" src="<?php echo ($this->profile) ? $this->profile->photoThmbnl64 : ''; // Will be replaced soon to the <%= authorPhotoThumbnail %> ?>">
            </div>
        </div>
        <div class="media-body">
            <div class="post-body">
                <h5 class="media-heading">
                    <span class="user"><%= authorDisplayName %></span> 
                    <small><a href="#<%= id %>"><%= displayTime %></a></small> 
                </h5>

                <div class="post-content">
                    <%= content %>
                </div>

                <div class="post-after">
                    <div class="post-rate pull-right">
                        <span class="icon-thumbs-up"><%= likes %></span>
                        <span class="icon-thumbs-down"><%= dislikes %></span>
                    </div>
                </div>
            </div>
            
            <div class="comments"></div>
        </div>
</script>

<script type="text/template" id="editable-tpl">
        <i class="icon-pencil"></i>&nbsp;
        <i class="icon-remove"></i>
</script>

<script type="text/template" id="comments-tpl">
    <div class="comments-feed"></div>
    <div class="comment-to-comment">
    </div>
</script>

<script type="text/template" id="more-btn-tpl">
    <div class="row-fluid get-more">
	<div class="span12"><a><%= t(view.moreMsg) %></a><span>Loading...</span></div>
    </div>
</script>