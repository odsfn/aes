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
                'aes:views/ItemView.js',
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
            'commentsAdmin' => array('commentModeration',
                function($widget) {
                    return array_merge(array(
                        'targetId' => $widget->jsConstructorOptions['targetId'],
                        'targetType' => $widget->jsConstructorOptions['targetType']
                    ), is_array($widget->roleCheckParams) ? $widget->roleCheckParams : array());
                }
            )
        ),
     ),

    'RatesMarionetteWidget' => array(
        'widgetName' => 'RatesWidget',
        'requires' => array(
            'depends' => array('aes-common'),
            'js' => array(
                'aes:collections/FilterableCollection.js'
            )
        )
    ),
                    
    'PostsMarionetteWidget' => array(
        'widgetName' => 'PostsWidget',
        'requires' => array(
            'depends' => array('aes-common', 'loadmask'),
            'js' => array(
                'aes:collections/FeedCollection.js',
                'aes:views/ItemView.js',
                'aes:views/EditBoxView.js',
                'aes:views/EditableView.js',
                'aes:views/MoreView.js'
            )
        ),
        'dependentWidgets' => array(
            'RatesMarionetteWidget'
        )
    )
);
