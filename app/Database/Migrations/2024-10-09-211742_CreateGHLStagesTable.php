<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGHLStagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ghlStageId' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ghlPipelineId' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'externalId' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'createdAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updatedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deletedAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('ghlStageId');
        $this->forge->addForeignKey('ghlPipelineId', 'ghl_pipelines', 'ghlPipelineId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ghl_stages');
    }

    public function down()
    {
        $this->forge->dropForeignKey('ghl_stages', 'ghl_stages_ghlPipelineId_foreign');
        $this->forge->dropTable('ghl_stages');
    }
}
