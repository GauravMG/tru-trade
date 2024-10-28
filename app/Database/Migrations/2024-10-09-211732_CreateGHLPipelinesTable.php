<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGHLPipelinesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ghlPipelineId' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'externalId' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'externalLocationId' => [
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

        $this->forge->addPrimaryKey('ghlPipelineId');
        $this->forge->createTable('ghl_pipelines');
    }

    public function down()
    {
        $this->forge->dropTable('ghl_pipelines');
    }
}
