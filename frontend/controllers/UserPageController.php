<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
class UserPageController extends SocialController {

    /**
     * Authenticated user's page
     */
    public function actionIndex() {
        $this->render('userPage');
    }
}
