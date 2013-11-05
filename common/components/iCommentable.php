<?php
/**
 * Each ActiveRecord which can be commented should implement this interface.
 * It is used by CommentController to perform role check configurations.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
interface iCommentable {
    
    public function doesUnassignedCanComment();
    
    public function doesUnassignedCanRead();
    
}
