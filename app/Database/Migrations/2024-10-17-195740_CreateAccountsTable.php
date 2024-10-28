<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'accountId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ghlOpportunityId' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'systemType' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'accountNumber' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'accountSize' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'accountCost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',  // 10 total digits, 2 decimal places
                'null' => false,
            ],
            'multiplier' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',  // 10 total digits, 2 decimal places
                'null' => false,
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
        $this->forge->addKey('accountId', true);

        $this->forge->addForeignKey('ghlOpportunityId', 'ghl_opportunities', 'ghlOpportunityId', 'CASCADE', 'CASCADE');

        // Create the table
        $this->forge->createTable('accounts');
    }

    public function down()
    {
        $this->forge->dropForeignKey('accounts', 'accounts_ghlOpportunityId_foreign');
        
        // Drop the table if it exists
        $this->forge->dropTable('accounts');
    }
}
