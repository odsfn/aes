<?php
/**
 * Rest controller for almost all types of comments ( except comments to the posts
 * they are processed by PostController )
 * 
 * If you want to provide new commentable entity in the system you should follow such steps:
 * 1. Create tables for comments using CommentableDbManagerHelper in the migration script
 * 2. Create models for new tables extending them from Comment model.
 * 3. Fix the urlManager route in the frontend/config/frontend.php to map new entities to the existing
 *    comment rest controller (frontend/modules/api/controllers/CommentController.php)
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class CommentController extends RestController {
    
    public $acceptableFilters = array(
        'plain' => 'target_id,target_type'
    );    
    
    public $nestedModels = array(
        
        'user' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64'
        ),
        
        'rates' => array('alias' => 'post_rate')
        
    );
        
    public $virtualAttrs = array(
            'user',
            'likes',
            'dislikes'
    );

    public function getOutputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'toTs'),
            'last_update_ts' => array('Formatter', 'toTs'),
            'rates.created_ts' => array('Formatter', 'toTs')
        );
    }

    public function getInputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'fromTs'),
            'last_update_ts' => array('Formatter', 'fromTs')
        );
    }
    
    public function getModel() 
    {
        if(!$this->model) {
            $model = $_GET['target_type'] . 'Comment';

            unset($_GET['target_type']);

            try {
                $this->model = new $model;
            } catch (Exception $e) {
                throw new Exception('You should provide existing model name in "target_type" request attribute', null, $e);
            }
        }
        
        return parent::getModel();
    }
    
    public function doRestList() {
        
        $criteria = $this->getModel();
                
        $criteria->with($this->nestedRelations)
                ->criteriaToTarget($this->plainFilter['target_id']);
        
        $countCriteria = clone $criteria;
        
        $totalCount = $countCriteria->count();
        
        $this->outputHelper( 
            'Records Retrieved Successfully',
            $criteria
                ->limit($this->restLimit)
                ->offset($this->restOffset)
                ->findAll(array('order' => 'created_ts ASC')),
            (int)$totalCount
        );
    }
    
    public function doRestCreate($data) {
        $data['user_id'] = Yii::app()->user->id;
        
        parent::doRestCreate($data);
    }
}