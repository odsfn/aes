<?php

/*
 * Base for all REST controllers
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class RestController extends ERestController {
    
    /**
     * Attributes which exists only on client side. Thay can't be saved as models'
     * properties, so they should be removed, before model saving start.
     * @var array 
     */
    public $virtualAttrs = array();
    
    /**
     * Model's property name which contains value of the owner. Used during default
     * access filters run.
     * @var string 
     */
    public $modelsUserIdAttr = 'user_id';
    
    /**
     * Describes which filters to accept. Allowable format:
     * 
     *  array('plain'=>'a,b,c', 'model'=>'d,e')
     * 
     * Filters described in 'plain' key will be copied to $this->plainFilters as 
     * key - value pairs. They are usefull when we override rest get methods.
     * 
     * Filters described in 'model' will be formated to ERestController filter format. 
     * Filters that are not present in 'model' will be removed from $this->restFilter.
     * 
     * If this property will be empty then all filters will be accepted and converted
     * to the ERestController filter format 
     * 
     * ERestController filters will be applied to the method filter()
     * 
     * @var array 
     */
    public $acceptFilters = array();
    
    protected $plainFilter = array();

    protected $convertRestFilters = false;
    
    public function beforeAction($event) {
        $result = parent::beforeAction($event);
        
        //decode extjs filters format
        if(is_string($this->restFilter) && isset($_GET['extjs'])) {
            $this->restFilter = CJSON::decode($this->restFilter);
        }
        
        if(ArrayHelper::isAssoc($this->restFilter)) {   //Conversion needed
            
            if(!empty($this->acceptFilters['plain'])) {
                $plainKeys = AESHelper::explode($this->acceptFilters['plain']);
                
                foreach ($plainKeys as $key) {
                    $this->plainFilter[$key] = $this->restFilter[$key];
                }
            }else{  //copy all to plainFilter
                foreach ($this->restFilter as $key => $value) {
                    $this->plainFilter[$key] = $value;
                }          
            }
            
            if(!empty($this->acceptFilters['model'])) {
                $acceptableKeys = AESHelper::explode($this->acceptFilters['model']);
                
                foreach ($this->restFilter as $key => $value) {
                    if(!in_array($key, $acceptableKeys))
                        unset($this->restFilter[$key]);
                    elseif ($value == '') {
                        $this->restFilter[$key] = null;
                    }
                }
            }
            
            if($this->convertRestFilters)
            {
                //Convert filters to acceptable format
                $convertedFilter = array();

                foreach ($this->restFilter as $filterName => $value) {
                    
                    if (!is_array($value) || !key_exists('property', $value) && !key_exists('value', $value)) {
                        $convertedFilter[] = array('property' => $filterName, 'value' => $value);    
                    } else {
                        $convertedFilter[] = $value;
                    }
                }

                $this->restFilter = $convertedFilter;
            }
            
        } else {                                        //Filters are in acceptable format
            
            $plainKeys = array();
            $filterableModelKeys = array();
            
            if(!empty($this->acceptFilters['plain'])) {
                $plainKeys = AESHelper::explode($this->acceptFilters['plain']);
            }
            
            if(!empty($this->acceptFilters['model'])) {
                $filterableModelKeys = AESHelper::explode($this->acceptFilters['model']);
            }
            
            $checkPlain = (bool)count($plainKeys);
            $checkModel = (bool)count($filterableModelKeys);
            
            if($checkModel || $checkPlain) {
                foreach ($this->restFilter as $index => $filter ) {
                
                    $filterKey = $filter['property'];
                    
                    if($checkPlain && in_array($filterKey, $plainKeys)) {
                        $this->plainFilter[$filterKey] = $filter['value'];
                    }
                        
                    if($checkModel && !in_array($filterKey, $filterableModelKeys)){
                        unset($this->restFilter[$index]);
                    }
                    
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Override to ignore any access restrictions. 
     * @TODO: Implement it for security needs
     */
    public function filterRestAccessRules($c) {
        Yii::app()->clientScript->reset(); //Remove any scripts registered by Controller Class
        $c->run();
    }
    
    /**
     * This output helper has been overriden for compability with Backbone.parse method
     * 
     * @param int|array $extraData If int - will be considered as totalCount, if array will be merged with data
     */
    public function outputHelper($message, $results, $extraData=0, $model= 'models' )
    {
            if(is_null($model))
                    $model = lcfirst(get_class($this->model));
            else
                    $model = lcfirst($model);	

            $data = array($model=>$this->allToArray($results));
            
            if(!is_array($extraData)) 
                $extraData = array('totalCount'=>$extraData);
            
            $data = array_merge($data, $extraData);
            
            $this->renderJson(array(
                    'success'=>true, 
                    'message'=>$message, 
                    'data'=> $data
            ));
    }    
    
    /**
     * Overrides method to provide formatting of specified attributes
     */
    public function allToArray($models) {
        return $this->formatOutput(parent::allToArray($models));
    }
    
    /**
     * Cleans array of models' attributes from attributes that are exist only on client side ( not in the DB )
     *  
     * @param array $data   Models data gotten from REST request
     * @param array $virtualAttrs   Attributes that should be removed
     * @return array
     */
    protected function removeVirtualAttributes($data, $virtualAttrs = null) {
         
        if(!$virtualAttrs) 
             $virtualAttrs = $this->virtualAttrs;
         
        foreach ($data as $key => $value) {
             
             if(empty($value) && $value !== '0' && $value !== 0) {
                 unset($data[$key]);
                 continue;
             }
             
             if(in_array($key, $virtualAttrs)) {
                 if(is_array($virtualAttrs[$key]) && is_array($data[$key])) {
                     $data[$key] = $this->removeVirtualAttributes($data[$key], $virtualAttrs[$key]);
                 }else{
                     unset($data[$key]);
                 }
             }
         }
         
         return $data;
    }
    
    protected function saveModel($model, $data) {
        $data = $this->removeVirtualAttributes($data);
        
        if(count($this->inputFormatters) > 0)
            $data = $this->formatInput($data);
        
        return parent::saveModel($model, $data);
    }
    
    public function doRestDelete($id) {
        $model = $this->loadOneModel($id);
        if (is_null($model)) {
            $this->HTTPStatus = $this->getHttpStatus(404);
            throw new CHttpException(404, 'Record Not Found');
        } else {
            if ($model->delete())
                $this->outputHelper('Record(s) Deleted', array($model), 1);
            else {
                $this->HTTPStatus = $this->getHttpStatus(406);
                throw new CHttpException(406, 'Could not delete model with ID: ' . $id);
            }
        }
    }
    
    public function _filters(){
	return array(
	    'accessControl'
	);
    }
    
    public function accessRules() {
	return array(
	    array('allow', 
		'actions' => array('restCreate'), 
		'users'=>array('@')
	    ),
            array('allow',
                'actions' => array('restDelete', 'restUpdate'),
                'expression' => array($this, 'doesUserCanControlModel')
            ),
	    array('deny', 
		'actions'=>array('restCreate', 'restDelete', 'restUpdate'),
		'users'=>array('*')
	    )
	);
    }
    
    public function doesUserCanControlModel($user, $rule) {
        $id = $_GET['id'];
        
        $model = $this->loadOneModel($id);
        
        if($this->isUserOwnsModel($model, $user))
            return true;
        
        return false;
    }
    
    /**
     * Checks whather the user has access to the model.
     * 
     * @param CActiveRecord $model
     * @param CWebUser $user
     * @return boolean
     */
    protected function isUserOwnsModel($model, $user = null) {
        if(!$user)
            $user = Yii::app()->user; 
        
        return $model->{$this->modelsUserIdAttr} == $user->id;
    }
    
    protected function getOutputFormatters() {
        return array();
    }
    
    protected function getInputFormatters() {
        return array();
    }
    
    /**
     * Formats models attributes by specified formatters before give them to 
     * the response
     * @param array $array  Array of models attributes converted to array
     */
    protected function formatOutput($array) {
        return ArrayHelper::format($array, $this->outputFormatters);
    }
    
    protected function formatInput($array) {
        return ArrayHelper::format($array, $this->inputFormatters);
    }

    /**
     * Attaches behaviour to the model, sets scenario
     * 
     * @param CModel $model
     */
    protected function prepareModel() {
        
        static $preparedModel;
        
        if(!$this->model)
            return;
        
        if($preparedModel && $preparedModel === $this->model)
            return;
        
        $this->_attachBehaviors($this->model);

        if(!is_null($this->restScenario)) {
            $this->model->scenario = $this->restScenario;
        }
        
        $preparedModel = $this->model;
    }

    public function createModel() {
        if ($this->model === null) 
        {
                $modelName = str_replace('Controller', '', get_class($this)); 
                $this->model = new $modelName;
        }
        
        return $this->model;
    }
    
    public function getModel() {
        $this->model = $this->createModel();
        $this->prepareModel();
        return $this->model;
    }

    /**
     * Fix of empty result for secondary read
     */ 
    protected $_data = false;
    
    public function data() 
    {
            $request = $this->requestReader->getContents();
            if ($request) {
                $this->_data = CJSON::decode($request);
            }
            
            return $this->_data;
    }
    
    public function doRestList() {
        
        $criteria = $this->processFilters();
        
        $results = $this->getResults($criteria);
        $resultsCount = $this->getResultsCount($criteria);
        
        $this->outputHelper( 
            'Records Retrieved Successfully',
            $results,
            $resultsCount
        );
        
    }
    
    protected function getResults($criteria) {
        return $this->getModel()
                ->with($this->nestedRelations)
                ->filter($this->restFilter)
                ->orderBy($this->restSort)
                ->limit($this->restLimit)
                ->offset($this->restOffset)
                ->findAll($criteria);
    }
    
    protected function getResultsCount($criteria) {
        return $this->getModel()
                ->with($this->nestedRelations)
                ->filter($this->restFilter)
                ->count($criteria);
    }
    
    /**
     * @return CDbCriteria
     */
    protected function processFilters() {
        $criteria = new CDbCriteria;
        
        foreach ($this->plainFilter as $filterName => $filterValue) {
            $method_name = 'onPlainFilter_' . $filterName;
            
            if(!method_exists($this, $method_name) || !$filterValue)
                continue;
            
            $this->$method_name($filterName, $filterValue, $criteria);
        }
        
        return $criteria;
    }
}
