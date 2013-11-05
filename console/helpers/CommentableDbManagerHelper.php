<?php
/**
 * Helper class for creation tables for commantable entities in migrations
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class CommentableDbManagerHelper {
    
    /**
     * Every commentable entity should have separate table for it's comments.
     * This method will create it.
     * 
     * @param string $commentableTableName  Name of the table with entities that you gonna make commentable
     * @param CDbMigration $migration
     */
    public static function createTables($commentableTableName, $migration) {
        
        $newTableName = $commentableTableName . '_comment';
        
        $migration->createTable($newTableName, array(
            'id' => 'pk',
            'target_id'     => 'INT(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'content' => 'TEXT NOT NULL',
            'created_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"',
            'last_update_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"'
        ));

        $migration->addForeignKey('fk_' . $newTableName . '_user_id', $newTableName, 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
        $migration->addForeignKey('fk_' . $newTableName . '_target_id', $newTableName, 'target_id', $commentableTableName, 'id', 'CASCADE', 'NO ACTION');
        
        RateableDbManagerHelper::createTables('election_comment', $migration);
    }
    
    /**
     * @param string $commentableTableName
     * @param CDbMigration $migration
     */
    public static function dropTables($commentableTableName, $migration) {
        
        RateableDbManagerHelper::dropTables($commentableTableName . '_comment', $migration);
        
        $migration->dropTable($commentableTableName . '_comment');
    }
}
