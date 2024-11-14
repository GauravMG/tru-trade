<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentsTabls extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'documentId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ghlOpportunityId' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'documentType' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'documentLink' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
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

        // Add the primary key
        $this->forge->addKey('documentId', true);

        $this->forge->addForeignKey('ghlOpportunityId', 'ghl_opportunities', 'ghlOpportunityId', 'CASCADE', 'CASCADE');

        // Create the table
        $this->forge->createTable('documents');
    }

    public function down()
    {
        $this->forge->dropForeignKey('documents', 'documents_ghlOpportunityId_foreign');
        
        // Drop the table if it exists
        $this->forge->dropTable('documents');
    }
}
