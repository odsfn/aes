<?php

class TypesController extends CController
{
    public function actionGetFormAttrs()
    {
        $type = $_POST['type'];
        $ident = new PersonIdentifier;
        $ident->type = $type;
        
//        $this->renderPartial('/personIdentifiers/_form', array(
//            'model' => $ident
//        ));
        $this->widget('personIdentifier.widgets.IdentifierInput', array('identifier' => $ident, 'ajax'=>true));
    }
}