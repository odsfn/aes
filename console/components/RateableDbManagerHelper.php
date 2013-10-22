<?php
/**
 * Helper class for creation tables for rateable entities in migrations
 * 
 * @author Vasiliy Pedak <truvazia@gmail.com>
 */
class RateableDbManagerHelper {
    
    /**
     * Every rateable entity should have separate table for it's rates.
     * This method will create it.
     * 
     * @param string $rateableTableName  Name of the table with entities that you gonna make rateable
     * @param CDbMigration $migration
     */
    public static function createTables($rateableTableName, $migration) {
        
        $newTable = $rateableTableName . '_rate';
        
        $migration->createTable($newTable, array(
            'id' => 'pk',
            'user_id' => 'int(11) NOT NULL',
            'target_id' => 'int(11) NOT NULL',
            'created_ts' => 'timestamp NOT NULL DEFAULT "0000-00-00"',
            'score' => 'tinyint NOT NULL'
        ));

        $migration->addForeignKey('fk_' . $newTable . '_user_id', $newTable, 'user_id', 'user_profile', 'user_id', 'CASCADE', 'NO ACTION');
        $migration->addForeignKey('fk_' . $newTable . '_target_id', $newTable, 'target_id', $rateableTableName, 'id', 'CASCADE', 'NO ACTION');       
    }
    
    /**
     * @param string $commentableTableName
     * @param CDbMigration $migration
     */    
    public static function dropTables($rateableTableName, $migration) {
        $migration->dropTable($rateableTableName . '_rate');
    }
}
