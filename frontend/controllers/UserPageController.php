<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserPageController extends EController {

    /**
     * Authenticated user's page
     */
    public function actionIndex() {
	$this->render('userPage');
    }
}
