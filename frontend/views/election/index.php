<?php $this->widget('application.widgets.ClientApp', array(
    'appName' => 'elections',
    'requires' => array(
        'js' => array(
            'aes:collections/FeedCollection.js',
        )
    )
));
?>

<div id="elections row-fluid">

    <div class="span12" id="posts-app-container">
        <div id="election_summary" class="summary"><?php echo Yii::t('aes','Found'); ?> <span id="total_elections">...</span> <?php echo Yii::t('aes','elections'); ?><a id="a_create_election" href="/election/create">create new</a></div>
        <div id="election_form"></div>
        <div id="election_list"></div>
        <div id="election_more"></div>
    </div>
    <div class="clearfix"></div>
</div>

    <script type="text/template" id="election-view">

        <a href="/election/view/<%= id %>"><%= name %></a><br>
        <span class="muted"><?php echo Yii::t('aes','Status'); ?>: </span><strong><%= text_status %></strong><br><br>

    </script>

    <script type="text/template" id="no-election-view">

        <p><?php echo Yii::t('aes','no election found'); ?></p>

    </script>

    <script type="text/template" id="search-view">

        <div class="input-prepend">
            <span class="add-on"><i class="icon-search"></i></span>
            <input type="text" class="span4" value="" maxlength="20" placeholder="<?php echo Yii::t('aes', 'Start typing part of the election&apos;s title'); ?>" name="elect_search" id="elect_search">
        </div>

        <?php echo CHtml::dropDownList('elect_status', '', array_merge( array(''=>Yii::t('aes','All')), AESHelper::arrTranslated(Election::$statuses)), array('id'=>'elect_status','class'=>'span2')); ?>

    </script>

    <script type="text/template" id="more-btn-tpl">
        <div class="row-fluid get-more">
            <div class="span12"><a id="a_more" href="#"><?php echo Yii::t('aes','More'); ?></a><span>Loading...</span></div>
        </div>
    </script>
