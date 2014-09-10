<?php
/**
 * Profides listing for user's profiles
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
Yii::import('frontend.modules.userAccount.models.Profile');

class ProfileController extends RestController {
    
    public $nestedModels = array();

    public $acceptFilters = array(
        'plain' => 'name,election_id,ageFrom,ageTo,birth_place,gender,applyScopes'
    );
    
    public function getOutputFormatters() {
        return array(
            'birth_day' => array('Formatter', 'toTs')
        );
    }    
    
    public function doRestList() {
        
        $model = $this->getModel();
        
        $peopleSearch = new PeopleSearch();
        
        if($name = $this->plainFilter['name'])
            $peopleSearch->name = $name;
        
        if($ageFrom = $this->plainFilter['ageFrom'])
            $peopleSearch->ageFrom = $ageFrom;
        
        if($ageTo = $this->plainFilter['ageTo'])
            $peopleSearch->ageTo = $ageTo;
        
        if($birth_place = $this->plainFilter['birth_place'])
            $peopleSearch->birth_place = $birth_place;
        
        if($gender = $this->plainFilter['gender'])
            $peopleSearch->gender = $gender;
        
        if($scopes = $this->plainFilter['applyScopes']) 
        {
            $scopes = CJSON::decode($scopes);
            $peopleSearch->applyScopes = $scopes;
            
            if (array_key_exists('elector', $scopes)) {
                $this->nestedModels['elector'] = array(
                    'joinType' => 'INNER JOIN',
                    'condition' => 
                        'elector.election_id = ' 
                            . $scopes['elector']['election_id']
                );
            }
            
            if(array_key_exists('inVoterGroup', $scopes)) {
                $this->nestedModels['voterGroupMember'] = array(
                    'joinType' => 'INNER JOIN',
                    'condition' => 
                        'voterGroupMember.voter_group_id = ' 
                            . $scopes['inVoterGroup']['voter_group_id']
                );
            }
        }
        
        $this->flushRestFilter('applyScopes', 'ageFrom', 'ageTo', 'name');
        
        $arProvCriteria = $peopleSearch->search()->criteria;
        
        $model->getDbCriteria()->mergeWith($arProvCriteria);
        
        $results = $model->with($this->nestedRelations)
            ->filter($this->restFilter)
            ->orderBy($this->restSort)
            ->limit($this->restLimit)->offset($this->restOffset)
            ->findAll();
        
        $forCount = $this->getModel();
        $forCount->getDbCriteria()->mergeWith($arProvCriteria);
        
        $this->outputHelper( 
                'Records Retrieved Successfully', 
                $results,
                $forCount->filter($this->restFilter)->with($this->nestedRelations)->count()
        );
    }

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('restList'),
                'users'=>array('*')
            ),
            array('deny',
                'actions'=>array('restCreate', 'restDelete', 'restUpdate'),
                'users'=>array('*')
            )
        );
    }

    protected function flushRestFilter($filterName)
    {
        $filterNames = func_get_args();
        foreach ($filterNames as $filterName){
            foreach ($this->restFilter as $index => $filter) {
                if($index == $filterName 
                    || ( isset($filter['property']) 
                            && $filter['property'] == $filterName) 
                ){
                    unset($this->restFilter[$index]);
                }
            }
        }
    }
}
