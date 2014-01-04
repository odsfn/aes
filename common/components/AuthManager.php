<?php

/**
 * Extends CDbAuthManager with extra functionality
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class AuthManager extends CDbAuthManager {
    
    /**
     * Returns names of all ancestors authItems of specified child
     * 
     * @param string $childItemName
     * @return array
     */
    public function getAncestors($childItemName) {
        $ancestors = $this->db->createCommand()
            ->select('parent')
            ->from($this->itemChildTable)
            ->where('child=:name', array(':name'=>$childItemName))
            ->queryColumn();
        
        if($ancestors && count($ancestors) > 0) {
            foreach ($ancestors as $ancestor) {
                $result = $this->getAncestors($ancestor);
                $ancestors = array_merge($ancestors, $result);
            }
        }
        
        return $ancestors;
    }
}
