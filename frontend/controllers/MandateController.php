<?php
/**
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class MandateController extends FrontController
{
    public function actionIndex()
    {
        $this->render('index');
    }
    
    public function actionCheckPetitionAcceptence($mandateId) {
        $mandate = $this->loadModel($mandateId);
        
        $result = false;
        
        $userId = Yii::app()->user->id;
        
        if($userId && $mandate->acceptsPetitionFrom($userId))
            $result = true;
        
        echo CJSON::encode(array('result' => $result));
        Yii::app()->end();
    }
    
    public function loadModel($id)
    {
        $model=Mandate::model()->findByPk($id);
        
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        
        return $model;
    }    
}
