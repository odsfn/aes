<?php
/**
 * Rest controller for almost all types of comments ( except comments to the posts
 * they are processed by PostController )
 * 
 * If you want to provide new rateable entity in the system you should follow such steps:
 * 1. Create table for rates using CommentableDbManagerHelper in the migration script
 * 2. Create model for new table extending them from Rate model.
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class RateController extends RestController {
    
    public $nestedModels = array();

    protected $convertRestFilters = true;
    
    public $acceptFilters = array('plain' => 'name,with_profile', 'model' => 'target_id');
    
    public function getOutputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'toTs'),
            'profile.birth_day' => array('Formatter', 'toTs')
        );
    }

    public function getInputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'fromTs')
        );
    }
    
    public function onPlainFilter_with_profile($filterName, $filterValue, $criteria) {        
        $this->nestedModels = array(
            'profile' => array(
                'select' => 'profile.user_id, profile.first_name, profile.last_name, profile.birth_day, profile.birth_place'
            )
        );
    }
    
    public function onPlainFilter_name($filterName, $filterValue, $criteria) {        
        $criteria->mergeWith(PeopleSearch::getCriteriaFindByName($filterValue, 'profile'));
    }
    
    public function getModel() 
    {
        if(!$this->model) {
            $model = $_GET['target_type'] . 'Rate';

            unset($_GET['target_type']);

            try {
                $this->model = new $model;
            } catch (Exception $e) {
                throw new Exception('You should provide existing rateable entity name in "target_type" request attribute', null, $e);
            }
        }
        
        return parent::getModel();
    }
    
    public function doRestCreate($data) {
        $data['user_id'] = Yii::app()->user->id;
        parent::doRestCreate($data);
    }
}
