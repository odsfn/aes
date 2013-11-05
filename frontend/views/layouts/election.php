<?php
$this->beginContent('//layouts/column1');
Yii::app()->bootstrap->registerAssetCss('bootstrap-box.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->getBaseUrl(true) . '/css/user.css');
?>

    <div class="row-fluid">
        <div class="span10 offset1">

            <div class="bootstrap-widget" id="title">
                <div class="bootstrap-widget-header smooth">
                    <i class="icon-briefcase"></i>
                    <h3>
                        <?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                            'homeLink' => false,
                            'links'=> Yii::app()->controller->breadcrumbs
                        )); ?>
                    </h3>
                </div>
            </div>

            <div class="row-fluid">

                <div class="span3" id="column-left">

                    <div id="photo">
<!--                        --><?php /*$this->widget('application.widgets.UsersPhoto', array(
                            'user' => $this->profile,
                            'imgWidth' => 237,
                            'imgHeight'=> 300,
                            'containerWidth' => '20%',
                            'containerHeight' => '300px'
                        )); */?>
                    </div>

                    <div id="navigation">
                        <?php
                        $this->widget('bootstrap.widgets.TbMenu', array(
                            'type'=>'pills',
                            'stacked' => 'true',
                            'items' => array(
                                array('label'=> Yii::t('userPage', (false) ? 'My page' : 'Page'), 'url'=> array('/userPage/index')),
                                array('label'=> Yii::t('userPage', 'My messages'), 'url'=> array('/messaging/index'), 'visible' => false, 'itemOptions' => array('class'=>'messaging')),
                                array('label'=> Yii::t('userPage', 'Write message'), 'url'=> array('/messaging/index/chat_with/' . Yii::app()->user->id), 'visible' => (!Yii::app()->user->isGuest)),
                                array('label'=> Yii::t('userPage', (false) ? 'My votes' : 'Votes'), 'url'=> array('/userPage/votes')),
                                array('label'=> Yii::t('userPage', (false) ? 'My nominations' : 'Nominations'), 'url'=> array('/userPage/nominations')),
                                array('label'=> Yii::t('userPage', (false) ? 'My mandates' : 'Mandates'), 'url'=> array('/userPage/mandates')),
                                array('label'=> Yii::t('userPage', (false) ? 'My photos' : 'Photos'), 'url'=> array('/userPage/photos')),
                                array('label'=> Yii::t('userPage', (false) ? 'My videos' : 'Videos'), 'url'=> array('/userPage/videos')),
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