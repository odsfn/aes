<?php $this->widget('application.widgets.ClientApp', array(
    'appName' => 'elections',
    'requires' => array(
        'js' => array(
            'aes:collections/FilterableCollection.js',
            'aes:collections/FeedCollection.js',
            'aes:models/Election.js'
        )
    )
));
?>

<div id="elections">


    <div id="column-left" class="span4">
        <div id="election_form" class="well form-vertical"></div>
    </div>



    <div id="posts-app-container" class="span8">
        <div id="election_summary" class="summary"><?php echo Yii::t('aes','Found'); ?> <span id="total_elections">...</span> <?php echo Yii::t('aes','elections'); ?><a id="a_create_election" href="<?php echo Yii::app()->createUrl('election/create'); ?>">create new</a></div>

        <div id="election_list"></div>
        <div id="election_more"></div>
    </div>
    <div class="clearfix"></div>
</div>

    <script type="text/template" id="election-view">

        <div class="election-info row-fluid">

            <div class="pull-left">
                <div style="width: 96px; height: 96px; background-color: #000;" class="election-photo"><span></span><img class="elect_th" alt="<?php echo Yii::t('aes','Election'); ?>" <% if (have_pic) { %> src="/uploads/elections/<%= id %>.jpg" <% } %>></div>
            </div>

            <div class="election_block">
                <a href="<?php echo Yii::app()->createUrl('election/view'); ?>/<%= id %>"><%= name %></a><br>
                <span class="muted"><?php echo Yii::t('aes','Status'); ?>: </span><strong><%= text_status %></strong><br><br>
            </div>

        </div>

    </script>

    <script type="text/template" id="no-election-view">

        <p><?php echo Yii::t('aes','no election found'); ?></p>

    </script>

    <script type="text/template" id="search-view">

        <div class="input-prepend">
            <label for="elect_search"><?php echo Yii::t('aes','Title'); ?></label>
            <span class="add-on"><i class="icon-search"></i></span>
            <input type="text" id="elect_search" class="span3" maxlength="20" placeholder="<?php echo Yii::t('aes', 'Start typing part of the election&apos;s title'); ?>" name="elect_search" value="">
        </div>

        <label for="elect_status"><?php echo Yii::t('aes','Status'); ?></label>
        <?php echo CHtml::dropDownList('elect_status', '', array_merge( array(''=>Yii::t('aes','All')), AESHelper::arrTranslated(Election::$statuses)), array('id'=>'elect_status','class'=>'span2')); ?>

    </script>

    <script type="text/template" id="more-btn-tpl">
        <div class="row-fluid get-more">
            <div class="span12"><a id="a_more" href="#"><?php echo Yii::t('aes','More'); ?></a><span>Loading...</span></div>
        </div>
    </script>
