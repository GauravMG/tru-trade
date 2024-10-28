<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGHLOpportunitiesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'ghlOpportunityId' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ghlPipelineId' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'ghlStageId' => [
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
            'assignedTo' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
            ],
            'source' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'lastStatusChangeAt' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'contactIdExternal' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'contactNameExternal' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'contactCompanyNameExternal' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'contactEmailExternal' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'contactPhoneExternal' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
            ],
            'contactTagsExternal' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'completeJson' => [
                'type' => 'TEXT',
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

        $this->forge->addPrimaryKey('ghlOpportunityId');
        $this->forge->addForeignKey('ghlPipelineId', 'ghl_pipelines', 'ghlPipelineId', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('ghlStageId', 'ghl_stages', 'ghlStageId', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ghl_opportunities');
    }

    public function down()
    {
        $this->forge->dropForeignKey('ghl_opportunities', 'ghl_opportunities_ghlPipelineId_foreign');
        $this->forge->dropForeignKey('ghl_opportunities', 'ghl_opportunities_ghlStageId_foreign');
        $this->forge->dropTable('ghl_opportunities');
    }
}
