<?php

class TypesController extends CController
{
    public function actionGetFormAttrs($type)
    {
        $ident = new PersonIdentifier;
        $ident->type = $type;
        
        $this->renderPartial('/personIdentifiers/_form', array(
            'model' => $ident
        ));
    }
}