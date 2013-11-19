<?php
/**
 * Contains widgets description. Will be included in frontend.php config file
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
return array(
    /**
     * Displays Comments Widget to the specified commentable entity.
     * Allows to add, edit, delete comments to owner or administrator
     * 
     * @param int $targetId Required id of the commentable entity
     * @param string $targetType Required name of the commentable entity ( Election ... )
     * 
     * See frontend/views/sandbox/commentsWidget.php for examples
     */
    'CommentsMarionetteWidget' => array(
        'widgetName' => 'CommentsWidget',
        'requires' => array(
            'depends' => array('loadmask'),
            'js' => array(
                'aes:views/EditBoxView.js',
                'aes:views/EditableView.js',
                'aes:collections/FeedCollection.js',
                'aes:views/FeedCountView.js',
                'aes:views/MoreView.js'
             )
        ),
        'dependentWidgets' => array(
            'RatesMarionetteWidget'
        ),
        'checkForRoles' => array(                                        
            'commentsAdmin' => array('election_commentModerator', 
                function($widget) {
                    return array(
                        'targetId' => $widget->jsConstructorOptions['targetId'],
                        'targetType' => $widget->jsConstructorOptions['targetType']
                    );
                }
            )
        ),
     ),

    'RatesMarionetteWidget' => array(
        'widgetName' => 'RatesWidget',
        'requires' => array(
            'depends' => array('aes-common')
        )
    ),
                    
    'PostsMarionetteWidget' => array(
        'widgetName' => 'PostsWidget',
        'requires' => array(
            'depends' => array('aes-common', 'loadmask'),
            'js' => array(
//                'models/Post.js',
//                'models/PostRate.js',
                'aes:collections/FeedCollection.js',
//                'collections/Posts.js',
//                'collections/Comments.js',
//                'views/PostsTitleView.js',
                'aes:views/EditBoxView.js',
//                'views/PostsView.js',
//                'views/CommentsView.js',
                'aes:views/EditableView.js',
                'aes:views/MoreView.js'
            )
        ),
        'dependentWidgets' => array(
            'RatesMarionetteWidget'
        ),
        'checkForRoles' => array(
            'postsAdmin' => function($params) {
                return (Yii::app()->user->id && Yii::app()->user->id == $params['widget']->jsConstructorOptions['userPageId']); 
            }
        )
    )
);
