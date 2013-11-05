<?php

/**
 * Base for all commentable objects ( like election, order and so on )
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
abstract class Commentable extends CActiveRecord implements iCommentable {
    
    public function doesUnassignedCanComment() {
        return true;
    }
    
    public function doesUnassignedCanRead() {
        return true;
    }
    
    public function getObjectAuthAssignmentBehaviorName() {
        return 'ObjectAuthAssignmentBehavior';
    }
    
    public function behaviors() {
        return array(
            $this->getObjectAuthAssignmentBehaviorName()
        );
    }
}
