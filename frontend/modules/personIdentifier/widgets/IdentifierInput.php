<?php

/** 
 * Renders personIdentifier form or fields
 */
class IdentifierInput extends CWidget
{
    public $identifier;
    
    public $form;
    
    public $ajax = false;


    public function run()
    {
        $view = 'personIdentifier.views.personIdentifiers._form';
        
        $fieldsView = $this->getIdentifierFieldsView($this->identifier);
        if (!$this->ajax)
            echo '<div id="identifier-input-container">';
        
        $output = $this->renderPartial($view, array('model'=> $this->identifier, 'form' => $this->form, 'fieldsView' => $fieldsView), true, $this->ajax);
        
        if ($this->ajax)
            $output = preg_replace('/(<form(.*)>)|(<\/form>)/Um', '', $output);
        
        echo $output;
        
        if (!$this->ajax)
            echo '</div>';
    }
    
    protected function getIdentifierFieldsView($identifier)
    {
        $path = Yii::app()->getModule('personIdentifier')->customIdentifiersPath . '.' . $identifier->type . '._formFields';
        $realpath = Yii::getPathOfAlias($path) . '.php';
        
        if (file_exists($realpath))
            return $path;
        else
            return 'personIdentifier.views.personIdentifiers._formFields';
    }
    
public function renderPartial($view,$data=null,$return=false,$processOutput=false)
{
    if(($viewFile=$this->getViewFile($view))!==false)
    {
        $output=$this->renderFile($viewFile,$data,true);
        if($processOutput)
            Yii::app()->getClientScript()->render($output);
        if($return)
            return $output;
        else
            echo $output;
    }
    else
        throw new CException(Yii::t('yii','{controller} cannot find the requested view "{view}".',
            array('{controller}'=>get_class($this), '{view}'=>$view)));
}    
}
