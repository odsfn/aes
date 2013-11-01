<?php
/**
 * Rest controller for almost all types of comments ( except comments to the posts
 * they are processed by PostController )
 * 
 * If you want to provide new rateable entity in the system you should follow such steps:
 * 1. Create table for rates using CommentableDbManagerHelper in the migration script
 * 2. Create models for new tables extending them from Rate model.
 * 3. Fix the urlManager route in the frontend/config/frontend.php to map new entities to the existing
 *    rate rest controller (frontend/modules/api/controllers/RateController.php)
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class RateController extends RestController {
    
    public $nestedModels = array();

    public function getOutputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'toTs')
        );
    }

    public function getInputFormatters() {
        return array(
            'created_ts' => array('Formatter', 'fromTs')
        );
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
