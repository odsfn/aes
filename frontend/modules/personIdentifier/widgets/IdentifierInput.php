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
        
        $this->initPopover();
        $output = $this->renderPartial($view, array('model'=> $this->identifier, 'form' => $this->form, 'fieldsView' => $fieldsView), true, $this->ajax);
        
        if ($this->ajax)
            $output = preg_replace('/(<form(.*)>)|(<\/form>)/Um', '', $output);
        
        echo $output;
        
        if (!$this->ajax)
            echo '</div>';
    }
    
    protected function initPopover()
    {
        $conf = $this->identifier->getTypeConfig();
        if (!isset($conf['form']['popover'])) 
            return;
        
        $popoverConf = array();
        
        foreach ($conf['form']['popover'] as $attrs => $conf) {
            foreach (AESHelper::explode($attrs) as $attr) {
                if (!isset($conf['title']))
                    $conf['title'] = 'Document example';
                
                $popoverConf[] = array_merge(array(
                    'attr' => $attr
                ), $conf);
            }
        }
        
        Yii::app()->clientScript->registerScriptFile(
            Yii::app()->assetManager->publish(Yii::getPathOfAlias('personIdentifier.assets') . '/popupdetails.js')
        );
        
        foreach ($popoverConf as $index => $conf) {
            if (!isset($conf['img'])) {
                unset($popoverConf[$index]);
                continue;
            }
            
            $imgPath = Yii::getPathOfAlias(Yii::app()->getModule('personIdentifier')->customIdentifiersPath . '.' . $this->identifier->type ) . '/' . $conf['img'];
            
            if (!file_exists($imgPath)) {
                unset($popoverConf[$index]);
                continue;
            }
            
            $imgUrl = Yii::app()->assetManager->publish($imgPath);
            
            $popoverConf[$index]['img'] = $imgUrl;
        }
        
        Yii::app()->clientScript->registerScript(uniqid(), 'PersonIdentifier.initPopups(' . CJavaScript::encode($popoverConf) . ');');
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
