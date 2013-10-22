<?php 
$this->layout = '//layouts/main';

$this->widget('application.widgets.MarionetteWidget', array(
    'widgetName' => 'CommentsWidget',
    'basePath' => 'frontend.views.sandbox.commentsWidget',
    'requires' => array(
        'depends' => array('loadmask'),
        'js' => array(
            'EditBoxView.js',
            'EditableView.js',
            'aes:collections/FeedCollection.js',
            'aes:views/CommentsWidget.js'
         )
    )
));

Yii::app()->clientScript->registerCss('commentsWidget', "

.post {
    padding-left: 5px;
    padding-top: 5px;
}

.post .img-wrapper-tocenter {
    width: 64px; 
    height: 64px;
    box-shadow: 1px 1px 5px 0px rgb(201, 201, 201);
}

.post .post-content {
    min-height: 40px;
}

.post .post-rate, .post .media-heading .controls {
    margin-right: 7px;
}

.post .post-rate span {
    margin-left: 7px;
}

.post .media-body .post-after {
    height: 20px;
}

.post .controls i {
    cursor: pointer;
}

.post .controls {
    display: none;
}

.post .post-rate {
    opacity: 0.25;
}

.post .post-body.hovered .controls{
    display: inline;
}

.post .post-body.hovered .post-rate{
    opacity: 1;
}

.post-body.hovered .post-rate span {
    cursor: pointer;
}

.post-rate span.chosen {
    font-weight: bold;
}

.post .comments > .media:first-child, 
.post .comments .new-post {
    margin-top: 15px;
}

.new-post .controls {
    display: none;
}

.new-post > .body > textarea {
    display: none;
    min-height: 100px;
}

");

Yii::app()->clientScript->registerScript('initCommentsWidget', "
var widget = CommentsWidget.create({
    targetId: 3,
    targetType: 'Election',
    webUser: webUser,
    autoFetch: false
});

$('#comments-box').html(widget.render().el);
widget.triggerMethod('show');
", CClientScript::POS_READY);

?>
<h1>Hello from sandbox!</h1>

<i>Empty comments</i>

<p>Lorem ipsum dolor. And comments to it below.</p>

<div class="row-fluid">
    <div id="comments-box" class="span4"></div>
</div>

<hr>

<i>With initial set of models</i>

<p>Lorem ipsum dolor. And comments to it below.</p>

<div class="row-fluid">
    <div id="comments-box-with-data-set" class="span4"></div>
</div>

<?php 

Yii::app()->clientScript->registerScript('initCommentsWidgetWithDataSet', "
var widgetWithDataSet = CommentsWidget.create({

    targetId: 2,
    
    targetType: 'Election',
    
    initData: {
    
        totalCount: 2,
        models: [
        
                {
                    id: _.uniqueId(),
                    user_id: 2,
                    user: {
                        displayName: 'Another User',
                        photo: \"http://placehold.it/64x64\"
                    },
                    content: \"Lorem ipsum dolor sit amet, at debet dolores est.\",
                    created_ts: 1376577788,                
                },
                
                {
                    id: _.uniqueId(),
                    user_id: 1,
                    user: {
                        displayName: 'Vasiliy Pedak',
                        photo: \"http://placehold.it/64x64\"
                    },
                    content: \"At debet dolores est. Lorem ipsum dolor sit amet\",
                    created_ts: 1376577889,                   
                }        
        ]
    },
    
    webUser: webUser
});

$('#comments-box-with-data-set').html(widgetWithDataSet.render().el);
widgetWithDataSet.triggerMethod('show');
", CClientScript::POS_READY);

?>

<hr>

<i>With fetching set of models</i>

<p>Lorem ipsum dolor. And comments to it below.</p>

<div class="row-fluid">
    <div id="comments-box-fetching" class="span4"></div>
</div>

<?php 

Yii::app()->clientScript->registerScript('initCommentsWidgetFetching', "
var widgetFetching = CommentsWidget.create({

    targetId: 1,
    
    targetType: 'Election',
    
    webUser: webUser
});

$('#comments-box-fetching').html(widgetFetching.render().el);
widgetFetching.triggerMethod('show');
", CClientScript::POS_READY);

?>
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
                    <small><a href="#<%= id %>"><%= i18n.date(created_ts, 'full', 'full') %></a></small> 
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