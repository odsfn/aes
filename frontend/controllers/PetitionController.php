<?php

class PetitionController extends FrontController
{

   /**
    * Creates a new model.
    * If creation is successful, the browser will be redirected to the 'view' page.
    */
    public function actionCreate($mandateId)
    {
        $model=new Petition;
        $userId = Yii::app()->user->id; 
        
        $mandate = Mandate::model()->findByPk($mandateId);

        if(!$mandate)
            throw new CHttpException(404, "Specified mandate was not found");
        
        if(!$userId || !$mandate->acceptsPetitionFrom($userId))
            throw new CHttpException(403, 'Petition can be created by mandate\'s adherents only');

//        $this->performAjaxValidation($model);

        if(isset($_POST['Petition']))
        {
            $model->attributes=$_POST['Petition'];
            if ($model->save()) {
                Yii::app()->user->setFlash('success', Yii::t('petitions',"Petition was created"));
                $this->redirect(array('mandate/index', '#' => '/details/'. $mandateId .'#petitions-tab'));
            }
        }
 
        $model->creator_id = $userId;
        $model->mandate_id = $mandate->id;
        
        $this->render('create', array(
            'model'=>$model,
        ));
    }

    /**
     * Creates new petition by ajax request
     */
    public function actionAjaxCreate() {
        $mandateId = isset($_POST['mandateId']) ? (int)$_POST['mandateId'] : false;
        if(!$mandateId && isset($_POST['Petition']))
            $mandateId = $_POST['Petition']['mandate_id'];
        
        if(!$mandateId)
            throw new CHttpException(403, 'New petition can be created for existing mandate only');
        
        $model=new Petition;
        $userId = Yii::app()->user->id; 
        
        $mandate = Mandate::model()->findByPk($mandateId);

        if(!$mandate)
            throw new CHttpException(404, "Specified mandate was not found");
        
        if(!$userId || !$mandate->acceptsPetitionFrom($userId))
            throw new CHttpException(403, 'Petition can be created by mandate\'s adherents only');
        
        if(isset($_POST['Petition']))
        {
            $jsonResponse = array('success' => false, 'responseHtml' => '');
            
            $model->attributes=$_POST['Petition'];
            
            if ($model->save()) {
                $jsonResponse['success'] = true;
            }
        } else {
            $model->creator_id = $userId;
            $model->mandate_id = $mandate->id;
        }
        
        $formHtml = $this->renderPartial('_form', array(
            'model'=>$model,
            'forAjax'=>true
        ), true);
        
        if (!isset($jsonResponse)) {
            echo $formHtml;
        } else {
            $jsonResponse['responseHtml'] = $formHtml;
            echo CJSON::encode($jsonResponse);
        }
        
        Yii::app()->end();
    }


    /**
    * Updates a particular model.
    * If update is successful, the browser will be redirected to the 'view' page.
    * @param integer $id the ID of the model to be updated
    */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if(isset($_POST['Petition']))
        {
            $model->attributes=$_POST['Petition'];
            if($model->save())
                $this->redirect(array('view','id'=>$model->id));
        }

        $this->render('update',array(
            'model'=>$model,
        ));
    }

    /**
    * Returns the data model based on the primary key given in the GET variable.
    * If the data model is not found, an HTTP exception will be raised.
    * @param integer the ID of the model to be loaded
    */
    public function loadModel($id)
    {
        $model=Petition::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    /**
    * Performs the AJAX validation.
    * @param CModel the model to be validated
    */
    protected function performAjaxValidation($model)
    {
        if(isset($_POST['ajax']) && $_POST['ajax']==='petition-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
