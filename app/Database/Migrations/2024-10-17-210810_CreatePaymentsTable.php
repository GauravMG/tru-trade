<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'paymentId' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'ghlOpportunityId' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'month' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',  // 10 total digits, 2 decimal places
                'null' => false,
            ],
            'paymentStatus' => [
                'type' => 'ENUM',
                'constraint' => ['paid', 'unpaid'],
                'default' => 'unpaid',
                'null' => false,
            ],
            'paymentMadeOn' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey('paymentId', true);

        // Add foreign key
        $this->forge->addForeignKey('ghlOpportunityId', 'ghl_opportunities', 'ghlOpportunityId', 'CASCADE', 'CASCADE');

        // Create the table
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropForeignKey('payments', 'payments_ghlOpportunityId_foreign');

        // Drop the table if it exists
        $this->forge->dropTable('payments');
    }
}
