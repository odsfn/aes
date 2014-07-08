<?php

/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class VoteController extends RestController
{
    public $nestedModels = array(
        'profile' => array(
            'select' => 'user_id, first_name, last_name, photo, photo_thmbnl_64, birth_place, birth_day'
        )
    );

    public $acceptFilters = array(
        'plain' => 'election_id,user_id,candidate_id,with_profile,name,accepted_only,ageFrom,ageTo,birth_place,gender'
    );

    public $virtualAttrs = array(
        'profile'
    );

    public function getOutputFormatters()
    {
        return array(
            'date' => array('Formatter', 'toTs')
        );
    }

    public function doRestCreate($data)
    {
        $candidate = Candidate::model()->findByPk($data['candidate_id']);

        if (!$candidate)
            throw new Exception('Candidate was not found');

        $data['election_id'] = $candidate->election_id;

        parent::doRestCreate($data);
    }

    public function doRestList()
    {

        if (empty($this->plainFilter['election_id']))
            throw new Exception('Election id filter property missed');

        $election = Election::model()->findByPk($this->plainFilter['election_id']);

        if (!$election)
            throw new Exception('Election was not found');

        $criteria = new CDbCriteria(array(
            'condition' => 't.election_id = ' . $election->id
        ));

        if (isset($this->plainFilter['user_id']))
            $criteria->mergeWith(array('condition' => 't.user_id = ' . (int) $this->plainFilter['user_id']));
            
        if (isset($this->plainFilter['candidate_id']))
            $criteria->mergeWith(array('condition' => 't.candidate_id = ' . (int) $this->plainFilter['candidate_id']));

        if (isset($this->plainFilter['accepted_only']) && $this->plainFilter['accepted_only'])
            $criteria->addCondition('t.status = ' . Vote::STATUS_PASSED);

        $peopleSearch = new PeopleSearch();

        if ($name = $this->plainFilter['name'])
            $peopleSearch->name = $name;

        if ($ageFrom = $this->plainFilter['ageFrom'])
            $peopleSearch->ageFrom = $ageFrom;

        if ($ageTo = $this->plainFilter['ageTo'])
            $peopleSearch->ageTo = $ageTo;

        if ($birth_place = $this->plainFilter['birth_place'])
            $peopleSearch->birth_place = $birth_place;

        if ($gender = $this->plainFilter['gender'])
            $peopleSearch->gender = $gender;

        $arProvCriteria = $peopleSearch->search('profile')->criteria;
        if ($arProvCriteria) {
            $originalCriteria = clone $criteria;
            $criteria->mergeWith($arProvCriteria);
        }        
        
        $results = $this->getModel()
                ->with($this->nestedRelations)
                ->limit($this->restLimit)
                ->offset($this->restOffset)
                ->findAll($criteria);

        $totalCount = $this->getModel()
                ->with($this->nestedRelations)
                ->count($criteria);

        $extraData = $totalCount;

        if (isset($this->plainFilter['candidate_id'])) {
            
            if(isset($originalCriteria)) {
                $criteria = $originalCriteria;
            }
            
            $acceptedCountCritetia = clone $criteria;
            
            $acceptedCount = $this->getModel()
                        ->with($this->nestedRelations)
                        ->count($acceptedCountCritetia->addCondition('t.status = ' . Vote::STATUS_PASSED));
            
            $extraData = array(
                'totalCount' => $totalCount,
                'acceptedCount' => $acceptedCount,
//                'declinedCount' => $this->getModel()
//                        ->with($this->nestedRelations)
//                        ->count($criteria->addCondition('t.status = ' . Vote::STATUS_DECLINED . ' OR t.status = ' . Vote::STATUS_REVOKED))
            );
        }

        $this->outputHelper(
                'Records Retrieved Successfully', $results, $extraData
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('restCreate', 'restUpdate', 'restDelete'),
                'users' => array('@'),
                'expression' => array($this, 'checkAccess')
            ),
            array('deny',
                'actions' => array('restCreate', 'restDelete', 'restUpdate'),
                'users' => array('*')
            )
        );
    }

    public function checkAccess()
    {

        Yii::app()->authManager->defaultRoles = array_merge(
                Yii::app()->authManager->defaultRoles, array('election_updateVoteStatus')
        );

        $data = $this->data();

        if (!empty($_GET['id'])) {

            $id = $_GET['id'];
            $model = $this->loadOneModel((int) $id);

            if (!$model)
                throw new Exception('Vote with id = ' . $id . ' was not found');

            $candidate = $model->candidate;
            $election = $candidate->election;
        } else {
            $candidate = Candidate::model()->findByPk($data['candidate_id']);
            $election = $candidate->election;
        }

        if (!$candidate)
            throw new Exception('Related Candidate was not found');

        if (!$election)
            throw new Exception('Related Election was not found');

        $params['election'] = $election;
        $params['candidate'] = $candidate;
        if ($model) {
            $params['vote'] = $model;

            if (isset($data['status']))
                $params['status'] = $data['status'];
        }

        if ($this->action->id == 'restCreate' && Yii::app()->user->checkAccess('election_createVote', $params))
            return true;

        if ($this->action->id == 'restDelete' && Yii::app()->user->checkAccess('election_deleteVote', $params))
            return true;

        if ($this->action->id == 'restUpdate' && Yii::app()->user->checkAccess('election_updateVoteStatus', $params))
            return true;

        return false;
    }

}
