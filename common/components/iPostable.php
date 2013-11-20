<?php
/**
 * Each ActiveRecord which can show posts on its page should implement this interface.
 * It is used by CommentController to perform role check configurations.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
interface iPostable {
    /**
     * @return bool 
     */
    public function canUnassignedPost();
    
    /**
     * @return bool
     */
    public function canUnassignedReadPost();
    
}
