<?php

class m131015_193450_add_election extends EDbMigration
{
	public function up()
	{
        $sql =
            <<<EOT
CREATE TABLE IF NOT EXISTS `election` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `target_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `mandate` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `quote` int(11) NOT NULL,
  `validity` int(11) NOT NULL,
  `cand_reg_type` tinyint(4) NOT NULL DEFAULT '0',
  `cand_reg_confirm` tinyint(4) NOT NULL DEFAULT '0',
  `voter_reg_type` tinyint(4) NOT NULL DEFAULT '0',
  `voter_reg_confirm` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

ALTER TABLE `election` ADD CONSTRAINT `election_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
EOT;
        ;
        $this->execute($sql);
        
        $this->addForeignKey('fk_election_target_id', 'election', 'target_id', 'target', 'target_id', 'CASCADE', 'NO ACTION');
    }

	public function down()
	{
            $this->dropTable('election');
	}

}