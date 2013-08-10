<?php
/*
 * Base controller for user's pages.
 */
class SocialController extends FrontController {
    
    public function init() {
        $this->attachBehavior('breadcrumbs', new CrumbsBehaviour);
        $this->breadcrumbs->setEnabled(true);
        
        parent::init();
    }
    
}
