<?php

interface iObjectAuthAssignment {
    
    public function checkUserInRole($userId, $roleName);
    
    public function assignRoleToUser($userId, $roleName);
    
    public function revokeRoleFromUser($userId, $roleName);
}