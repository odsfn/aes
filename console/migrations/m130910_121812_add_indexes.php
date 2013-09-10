<?php

class m130910_121812_add_indexes extends EDbMigration
{
	public function up()
	{
            $this->createIndex('ix_post_placement_target_id_target_type', 'post_placement', 'target_id, target_type');
            $this->createIndex('ix_post_placement_placed_ts', 'post_placement', 'placed_ts');
	}

	public function down()
	{
            $this->dropIndex('ix_post_placement_target_id_target_type', 'post_placement');
            $this->dropIndex('ix_post_placement_placed_ts', 'post_placement');
	}
}