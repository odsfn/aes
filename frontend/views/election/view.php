<?php
$this->breadcrumbs = array ('Elections'=>'/election', CHtml::encode($model->name));
?>

<div class="row-fluid">
    <div id="comments-box"></div>
    <?php
    $this->widget('CommentsMarionetteWidget', array(
        'jsConstructorOptions' => array(
            'targetId' => $model->id,
            'targetType' => 'Election',
            'limit' => 10,
            'title' => true,
        ),
        //'templates'=>array('commentView' => '#election-comment-tpl'),
        //'show' => array('div', array('class'=>'span4')),
        'show' => array('el' => '#comments-box'),
        //'show' => array('el' => '#election-comment-tpl'),
    ));
    ?>
</div>




<?php /*$this->widget('application.widgets.ClientApp', array(
    'appName' => 'posts',
    'requires' => array(
        'depends' => array('loadmask'),
        'js' => array(
            'models/Post.js',
            'models/PostRate.js',
            'aes:collections/FeedCollection.js',
            'collections/Posts.js',
            'collections/Comments.js',
            'views/PostsTitleView.js',
            'views/EditBoxView.js',
            'views/PostsView.js',
            'views/CommentsView.js',
            'views/EditableView.js',
            'views/MoreView.js',
            'modules/Feed.js'
        )
    )
));*/

/*Yii::app()->clientScript->registerScript('setUsersPageId',
    'PostsApp.on("initialize:before", function() { PostsApp.pageUserId = ' . $this->profile->user_id . '; });',
    CClientScript::POS_HEAD
);

// @TODO: Replace this by setting system property
if(defined('TEST_APP_INSTANCE') && TEST_APP_INSTANCE) {
    Yii::app()->clientScript->registerScript('setPostsLimitTest',
        'PostsApp.on("initialize:before", function() { PostsApp.Feed.posts.limit = 3; });',
        CClientScript::POS_HEAD
    );
}*/

?>

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
        <div class="img-wrapper-tocenter users-photo users-photo-<%= user_id %>">
            <span></span>
            <a href="<%= user.pageUrl %>" name="<%= id %>"><img alt="<%= user.displayName %>" src="<%= user.photoThmbnl64 %>"></a>
        </div>
    </div>
    <div class="media-body">
        <div class="post-body">
            <h5 class="media-heading">
                <span class="user"><%= user.displayName %></span>
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



<script type="text/template" id="election-comment-tpl">
    <div class="pull-left">
        <div class="img-wrapper-tocenter users-photo users-photo-<%= user_id %>">
            <span></span>
            <a href="<%= user.pageUrl %>" name="<%= id %>"><img alt="<%= user.displayName %>" src="<%= user.photoThmbnl64 %>"></a>
        </div>
    </div>
    <div class="media-body">
        <div class="post-body">
            <h5 class="media-heading">
                <span class="user"><%= user.displayName %></span>
                <small><a href="#<%= id %>"><%= i18n.date(created_ts, 'full', 'full') %></a></small>
            </h5>

            <div class="post-content">
                    <%= content %>
                </div>

            <div class="post-after">
                <div class="post-rate pull-right"></div>
            </div>
        </div>

        <div class="comments"></div>
    </div>
</script>