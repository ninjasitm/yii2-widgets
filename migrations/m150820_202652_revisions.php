<?php

use yii\db\Schema;
use yii\db\Migration;

class m150820_202652_revisions extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
		$tableSchema = \Yii::$app->db->getTableSchema('revisions');
		if($tableSchema)
			return true;
		$this->createTable('revisions', [
            'id' => 'pk',
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT NOW()',
            'parent_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'parent_type' => Schema::TYPE_STRING . '(32) NOT NULL',
        ]);
		
	    $this->addForeignKey('fk_revisions_author', '{{%revisions}}', 'author_id', '{{%user}}', 'id', 'CASCADE', 'RESTRICT');
    }

    public function safeDown()
    {
        echo "m150820_202652_revisions cannot be reverted.\n";

        return true;
    }
}
