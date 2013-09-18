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
            $model=new Profile('search');
            $model->unsetAttributes();  // clear any default values
            if(isset($_GET['Profile']))
                $model->attributes=$_GET['Profile'];

            $this->render('index',array(
                'model'=>$model,
            ));
	}
}