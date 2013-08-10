<?php
/*
 * Adds breadcrumbs management functionality to the component
 */
class CrumbsBehaviour extends CBehavior {
    
    protected $breadcrumbs = array();

    /**
     * Adds breadcrumbs
     * @param mixed $label
     * @param string $url
     */
    public function add($label, $url = '') {
        
        if(is_array($label)) {
            $insert = array(); 
            $adding = array_reverse($label);
            
            foreach ($adding as $crumb) {
                
                if(is_array($crumb)) {
                    if(isset($crumb['label'])) {
                        $insert = array_merge($insert, array($crumb['label'] => isset($crumb['url']) ? $crumb['url'] : ''));
                    } else {
                        $insert = array_merge($insert, array($crumb[0] => isset($crumb[1]) ? $crumb[1] : ''));
                    }
                } else {
                    $insert = array_merge($insert, array($crumb => ''));
                }
                
            }
            
        }else{
            $insert = array($label => $url);
        }
        
        $this->breadcrumbs = array_merge($this->breadcrumbs, $insert);
    }

    public function getBreadcrumbs() {
        return array_reverse($this->breadcrumbs);
    }
}
