<?php
/** 
 * CommentsWidget renders several last comments, and information
 * about total count of comments to current target. It also displays buttons to 
 * view all comments. Pressing on this button will start comments aplication on
 * client side.
 * 
 * Note: 
 * - CommentsMarionetteWidget assets and templates should be registered before
 * this widget run
 */
class CommentsWidget extends CWidget
{
    public $targetId;
    
    public $targetType;
    
    public $commentsToShow = 3;
    /**
     * @var CActiveDataProvider
     */
    protected $_dataProvider;

    public function getViewPath($checkTheme = false)
    {
        $path = Yii::getPathOfAlias('frontend.widgets.views.comments');
        return $path;
    }    
    
    public function init()
    {
        Yii::app()->clientScript
            ->registerCssFile('/css/widgets/comments-widget.css')
            ->registerScript('commentsWidgetSimple', <<<'SCRIPT'
$('body').on('click', '.open-comments', function() {
    $('.comments-widget-simple img.loader').show();
    $('.comments-widget-simple .open-comments').hide();

    var targetId = $(this).data('targetId');
    var targetType = $(this).data('targetType');
    var comments = CommentsWidget.create({
        targetId: targetId,
        targetType: targetType,
        title: true
    });

    $('#comments-container').html(comments.render().el);
    comments.triggerMethod('show');
});
SCRIPT
            , CClientScript::POS_READY);
        
        parent::init();
    }
    
    public function run()
    {
        $totalCount = $this->getTotalCount();
        $comments = $this->getDataProvider()->getData();
        $canComment = $this->checkCanComment();
        
        $data = array(
            'targetId' => $this->targetId,
            'targetType' => $this->targetType,
            'totalCount' => $totalCount,
            'comments' => $comments,
            'canComment' => $canComment
        );
        
        $this->render('widget', $data);
    }
    
    
    protected function getCommentClass()
    {
        return $this->targetType . 'Comment';
    }
    
    protected function getDataProvider()
    {
        if(!$this->_dataProvider) {
            $commentClass = $this->commentClass;
            $model = $commentClass::model();
            $model->with(
                array(
                    'user' => array(
                        'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
                    ),
                    'positiveRatesCount',
                    'negativeRatesCount'
                )
            )->criteriaToTarget($this->targetId);
            
            $criteria = $model->getDbCriteria();
            $criteria->order = 't.created_ts DESC';
            
            $this->_dataProvider = new CActiveDataProvider($this->commentClass, array(
                'criteria' => $criteria,
                'pagination' => array(
                    'pageSize' => $this->commentsToShow
                )
            ));
        }
        
        return $this->_dataProvider;
    }
    
    protected function getTotalCount()
    {
        return $this->getDataProvider()->totalItemCount;
    }
    
    protected function getTarget()
    {
        $targetClass = $this->targetType;
        $target = new $targetClass;
        return $target->findByPk($this->targetId);
    }

    protected function checkCanComment()
    {
        $params = array();
        
        $target = $this->getTarget();
        
        if(! $target instanceof iCommentable ) {
            $canUnassignedComment = true;
            $canUnassignedRead = true;
        } else {
            $canUnassignedComment = $target->canUnassignedComment();
            $canUnassignedRead = $target->canUnassignedRead();
        }
        
        $params[lcfirst($this->targetType)] = $target;
        $params['target'] = $target;
        
        $disabledRoles = array();
        
        if(!$canUnassignedComment)
            $disabledRoles[] = 'commentor';
        
        if(!$canUnassignedRead)
            $disabledRoles[] = 'commentReader';
        
        if(!method_exists($target, 'checkUserInRole')) {
            $disabledRoles[] = 'commentModerator';
        }
        
        $params['disabledRoles'] = $disabledRoles;
        
        $result = Yii::app()->user->checkAccess('createComment', $params);
        return $result;
    }
}
