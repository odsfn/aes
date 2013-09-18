<?php
/**
 *
 * PeopleController class
 *
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class PeopleController extends FrontController
{
	public function actionIndex()
	{   
            $model=new PeopleSearch('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['PeopleSearch']))
                $model->attributes=$_GET['PeopleSearch'];

            $this->render('index',array(
                'model'=>$model,
            ));
	}
}